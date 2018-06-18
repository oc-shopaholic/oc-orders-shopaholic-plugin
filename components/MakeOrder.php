<?php namespace Lovata\OrdersShopaholic\Components;

use Input;
use System\Classes\PluginManager;

use Kharanenka\Helper\Result;
use Lovata\Toolbox\Classes\Helper\UserHelper;
use Lovata\Toolbox\Classes\Component\ComponentSubmitForm;
use Lovata\Toolbox\Traits\Helpers\TraitValidationHelper;

use Lovata\Shopaholic\Models\Settings;
use Lovata\OrdersShopaholic\Classes\Processor\OrderProcessor;

/**
 * Class MakeOrder
 * @package Lovata\OrdersShopaholic\Components
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class MakeOrder extends ComponentSubmitForm
{
    use TraitValidationHelper;

    protected $bCreateNewUser = true;

    protected $arOrderData;
    protected $arUserData;

    /** @var \Lovata\Buddies\Models\User */
    protected $obUser;

    /** @var UserHelper */
    protected $obUserHelper;

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
     * Get redirect page property list
     * @return array
     */
    protected function getRedirectPageProperties()
    {
        if (!Result::status()) {
            return [];
        }

        $arResult = Result::data();
        if (empty($arResult) || !is_array($arResult)) {
            return [];
        }

        return $arResult;
    }

    /**
     * Init plugin method
     */
    public function init()
    {
        $this->obUserHelper = UserHelper::instance();

        $this->bCreateNewUser = Settings::getValue('create_new_user');
        $this->obUser = $this->obUserHelper->getUser();

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

        return $this->getResponseModeForm();
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

        return $this->getResponseModeAjax();
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

            $this->arUserData = [
                'email'       => $this->obUser->email,
                'name'        => $this->obUser->name,
                'last_name'   => $this->obUser->last_name,
                'middle_name' => $this->obUser->middle_name,
                'phone'       => $this->obUser->phone,
            ];
        }

        if (!Result::status()) {
            return;
        }

        $arOrderData = $this->arOrderData;
        if (!isset($arOrderData['property']) || !is_array($arOrderData['property'])) {
            $arOrderData['property'] = [];
        }

        if (!empty($this->arUserData)) {
            $arOrderData['property'] = array_merge($arOrderData['property'], $this->arUserData);
        }

        $obOrder = OrderProcessor::instance()->create($arOrderData, $this->obUser);
        if (empty($obOrder)) {
            return;
        }

        if (PluginManager::instance()->hasPlugin('Lovata.OmnipayShopaholic')) {

            $arPaymentData = Input::get('payment');
            if (!empty($arPaymentData)) {
                $obOrder->payment_data = $arPaymentData;
                $obOrder->save();
            }
        }
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
        $this->obUser = $this->obUserHelper->findUserByEmail($sEmail);

        //if Buddies plugin is installed, then we need to process "phone" field
        if ($this->obUserHelper->getPluginName() == 'Lovata.Buddies') {
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

        $arUserData = $this->arUserData;
        $arUserData['password'] = $sPassword;
        $arUserData['password_confirmation'] = $sPassword;

        try {
            //Create new user
            $this->obUser = $this->obUserHelper->register($arUserData, true);
        } catch (\October\Rain\Database\ModelException $obException) {
            $this->processValidationError($obException);
            return;
        }
    }
}