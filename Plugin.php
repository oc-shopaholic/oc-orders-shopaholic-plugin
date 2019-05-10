<?php namespace Lovata\OrdersShopaholic;

use Lang;
use Event;
use Backend;
use System\Classes\PluginBase;
//Console commands
use Lovata\OrdersShopaholic\Classes\Console\SendManagerNotification;

//Events
//Extend Backend menu events
use Lovata\OrdersShopaholic\Classes\Event\ExtendBackendMenuHandler;
//CartPosition events
use Lovata\OrdersShopaholic\Classes\Event\CartPosition\CartPositionModelHandler;
//Offer events
use Lovata\OrdersShopaholic\Classes\Event\Offer\OfferModelHandler;
//Order events
use Lovata\OrdersShopaholic\Classes\Event\Order\ExtendOrderFieldsHandler;
use Lovata\OrdersShopaholic\Classes\Event\Order\OrderModelHandler;
use Lovata\OrdersShopaholic\Classes\Event\Order\OrdersControllerHandler;
//Order position events
use Lovata\OrdersShopaholic\Classes\Event\OrderPosition\ExtendOrderPositionFieldsHandler;
use Lovata\OrdersShopaholic\Classes\Event\OrderPosition\OrderPositionModelHandler;
//Payment method events
use Lovata\OrdersShopaholic\Classes\Event\PaymentMethod\ExtendPaymentMethodFieldsHandler;
use Lovata\OrdersShopaholic\Classes\Event\PaymentMethod\PaymentMethodModelHandler;
use Lovata\OrdersShopaholic\Classes\Event\PaymentMethod\PaymentMethodRelationHandler;
//Payment restriction events
use Lovata\OrdersShopaholic\Classes\Event\PaymentRestriction\ExtendPaymentRestrictionFieldsHandler;
use Lovata\OrdersShopaholic\Classes\Event\PaymentRestriction\PaymentRestrictionRelationHandler;
use Lovata\OrdersShopaholic\Classes\Event\PaymentRestriction\PaymentRestrictionModelHandler;
//Product events
use Lovata\OrdersShopaholic\Classes\Event\Product\ProductModelHandler;
//Shipping restriction events
use Lovata\OrdersShopaholic\Classes\Event\ShippingRestriction\ExtendShippingRestrictionFieldsHandler;
use Lovata\OrdersShopaholic\Classes\Event\ShippingRestriction\ShippingRestrictionRelationHandler;
use Lovata\OrdersShopaholic\Classes\Event\ShippingRestriction\ShippingRestrictionModelHandler;
//Shipping type events
use Lovata\OrdersShopaholic\Classes\Event\ShippingType\ExtendShippingTypeFieldsHandler;
use Lovata\OrdersShopaholic\Classes\Event\ShippingType\ShippingTypeModelHandler;
use Lovata\OrdersShopaholic\Classes\Event\ShippingType\ShippingTypeRelationHandler;
//Settings events
use Lovata\OrdersShopaholic\Classes\Event\Settings\ExtendSettingsFieldHandler;
//Status events
use Lovata\OrdersShopaholic\Classes\Event\Status\StatusModelHandler;
//Tax events
use Lovata\OrdersShopaholic\Classes\Event\Tax\TaxModelHandler;
use Lovata\OrdersShopaholic\Classes\Event\Tax\ExtendTaxFieldsHandler;
//User events
use Lovata\OrdersShopaholic\Classes\Event\User\UserModelHandler;
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
    public function registerSettings()
    {
        return [
            'orders-shopaholic-menu-payment-methods' => [
                'label'       => 'lovata.ordersshopaholic::lang.menu.payment_methods',
                'description' => 'lovata.ordersshopaholic::lang.menu.payment_methods_description',
                'category'    => 'lovata.shopaholic::lang.tab.settings',
                'icon'        => 'icon-credit-card',
                'url'         => Backend::url('lovata/ordersshopaholic/paymentmethods'),
                'order'       => 1000,
                'permissions' => ['shopaholic-order-payment-type'],
            ],
            'orders-shopaholic-menu-shipping-types' => [
                'label'       => 'lovata.ordersshopaholic::lang.menu.shipping_types',
                'description' => 'lovata.ordersshopaholic::lang.menu.shipping_types_description',
                'category'    => 'lovata.shopaholic::lang.tab.settings',
                'icon'        => 'icon-truck',
                'url'         => Backend::url('lovata/ordersshopaholic/shippingtypes'),
                'order'       => 1100,
                'permissions' => ['shopaholic-delivery-type'],
            ],
            'orders-shopaholic-menu-statuses' => [
                'label'       => 'lovata.ordersshopaholic::lang.menu.statuses',
                'description' => 'lovata.ordersshopaholic::lang.menu.statuses_description',
                'category'    => 'lovata.shopaholic::lang.tab.settings',
                'icon'        => 'icon-random',
                'url'         => Backend::url('lovata/ordersshopaholic/statuses'),
                'order'       => 1300,
                'permissions' => ['shopaholic-order-status'],
            ],
            'orders-shopaholic-menu-order-property' => [
                'label'       => 'lovata.ordersshopaholic::lang.menu.order_property',
                'description' => 'lovata.ordersshopaholic::lang.menu.order_property_description',
                'category'    => 'lovata.shopaholic::lang.tab.settings',
                'icon'        => 'icon-list',
                'url'         => Backend::url('lovata/ordersshopaholic/orderproperties'),
                'order'       => 1400,
                'permissions' => ['shopaholic-order-property'],
            ],
            'orders-shopaholic-menu-order-position-property' => [
                'label'       => 'lovata.ordersshopaholic::lang.menu.order_position_property',
                'description' => 'lovata.ordersshopaholic::lang.menu.order_position_property_description',
                'category'    => 'lovata.shopaholic::lang.tab.settings',
                'icon'        => 'icon-list',
                'url'         => Backend::url('lovata/ordersshopaholic/orderpositionproperties'),
                'order'       => 1500,
                'permissions' => ['shopaholic-order-property'],
            ],
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
        //Extend Backend menu events
        Event::subscribe(ExtendBackendMenuHandler::class);
        //CartPosition events
        Event::subscribe(CartPositionModelHandler::class);
        //Offer events
        Event::subscribe(OfferModelHandler::class);
        //Order events
        Event::subscribe(ExtendOrderFieldsHandler::class);
        Event::subscribe(OrderModelHandler::class);
        Event::subscribe(OrdersControllerHandler::class);
        //Order position events
        Event::subscribe(ExtendOrderPositionFieldsHandler::class);
        Event::subscribe(OrderPositionModelHandler::class);
        //Payment method events
        Event::subscribe(ExtendPaymentMethodFieldsHandler::class);
        Event::subscribe(PaymentMethodModelHandler::class);
        Event::subscribe(PaymentMethodRelationHandler::class);
        //Payment restriction events
        Event::subscribe(ExtendPaymentRestrictionFieldsHandler::class);
        Event::subscribe(PaymentRestrictionRelationHandler::class);
        Event::subscribe(PaymentRestrictionModelHandler::class);
        //Product events
        Event::subscribe(ProductModelHandler::class);
        //Shipping restriction events
        Event::subscribe(ExtendShippingRestrictionFieldsHandler::class);
        Event::subscribe(ShippingRestrictionRelationHandler::class);
        Event::subscribe(ShippingRestrictionModelHandler::class);
        //Shipping type events
        Event::subscribe(ExtendShippingTypeFieldsHandler::class);
        Event::subscribe(ShippingTypeModelHandler::class);
        Event::subscribe(ShippingTypeRelationHandler::class);
        //Settings events
        Event::subscribe(ExtendSettingsFieldHandler::class);
        //Status events
        Event::subscribe(StatusModelHandler::class);
        //Tax events
        Event::subscribe(TaxModelHandler::class);
        Event::subscribe(ExtendTaxFieldsHandler::class);
        //User events
        Event::subscribe(UserModelHandler::class);
        Event::subscribe(ExtendUserFieldsHandler::class);
        Event::subscribe(ExtendUserControllerHandler::class);
        //User address events
        Event::subscribe(UserAddressModelHandler::class);
    }
}
