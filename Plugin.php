<?php namespace Lovata\OrdersShopaholic;

use Lang;
use Event;
use System\Classes\PluginBase;
//Console commands
use Lovata\OrdersShopaholic\Classes\Console\SendManagerNotification;
//Events
//CartPosition events
use Lovata\OrdersShopaholic\Classes\Event\CartPosition\CartPositionModelHandler;
//Extend Backend menu events
use Lovata\OrdersShopaholic\Classes\Event\ExtendBackendMenuHandler;
use Lovata\OrdersShopaholic\Classes\Event\ExtendFieldHandler;
use Lovata\OrdersShopaholic\Classes\Event\ExtendPaymentMethodFieldHandler;
use Lovata\OrdersShopaholic\Classes\Event\OfferModelHandler;
use Lovata\OrdersShopaholic\Classes\Event\OrdersControllerHandler;
use Lovata\OrdersShopaholic\Classes\Event\OrderModelHandler;
use Lovata\OrdersShopaholic\Classes\Event\OrderPositionModelHandler;
use Lovata\OrdersShopaholic\Classes\Event\PaymentMethodModelHandler;
use Lovata\OrdersShopaholic\Classes\Event\ProductModelHandler;
use Lovata\OrdersShopaholic\Classes\Event\ShippingTypeModelHandler;
use Lovata\OrdersShopaholic\Classes\Event\StatusModelHandler;
//User events
use Lovata\OrdersShopaholic\Classes\Event\User\UserModelHandler;
use Lovata\OrdersShopaholic\Classes\Event\User\ExtendUserItemHandler;
use Lovata\OrdersShopaholic\Classes\Event\User\ExtendUserFieldsHandler;
use Lovata\OrdersShopaholic\Classes\Event\User\ExtendUserControllerHandler;
//User address events
use Lovata\OrdersShopaholic\Classes\Event\UserAddress\UserAddressModelHandler;

/**
 *
 * Class Plugin
 * @package Lovata\OrdersShopaholic
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class Plugin extends PluginBase
{
    public $require = ['Lovata.Shopaholic', 'Lovata.Toolbox'];

    /**
     * Register component plugin method
     * @return array
     */
    public function registerComponents()
    {
        return [
            'Lovata\OrdersShopaholic\Components\Cart'              => 'Cart',
            'Lovata\OrdersShopaholic\Components\MakeOrder'         => 'MakeOrder',
            'Lovata\OrdersShopaholic\Components\OrderPage'         => 'OrderPage',
            'Lovata\OrdersShopaholic\Components\ShippingTypeList'  => 'ShippingTypeList',
            'Lovata\OrdersShopaholic\Components\PaymentMethodList' => 'PaymentMethodList',
            'Lovata\OrdersShopaholic\Components\StatusList'        => 'StatusList',
            'Lovata\OrdersShopaholic\Components\UserAddress'       => 'UserAddress',
        ];
    }

    /**
     * @return array
     */
    public function registerMailTemplates()
    {
        return [
            'lovata.ordersshopaholic::mail.create_order_manager' => Lang::get('lovata.ordersshopaholic::mail.create_order_manager'),
            'lovata.ordersshopaholic::mail.create_order_user'    => Lang::get('lovata.ordersshopaholic::mail.create_order_user'),
        ];
    }

    /**
     * Register console command
     */
    public function register()
    {
        $this->registerConsoleCommand('shopaholic:order.send_manager_notification', SendManagerNotification::class);
    }

    /**
     * Boot plugin method
     */
    public function boot()
    {
        $this->addEventListener();
    }

    /**
     * Add event listeners
     */
    protected function addEventListener()
    {
        //CartPosition events
        Event::subscribe(CartPositionModelHandler::class);
        //Extend Backend menu events
        Event::subscribe(ExtendBackendMenuHandler::class);
        Event::subscribe(ExtendFieldHandler::class);
        Event::subscribe(ExtendPaymentMethodFieldHandler::class);
        Event::subscribe(OfferModelHandler::class);
        Event::subscribe(OrdersControllerHandler::class);
        Event::subscribe(OrderModelHandler::class);
        Event::subscribe(OrderPositionModelHandler::class);
        Event::subscribe(PaymentMethodModelHandler::class);
        Event::subscribe(ProductModelHandler::class);
        Event::subscribe(ShippingTypeModelHandler::class);
        Event::subscribe(StatusModelHandler::class);
        //User events
        Event::subscribe(UserModelHandler::class);
        Event::subscribe(ExtendUserItemHandler::class);
        Event::subscribe(ExtendUserFieldsHandler::class);
        Event::subscribe(ExtendUserControllerHandler::class);
        //User address events
        Event::subscribe(UserAddressModelHandler::class);
    }
}
