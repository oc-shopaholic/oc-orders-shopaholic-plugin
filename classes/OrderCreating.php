<?php namespace Lovata\OrdersShopaholic\Classes;

use DB;
use Event;
use Kharanenka\Helper\Result;
use Lang;
use Lovata\Buddies\Components\Buddies;
use Lovata\Buddies\Facades\BuddiesAuth;
use Lovata\OrdersShopaholic\Plugin;
use Lovata\OrdersShopaholic\Models\Order;
use Lovata\OrdersShopaholic\Models\Phone;
use Lovata\OrdersShopaholic\Models\Status;
use Lovata\Buddies\Models\User;
use Lovata\Shopaholic\Models\Offer;
use Lovata\Shopaholic\Models\Settings;
use System\Classes\PluginManager;
use Validator;
use Carbon\Carbon;

/**
 * Class OrderCreating
 * @package Lovata\OrdersShopaholic\Classes
 * @author Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class OrderCreating
{
    const CACHE_TAG = 'shopaholic-order';

    /** @var OrderCreating */
    protected static $obThis = null;
    
    /** @var User */
    protected $obUser;
    
    //Settings
    protected $bCheckQuantityOnOrder = false;
    protected $bDecrementQuantityAfterOrder = false;
    protected $bCreateNewUser = true;
    protected $sUserKeyField;

    protected function __construct()
    {
        // Get order behavior flags from settings
        $this->bCheckQuantityOnOrder = Settings::getValue('check_quantity_on_order');
        $this->bDecrementQuantityAfterOrder = Settings::getValue('decrement_quantity_after_order');
        $this->bCreateNewUser = Settings::getValue('create_new_user');

        $sUserKeyField = Settings::getValue('user_key_field');
        if(!empty($sUserKeyField)){
            $this->sUserKeyField = $sUserKeyField;
        } else {
            $this->sUserKeyField = Plugin::FIND_USER_BY_EMAIL;
        }

        //Check auth user
        if(BuddiesAuth::check()) {
            $this->obUser = BuddiesAuth::getUser();
        }
    }

    /**
     * @return OrderCreating
     */
    protected static function getInstance() {
        
        if(empty(self::$obThis)) {
            self::$obThis = new OrderCreating();
        }
        
        return self::$obThis;
    }

    /**
     * Create new order
     * @param $arOrderData
     * @return void
     */
    public static function create($arOrderData)
    {
        if(empty($arOrderData)) {

            $arErrorData = [
                'message'   => Lang::get('lovata.toolbox::lang.message.e_not_correct_request'),
                'field'     => null,
            ];
            
            Result::setFalse($arErrorData);
            return;
        }

        if(!isset($arOrderData['item']) || empty($arOrderData['item'])) {

            $arErrorData = [
                'message'   => Lang::get('lovata.ordersshopaholic::lang.message.empty_cart'),
                'field'     => null,
            ];

            Result::setFalse($arErrorData);
            return;
        }
        
        $obThis = self::getInstance();
        
        //Find or create new user
        if(empty($obThis->obUser) && $obThis->bCreateNewUser) {
            $obThis->findOrCreateUser($arOrderData);
        }

        if(!empty($obThis->obUser)) {
            $arOrderData['user_id'] = $obThis->obUser->id;
        }

        $arOrderData['status_id'] = Status::getStatusIDByCode(Status::STATUS_NEW);
        
        //Begin transaction
        DB::beginTransaction();

        $obOrder = Order::create($arOrderData);
        
        foreach($arOrderData['item'] as $arItemData) {
            
            if(empty($arItemData) || !isset($arItemData['offer_id']) || empty($arItemData['offer_id'])) {
                continue;
            }
            
            if(!isset($arItemData['quantity']) || $arItemData['quantity'] < 1) {
                continue;
            }
            
            $iOfferID = $arItemData['offer_id'];
            $iQuantity = $arItemData['quantity'];
            
            //Get offer object and check "active"
            /** @var Offer $obOffer */
            $obOffer = Offer::active()->find($iOfferID);
            if(empty($obOffer)) {

                $arErrorData = [
                    'message'   => Lang::get('lovata.ordersshopaholic::lang.message.offer_not_found'),
                    'field'     => null,
                    'offer_id'  => $iOfferID,
                ];
                
                Result::setFalse($arErrorData);
                break;
            }

            //Check quantity
            if($obThis->bCheckQuantityOnOrder && $obOffer->quantity < $iQuantity) {

                $arErrorData = [
                    'message'   => Lang::get('lovata.ordersshopaholic::lang.message.insufficient_amount'),
                    'field'     => null,
                    'offer_id'  => $iOfferID,
                ];
                
                Result::setFalse($arErrorData);
                break;
            }

            //Decrement offer quantity
            if($obThis->bDecrementQuantityAfterOrder) {
                try {
                    DB::table('lovata_shopaholic_offers')->where('id', $obOffer->id)->decrement('quantity', $iQuantity);
                } catch (\Exception $e) {
                    
                    $arErrorData = [
                        'message'   => Lang::get('lovata.ordersshopaholic::lang.message.insufficient_amount'),
                        'field'     => null,
                        'offer_id'  => $iOfferID,
                    ];

                    Result::setFalse($arErrorData);
                    break;
                }
            }
            
            $arPivotData = [
                'price'         => $obOffer->getPriceValue(),
                'old_price'     => $obOffer->getOldPriceValue(),
                'quantity'      => $iQuantity,
                'code'          => $obOffer->code,
            ];

            if(PluginManager::instance()->hasPlugin('Lovata.CustomShopaholic')) {
                \Lovata\CustomShopaholic\Classes\OrderExtend::addOfferOrderPivotData($arItemData, $arPivotData);
            }
            
            //Attach offer to order
            $obOrder->offer()->attach($obOffer->id, $arPivotData);
        }
        
        if(!Result::flag()) {
            DB::rollBack();
            return;
        }

        DB::commit();
        $obOrder->save();

        Event::fire('shopaholic.order.created', $obOrder);
        
        //TODO: Логика для отправки письма, необходимо доработать
//        $sEmailSend = Settings::getValue('order_create_email');
//        if(!empty($sEmailSend)) {
//
//            $arOrderData = [
//                'number' => $this->obOrder->order_number,
//            ];
//            
//            Mail::queue('lovata.order:admin.create', $arOrderData, function($obMessage) use ($sEmailSend) {
//                $obMessage->to($sEmailSend);
//            });
//        }
        
        Result::setTrue($obOrder);
    }

    /**
     * Find user by request data or create new user
     * @param array $arOrderData
     */
    private function findOrCreateUser($arOrderData)
    {
        if(!empty($this->obUser) || empty($arOrderData)) {
            return;
        }
        
        if($this->sUserKeyField == Plugin::FIND_USER_BY_PHONE) {
            $this->findUserByPhone($arOrderData);
        }
        
        if($this->sUserKeyField == Plugin::FIND_USER_BY_EMAIL) {
            $this->findUserByEmail($arOrderData);
        }

        if(!empty($this->obUser) || !$this->bCreateNewUser) {
            return;
        }
        
        $this->createUser($arOrderData);
    }

    /**
     * Find user by phone
     * @param $arOrderData
     * @return void
     */
    private function findUserByPhone($arOrderData) {
        
        if(empty($arOrderData) || !isset($arOrderData[Plugin::FIND_USER_BY_PHONE]) || empty($arOrderData[Plugin::FIND_USER_BY_PHONE])) {
            return;
        }

        //Find user by phone
        $sPhone = $arOrderData[Plugin::FIND_USER_BY_PHONE];
        $this->obUser = User::whereHas('order_phones', function ($obQuery) use ($sPhone) {
            /** @var Phone $obQuery */
            $obQuery->phone($sPhone);
        })->first();
        
        if(!empty($this->obUser)) {
            return;
        }
        
        //Get user email
        if(!isset($arOrderData[Plugin::FIND_USER_BY_EMAIL]) || empty($arOrderData[Plugin::FIND_USER_BY_EMAIL])) {
            return;
        }

        //Find user by email
        $sEmail = $arOrderData[Plugin::FIND_USER_BY_EMAIL];
        $this->obUser = User::getByEmail($sEmail)->first();
        
        //If user was found, create new phone for user
        if(!empty($this->obUser)) {
            Phone::create([
                'user_id' => $this->obUser->id,
                'phone' => $sPhone,
            ]);
        }
    }

    /**
     * Find user by email
     * @param $arOrderData
     * @return void
     */
    private function findUserByEmail($arOrderData) {

        if(empty($arOrderData) || !isset($arOrderData[Plugin::FIND_USER_BY_EMAIL]) || empty($arOrderData[Plugin::FIND_USER_BY_EMAIL])) {
            return;
        }

        //Find user by email
        $sEmail = $arOrderData[Plugin::FIND_USER_BY_EMAIL];
        $this->obUser = User::getByEmail($sEmail)->first();

        if(empty($this->obUser)) {
            return;
        }

        if(!isset($arOrderData[Plugin::FIND_USER_BY_PHONE]) || empty($arOrderData[Plugin::FIND_USER_BY_PHONE])) {
            return;
        }

        //Find user by phone
        $sPhone = $arOrderData[Plugin::FIND_USER_BY_PHONE];
        $obUser = User::whereHas('order_phones', function ($obQuery) use ($sPhone) {
            /** @var Phone $obQuery */
            $obQuery->phone($sPhone);
        })->first();

        //If user was not found, create new phone for user
        if(empty($obUser)) {
            Phone::create([
                'user_id' => $this->obUser->id,
                'phone' => $sPhone,
            ]);
        }
    }

    /**
     * Create new user
     * @param $arOrderData
     * @return void
     */
    private function createUser($arOrderData) {

        if(empty($arOrderData)) {
            return;
        }
        
        //TODO: Добавить возможность кастомно генерить пароль
        $sPassword = microtime(true);

        //Get user email
        if(isset($arOrderData[Plugin::FIND_USER_BY_EMAIL]) && !empty($arOrderData[Plugin::FIND_USER_BY_EMAIL])) {
            $sEmail = $arOrderData[Plugin::FIND_USER_BY_EMAIL];
        } else {
            $sEmail = 'fake' . $sPassword . '@fake.com';
        }

        $arUserData = [
            'name'                      => !empty($arOrderData['name']) ? $arOrderData['name'] : null,
            'last_name'                 => !empty($arOrderData['last_name']) ? $arOrderData['last_name'] : null,
            'property'                  => !empty($arOrderData['property']) ? $arOrderData['property'] : null,
            'email'                     => $sEmail,
            'password'                  => $sPassword,
            'password_confirmation'     => $sPassword,
        ];
        
        $arMessages = Buddies::getDefaultValidationMessage();
        $arMessages['email.unique'] = Lang::get('lovata.buddies::lang.message.e_email_unique');

        //Default validation 
        $obValidator = Validator::make($arUserData, User::getValidationRules(), $arMessages);

        if($obValidator->fails()) {
            $arErrorData = Buddies::getValidationError($obValidator);
            Result::setFalse($arErrorData);
            return;
        }
        
        $this->obUser = User::create($arUserData);

        $this->obUser->is_activated = true;
        $this->obUser->activated_at = Carbon::now();
        $this->obUser->save();

        if(!isset($arOrderData[Plugin::FIND_USER_BY_PHONE]) || empty($arOrderData[Plugin::FIND_USER_BY_PHONE])) {
            return;
        }

        //Find user by phone
        $sPhone = $arOrderData[Plugin::FIND_USER_BY_PHONE];
        $obUser = User::whereHas('order_phones', function ($obQuery) use ($sPhone) {
            /** @var Phone $obQuery */
            $obQuery->phone($sPhone);
        })->first();

        //If user was not found, create new phone for user
        if(empty($obUser)) {
            Phone::create([
                'user_id' => $this->obUser->id,
                'phone' => $sPhone,
            ]);
        }
    }
}
