<?php namespace Lovata\OrdersShopaholic;

use Lang;
use Event;
use System\Classes\PluginBase;

//Events
use Lovata\OrdersShopaholic\Classes\Event\CartPositionModelHandler;
use Lovata\OrdersShopaholic\Classes\Event\ExtendBackendMenuHandler;
use Lovata\OrdersShopaholic\Classes\Event\ExtendFieldHandler;
use Lovata\OrdersShopaholic\Classes\Event\ExtendPaymentMethodFieldHandler;
use Lovata\OrdersShopaholic\Classes\Event\ExtendUserModelHandler;
use Lovata\OrdersShopaholic\Classes\Event\OfferModelHandler;
use Lovata\OrdersShopaholic\Classes\Event\OrdersControllerHandler;
use Lovata\OrdersShopaholic\Classes\Event\OrderModelHandler;
use Lovata\OrdersShopaholic\Classes\Event\OrderPositionModelHandler;
use Lovata\OrdersShopaholic\Classes\Event\PaymentMethodModelHandler;
use Lovata\OrdersShopaholic\Classes\Event\ProductModelHandler;
use Lovata\OrdersShopaholic\Classes\Event\ShippingTypeModelHandler;
use Lovata\OrdersShopaholic\Classes\Event\StatusModelHandler;

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
        Event::subscribe(CartPositionModelHandler::class);
        Event::subscribe(ExtendBackendMenuHandler::class);
        Event::subscribe(ExtendFieldHandler::class);
        Event::subscribe(ExtendPaymentMethodFieldHandler::class);
        Event::subscribe(ExtendUserModelHandler::class);
        Event::subscribe(OfferModelHandler::class);
        Event::subscribe(OrdersControllerHandler::class);
        Event::subscribe(OrderModelHandler::class);
        Event::subscribe(OrderPositionModelHandler::class);
        Event::subscribe(PaymentMethodModelHandler::class);
        Event::subscribe(ProductModelHandler::class);
        Event::subscribe(ShippingTypeModelHandler::class);
        Event::subscribe(StatusModelHandler::class);
    }
}
