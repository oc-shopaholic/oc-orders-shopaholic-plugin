<?php namespace Lovata\OrdersShopaholic\Components;

use Input;
use Event;
use Redirect;
use Kharanenka\Helper\Result;
use Lovata\Toolbox\Classes\Helper\PageHelper;
use Lovata\Toolbox\Classes\Helper\UserHelper;
use Lovata\Toolbox\Classes\Component\ComponentSubmitForm;
use Lovata\Toolbox\Traits\Helpers\TraitValidationHelper;

use Lovata\Shopaholic\Models\Settings;
use Lovata\Shopaholic\Classes\Helper\CurrencyHelper;
use Lovata\OrdersShopaholic\Models\UserAddress;
use Lovata\OrdersShopaholic\Classes\Processor\OrderProcessor;
use Lovata\OrdersShopaholic\Classes\Processor\CartProcessor;

/**
 * Class MakeOrder
 * @package Lovata\OrdersShopaholic\Components
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class MakeOrder extends ComponentSubmitForm
{
    use TraitValidationHelper;

    protected $bCreateNewUser = true;

    protected $arOrderData = [];
    protected $arUserData = [];
    protected $arBillingAddressOrder = [];
    protected $arShippingAddressOrder = [];
    /** @var \Lovata\Buddies\Models\User */
    protected $obUser;

    /** @var \Lovata\OrdersShopaholic\Models\Order */
    protected $obOrder;

    /** @var \Lovata\OrdersShopaholic\Interfaces\PaymentGatewayInterface|null */
    protected $obPaymentGateway;

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
     * @return array
     */
    public function defineProperties()
    {
        $arResult = $this->getModeProperty();

        return $arResult;
    }

    /**
     * Init plugin method
     */
    public function init()
    {
        $this->bCreateNewUser = Settings::getValue('create_new_user');
        $this->obUser = UserHelper::instance()->getUser();

        parent::init();
    }

    /**
     * Create new order
     * @return \Illuminate\Http\RedirectResponse|null
     * @throws \Exception
     */
    public function onRun()
    {
        if ($this->sMode != self::MODE_SUBMIT) {
            return null;
        }

        $arOrderData = (array) Input::get('order');
        $arUserData = (array) Input::get('user');
        if (empty($arOrderData) && empty($arUserData)) {
            return null;
        }

        $this->create($arOrderData, $arUserData);

        //Fire event and get redirect URL
        $sRedirectURL = Event::fire(OrderProcessor::EVENT_ORDER_GET_REDIRECT_URL, $this->obOrder, true);
        if (!Result::status() && !empty($this->obPaymentGateway) && $this->obPaymentGateway->isRedirect()) {
            $sRedirectURL = $this->obPaymentGateway->getRedirectURL();

            return Redirect::to($sRedirectURL);
        } else if (empty($this->obPaymentGateway) || !Result::status()) {
            return $this->getResponseModeForm($sRedirectURL);
        }

        if ($this->obPaymentGateway->isRedirect()) {
            $sRedirectURL = $this->obPaymentGateway->getRedirectURL();

            return Redirect::to($sRedirectURL);
        } else if ($this->obPaymentGateway->isSuccessful()) {
            Result::setTrue($this->obPaymentGateway->getResponse());
        } else {
            Result::setFalse($this->obPaymentGateway->getResponse());
        }

        Result::setMessage($this->obPaymentGateway->getMessage());

        return $this->getResponseModeForm($sRedirectURL);
    }

    /**
     * Create new order (AJAX)
     * @return \Illuminate\Http\RedirectResponse|array
     * @throws \Exception
     */
    public function onCreate()
    {
        $arOrderData = (array) Input::get('order');
        $arUserData = (array) Input::get('user');

        $this->create($arOrderData, $arUserData);

        //Fire event and get redirect URL
        $sRedirectURL = Event::fire(OrderProcessor::EVENT_ORDER_GET_REDIRECT_URL, $this->obOrder, true);
        if (!Result::status() && !empty($this->obPaymentGateway) && $this->obPaymentGateway->isRedirect()) {
            $sRedirectURL = $this->obPaymentGateway->getRedirectURL();

            return Redirect::to($sRedirectURL);
        } else if (empty($this->obPaymentGateway) || !Result::status()) {
            $this->prepareResponseData();

            return $this->getResponseModeAjax($sRedirectURL);
        }

        if ($this->obPaymentGateway->isRedirect()) {
            $sRedirectURL = $this->obPaymentGateway->getRedirectURL();

            return Redirect::to($sRedirectURL);
        } else if ($this->obPaymentGateway->isSuccessful()) {
            Result::setTrue($this->obPaymentGateway->getResponse());
        } else {
            Result::setFalse($this->obPaymentGateway->getResponse());
        }

        Result::setMessage($this->obPaymentGateway->getMessage());
        $this->prepareResponseData();

        return $this->getResponseModeAjax($sRedirectURL);
    }

    /**
     * Create new order
     * @param array $arOrderData
     * @param array $arUserData
     * @throws \Exception
     */
    public function create($arOrderData, $arUserData)
    {
        $this->arOrderData = (array) $arOrderData;
        $this->arUserData = (array) $arUserData;

        //Find or create new user
        if (empty($this->obUser) && $this->bCreateNewUser) {
            $this->findOrCreateUser();
        } else if (!empty($this->obUser)) {
            $arAuthUserData = [
                'email'       => $this->obUser->email,
                'name'        => $this->obUser->name,
                'last_name'   => $this->obUser->last_name,
                'middle_name' => $this->obUser->middle_name,
                'phone'       => $this->obUser->phone,
            ];

            $this->arUserData = array_merge($arAuthUserData, $this->arUserData);
        }

        $obCart = CartProcessor::instance()->getCartObject();
        $this->arUserData = array_merge((array) $obCart->user_data, $this->arUserData);

        $this->processOrderAddress();

        if (!Result::status()) {
            return;
        }

        $arOrderData = $this->arOrderData;

        $obActiveCurrency = CurrencyHelper::instance()->getActive();
        if (empty(array_get($arOrderData, 'currency')) && !empty($obActiveCurrency)) {
            $arOrderData['currency_id'] = $obActiveCurrency->id;
        }
        if (!isset($arOrderData['property']) || !is_array($arOrderData['property'])) {
            $arOrderData['property'] = [];
        }

        $arOrderData['property'] = array_merge($arOrderData['property'], $this->arUserData, $this->arBillingAddressOrder, $this->arShippingAddressOrder);

        $arPaymentData = Input::get('payment');
        if (!empty($arPaymentData) && is_array($arPaymentData)) {
            $arOrderData['payment_data'] = $arPaymentData;
        }

        $this->obOrder = OrderProcessor::instance()->create($arOrderData, $this->obUser);
        $this->obPaymentGateway = OrderProcessor::instance()->getPaymentGateway();
    }

    /**
     * Get redirect page property list
     * @return array
     */
    protected function getRedirectPageProperties()
    {
        if (!Result::status() || empty($this->obOrder)) {
            return [];
        }

        $arResult = [
            'id'     => $this->obOrder->id,
            'number' => $this->obOrder->order_number,
            'key'    => $this->obOrder->secret_key,
        ];

        $sRedirectPage = $this->property(self::PROPERTY_REDIRECT_PAGE);
        if (empty($sRedirectPage)) {
            return $arResult;
        }

        $arPropertyList = PageHelper::instance()->getUrlParamList($sRedirectPage, 'OrderPage');
        if (!empty($arPropertyList)) {
            $arResult[array_shift($arPropertyList)] = $this->obOrder->secret_key;
        }

        return $arResult;
    }

    /**
     * Find user by request data or create new user
     */
    protected function findOrCreateUser()
    {
        $sUserPluginName = UserHelper::instance()->getPluginName();
        if (!empty($this->obUser) || empty($this->arUserData) || empty($sUserPluginName)) {
            return;
        }

        $this->findUserByEmail();
        if (!empty($this->obUser) || !$this->bCreateNewUser) {
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
        if (empty($this->arUserData) || !isset($this->arUserData['email']) || empty($this->arUserData['email'])) {
            return;
        }

        //Find user by email
        $sEmail = $this->arUserData['email'];
        $this->obUser = UserHelper::instance()->findUserByEmail($sEmail);
        if (empty($this->obUser)) {
            $this->obUser = Event::fire(OrderProcessor::EVENT_ORDER_FIND_USER_BEFORE_CREATE, $this->arUserData, true);
        }

        //if Buddies plugin is installed, then we need to process "phone" field
        if (UserHelper::instance()->getPluginName() == 'Lovata.Buddies') {
            $this->processUserPhone();
            $this->processUserPhoneList();
        }
    }

    /**
     * Process user phone
     */
    protected function processUserPhone()
    {
        if (empty($this->obUser) || empty($this->arUserData)) {
            return;
        }

        if (!isset($this->arUserData['phone']) || empty($this->arUserData['phone'])) {
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
        if (empty($this->obUser) || empty($this->arUserData)) {
            return;
        }

        if (!isset($this->arUserData['phone_list']) || empty($this->arUserData['phone_list'])) {
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

        if (empty($this->arUserData)) {
            return;
        }

        $sPassword = md5(microtime(true));

        //Get user email
        if (Settings::getValue('generate_fake_email') && (!isset($this->arUserData['email']) || empty($this->arUserData['email']))) {
            $this->arUserData['email'] = 'fake'.$sPassword.'@fake.com';
        }

        $arUserData = (array) $this->arUserData;

        if (!isset($arUserData['password']) || empty($arUserData['password'])) {
            $arUserData['password'] = $sPassword;
        }

        $arUserData['password_confirmation'] = $arUserData['password'];

        try {
            //Create new user
            $this->obUser = UserHelper::instance()->register($arUserData, true);
        } catch (\October\Rain\Database\ModelException $obException) {
            $this->processValidationError($obException);
            return;
        }

        Event::fire(OrderProcessor::EVENT_ORDER_USER_CREATED, $this->obUser);
    }

    /**
     * Process shipping/billing addresses. Create new user address or get data from exist address
     */
    protected function processOrderAddress()
    {
        $arShippingAddressData = (array) Input::get('shipping_address');
        $arBillingAddressData = (array) Input::get('billing_address');

        $obCart = CartProcessor::instance()->getCartObject();
        $arShippingAddressData = array_merge((array) $obCart->shipping_address, $arShippingAddressData);
        $arBillingAddressData = array_merge((array) $obCart->billing_address, $arBillingAddressData);

        $this->arShippingAddressOrder = $this->addOrderAddress(UserAddress::ADDRESS_TYPE_SIPPING, $arShippingAddressData);
        $this->arBillingAddressOrder = $this->addOrderAddress(UserAddress::ADDRESS_TYPE_BILLING, $arBillingAddressData);
    }

    /**
     * Add user address data
     * @param string $sType
     * @param array  $arAddressData
     * @return array
     */
    protected function addOrderAddress($sType, $arAddressData) : array
    {
        if (empty($arAddressData) || empty($sType) || empty($this->obUser)) {
            return $this->prepareAddressData($sType, $arAddressData);
        }

        $arResult = $this->findAddressByID($sType, $arAddressData);

        if (empty($arResult)) {
            $obAddress = UserAddress::findAddressByData($arAddressData, $this->obUser->id);
            if (!empty($obAddress)) {
                $arResult = $this->getAddressData($obAddress);
            }
        }

        if (empty($arResult)) {
            $arResult = $this->createUserAddress($sType, $arAddressData);
        }

        return $this->prepareAddressData($sType, $arResult);
    }

    /**
     * Prepare address array to save in Order properties
     * @param string $sType
     * @param array  $arAddressData
     * @return array
     */
    protected function prepareAddressData($sType, $arAddressData) : array
    {
        if (empty($arAddressData)) {
            return [];
        }

        $arResult = [];
        foreach ($arAddressData as $sKey => $sValue) {
            $arResult[$sType.'_'.$sKey] = $sValue;
        }

        return $arResult;
    }

    /**
     * Find Address object by ID, type and user_id
     * @param string $sType
     * @param array  $arAddressData
     * @return array
     */
    protected function findAddressByID($sType, $arAddressData) : array
    {
        $iAddressID = array_get($arAddressData, 'id');
        if (empty($iAddressID)) {
            return [];
        }

        $obAddress = UserAddress::getByUser($this->obUser->id)->getByType($sType)->find($iAddressID);
        if (empty($obAddress)) {
            return [];
        }

        return $this->getAddressData($obAddress);
    }

    /**
     * @param string $sType
     * @param array  $arAddressData
     * @return array
     */
    protected function createUserAddress($sType, $arAddressData) : array
    {
        if (empty($arAddressData)) {
            return [];
        }

        $arAddressData['type'] = $sType;
        $arAddressData['user_id'] = $this->obUser->id;

        try {
            //Create new address for user
            $obAddress = UserAddress::create($arAddressData);
        } catch (\October\Rain\Database\ModelException $obException) {
            $this->processValidationError($obException);
            return [];
        }

        return $this->getAddressData($obAddress);
    }

    /**
     * Get address data from object
     * @param UserAddress $obAddress
     * @return array
     */
    protected function getAddressData($obAddress) : array
    {
        if (empty($obAddress)) {
            return [];
        }

        $arResult = $obAddress->toArray();
        array_forget($arResult, ['id', 'type']);

        return $arResult;
    }

    /**
     * Fire event and prepare response data
     */
    protected function prepareResponseData()
    {
        if (!Result::status()) {
            return;
        }

        $arResponseData = Result::data();
        $arEventData = Event::fire(OrderProcessor::EVENT_UPDATE_ORDER_RESPONSE_AFTER_CREATE, [$arResponseData, $this->obOrder, $this->obUser, $this->obPaymentGateway]);
        if (empty($arEventData)) {
            return;
        }

        foreach ($arEventData as $arData) {
            if (empty($arData)) {
                continue;
            }

            $arResponseData = array_merge($arResponseData, $arData);
        }

        Result::setData($arResponseData);
    }
}
