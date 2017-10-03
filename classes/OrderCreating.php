<?php namespace Lovata\OrdersShopaholic\Classes;

use DB;
use Event;
use Kharanenka\Helper\Result;
use Lang;
use Lovata\OrdersShopaholic\Models\Order;
use Lovata\OrdersShopaholic\Models\Status;
use Lovata\Buddies\Models\User;
use Lovata\Shopaholic\Models\Offer;
use Lovata\Shopaholic\Models\Settings;

/**
 * Class OrderCreating
 * @package Lovata\OrdersShopaholic\Classes
 * @author Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class OrderCreating
{
    /** @var User */
    protected $obUser;
    
    /** @var CartData */
    protected $obCartData;
    
    //Settings
    protected $bCheckQuantityOnOrder = false;
    protected $bDecrementQuantityAfterOrder = false;
    protected $bCreateNewUser = true;
    protected $sUserKeyField;

    /**
     * OrderCreating constructor.
     * @param CartData $obCartData
     */
    public function __construct(CartData $obCartData)
    {
        $this->obCartData = $obCartData;
        
        // Get order behavior flags from settings
        $this->bCheckQuantityOnOrder = Settings::getValue('check_quantity_on_order');
        $this->bDecrementQuantityAfterOrder = Settings::getValue('decrement_quantity_after_order');

    }

    /**
     * Create new order
     * @param $arOrderData
     * @param User $obUser
     * @return void
     */
    public function create($arOrderData, $obUser = null)
    {
        $this->obUser = $obUser;
        
        if(empty($arOrderData)) {

            $sMessage = Lang::get('lovata.toolbox::lang.message.e_not_correct_request');
            Result::setMessage($sMessage);
            return;
        }

        if(!isset($arOrderData['item']) || empty($arOrderData['item'])) {
            
            $sMessage = Lang::get('lovata.ordersshopaholic::lang.message.empty_cart');
            Result::setMessage($sMessage);
            return;
        }

        if(!empty($this->obUser)) {
            $arOrderData['user_id'] = $this->obUser->id;
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
                continue;
            }

            //Check quantity
            if($this->bCheckQuantityOnOrder && $obOffer->quantity < $iQuantity) {
                
                $sMessage = Lang::get('lovata.ordersshopaholic::lang.message.insufficient_amount');
                Result::setFalse(['offer_id' => $iOfferID])->setMessage($sMessage);
                break;
            }

            //Decrement offer quantity
            if($this->bDecrementQuantityAfterOrder) {
                try {
                    DB::table('lovata_shopaholic_offers')->where('id', $obOffer->id)->decrement('quantity', $iQuantity);
                } catch (\Exception $e) {

                    $sMessage = Lang::get('lovata.ordersshopaholic::lang.message.insufficient_amount');
                    Result::setFalse(['offer_id' => $iOfferID])->setMessage($sMessage);
                    break;
                }
            }
            
            $arPivotData = [
                'price'         => $obOffer->getPriceValue(),
                'old_price'     => $obOffer->getOldPriceValue(),
                'quantity'      => $iQuantity,
                'code'          => $obOffer->code,
            ];
            
            //Attach offer to order
            $obOrder->offer()->attach($obOffer->id, $arPivotData);
        }
        
        if(!Result::status()) {
            DB::rollBack();
            return;
        }

        DB::commit();
        $obOrder->save();

        Event::fire('shopaholic.order.created', $obOrder);
        
        $this->obCartData->clear();
        
        $arResult = [
            'id'     => $obOrder->id,
            'number' => $obOrder->order_number,
        ];
        
        Result::setTrue($arResult);
    }
}
