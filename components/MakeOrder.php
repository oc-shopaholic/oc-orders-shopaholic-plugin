<?php namespace Lovata\OrdersShopaholic\Components;

use App;
use Input;
use Cms\Classes\ComponentBase;

use Kharanenka\Helper\Result;
use Lovata\Toolbox\Traits\Helpers\TraitValidationHelper;

use Lovata\Buddies\Models\User;
use Lovata\Buddies\Facades\AuthHelper;
use Lovata\Shopaholic\Models\Settings;
use Lovata\OrdersShopaholic\Classes\OrderProcessor;

/**
 * Class MakeOrder
 * @package Lovata\OrdersShopaholic\Components
 * @author Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class MakeOrder extends ComponentBase
{
    use TraitValidationHelper;
    
    protected $bCreateNewUser = true;

    protected $arOrderData;
    protected $arUserData;

    /** @var \Lovata\Buddies\Models\User */
    protected $obUser;

    /** @var \Lovata\OrdersShopaholic\Classes\OrderProcessor */
    protected $obOrderProcessor;
    
    /**
     * @return array
     */
    public function componentDetails()
    {
        return [
            'name'        => 'lovata.ordersshopaholic::lang.component.make_order_name',
            'description' => 'lovata.ordersshopaholic::lang.component.make_order_description',
        ];
    }

    /**
     * Init plugin method
     */
    public function init()
    {
        $this->bCreateNewUser = Settings::getValue('create_new_user');
        $this->obUser = AuthHelper::getUser();

        $this->obOrderProcessor = App::make(OrderProcessor::class);
    }

    /**
     * Order create
     * @return array
     */
    public function onCreate()
    {
        $arOrderData = Input::get('order');
        $arUserData = Input::get('user');

        return $this->create($arOrderData, $arUserData);
    }

    /**
     * Create new order
     * @param array $arOrderData
     * @param array $arUserData
     * @return array
     */
    public function create($arOrderData, $arUserData)
    {
        $this->arOrderData = $arOrderData;
        $this->arUserData = $arUserData;

        //Find or create new user
        if(empty($this->obUser) && $this->bCreateNewUser) {
            $this->findOrCreateUser();
        } else {

            $this->arUserData = [
                'email'       => $this->obUser->email,
                'name'        => $this->obUser->name,
                'last_name'   => $this->obUser->last_name,
                'middle_name' => $this->obUser->middle_name,
                'phone'       => $this->obUser->phone,
            ];
        }

        if(!Result::status()) {
            return Result::get();
        }

        $arOrderData = $this->arOrderData;
        if(!isset($arOrderData['property']) || !is_array($arOrderData['property'])) {
            $arOrderData['property'] = [];
        }

        if(!empty($this->arUserData)) {
            $arOrderData['property'] = array_merge($arOrderData['property'], $this->arUserData);
        }

        $this->obOrderProcessor->create($arOrderData, $this->obUser);

        return Result::get();
    }

    /**
     * Find user by request data or create new user
     */
    protected function findOrCreateUser()
    {
        if(!empty($this->obUser) || empty($this->arUserData)) {
            return;
        }

        $this->findUserByEmail();
        if(!empty($this->obUser) || !$this->bCreateNewUser) {
            return;
        }

        $this->createUser();
    }

    /**
     * Find user by email
     * @return void
     */
    protected function findUserByEmail()
    {
        if(empty($this->arUserData) || !isset($this->arUserData['email']) || empty($this->arUserData['email'])) {
            return;
        }

        //Find user by email
        $sEmail = $this->arUserData['email'];
        $this->obUser = User::getByEmail($sEmail)->first();
        $this->processUserPhone();
        $this->processUserPhoneList();
    }

    /**
     * Process user phone
     */
    protected function processUserPhone()
    {
        if(empty($this->obUser) || empty($this->arUserData)) {
            return;
        }
        
        if(!isset($this->arUserData['phone']) || empty($this->arUserData['phone'])) {
            return;
        }
        
        $sPhone = $this->arUserData['phone'];
        
        $arPhoneList = $this->obUser->phone_list;
        $arPhoneList[] = $sPhone;
        $arPhoneList = array_unique($arPhoneList);
        
        $this->obUser->phone_list = $arPhoneList;
        $this->obUser->save();
    }
    
    /**
     * Process user phone list
     */
    protected function processUserPhoneList()
    {
        if(empty($this->obUser) || empty($this->arUserData)) {
            return;
        }
        
        if(!isset($this->arUserData['phone_list']) || empty($this->arUserData['phone_list'])) {
            return;
        }
        
        $arRequestPhoneList = $this->arUserData['phone_list'];
        
        $arPhoneList = $this->obUser->phone_list;
        $arPhoneList = array_merge($arPhoneList, $arRequestPhoneList);
        $arPhoneList = array_unique($arPhoneList);
        
        $this->obUser->phone_list = $arPhoneList;
        $this->obUser->save();
    }

    /**
     * Create new user
     * @return void
     */
    protected function createUser()
    {
        if(empty($this->arUserData)) {
            return;
        }
        
        $sPassword = md5(microtime(true));

        //Get user email
        if(isset($this->arUserData['email']) && !empty($this->arUserData['email'])) {
            $sEmail = $this->arUserData['email'];
        } else {
            $sEmail = 'fake' . $sPassword . '@fake.com';
        }

        $arUserData = $this->arUserData;
        
        $arUserData['email'] = $sEmail;
        $arUserData['password'] = $sPassword;
        $arUserData['password_confirmation'] = $sPassword;

        try {
            //Create new user
            $this->obUser = AuthHelper::register($arUserData, true);
        } catch (\October\Rain\Database\ModelException $obException) {
            $this->processValidationError($obException);
            return;
        }
    }
}