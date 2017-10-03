<?php namespace Lovata\OrdersShopaholic\Components;

use Input;
use Cms\Classes\ComponentBase;
use Lovata\Buddies\Facades\BuddiesAuth;
use Kharanenka\Helper\Result;
use Lovata\Buddies\Models\User;
use Lovata\OrdersShopaholic\Classes\OrderCreating;
use Lovata\Shopaholic\Models\Settings;
use Lovata\Toolbox\Traits\Helpers\TraitValidationHelper;
use October\Rain\Argon\Argon;
use October\Rain\Exception\ValidationException;

/**
 * Class Ordering
 * @package Lovata\OrdersShopaholic\Components
 * @author Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class Ordering extends ComponentBase
{
    use TraitValidationHelper;
    
    protected $bCreateNewUser = true;
    
    protected $arOrderData;
    protected $arUserData;

    /** @var \Lovata\Buddies\Models\User */
    protected $obUser;
    
    /**
     * @return array
     */
    public function componentDetails()
    {
        return [
            'name'        => 'lovata.ordersshopaholic::lang.component.ordering_name',
            'description' => 'lovata.ordersshopaholic::lang.component.ordering_description',
        ];
    }

    /**
     * Init plugin method
     */
    public function init()
    {
        $this->bCreateNewUser = Settings::getValue('create_new_user');
    }

    /**
     * Order create
     * @return array
     */
    public function onMakeOrder()
    {
        $this->arOrderData = Input::get('order');
        $this->arUserData = Input::get('user');

        $this->obUser = BuddiesAuth::getUser();

        //Find or create new user
        if(empty($this->obUser) && $this->bCreateNewUser) {
            $this->findOrCreateUser();
        }
        
        if(!Result::status()) {
            return Result::get();
        }
        
        $arOrderData = $this->arOrderData;
        if(!isset($arOrderData['property']) || !is_array($arOrderData['property'])) {
            $arOrderData['property'] = [];
        }
        
        $arOrderData['property'] = array_merge($arOrderData['property'], $this->arUserData);
        
        /** @var OrderCreating $obOrdering */
        $obOrdering = app()->make(OrderCreating::class);
        $obOrdering->create($arOrderData, $this->obUser);

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
        $arUserData['password_change'] = true;
        $arUserData['password'] = $sPassword;
        $arUserData['password_confirmation'] = $sPassword;

        try {
            $this->obUser = User::create($arUserData);
        } catch(ValidationException $obValidation) {
            
            Result::setFalse($this->getValidationError($obValidation));
            return;
        }

        $this->obUser->password_change = true;
        $this->obUser->password = $sPassword;
        $this->obUser->password_confirmation = $sPassword;
        
        $this->obUser->is_activated = true;
        $this->obUser->activated_at = Argon::now();
        $this->obUser->save();
    }
}