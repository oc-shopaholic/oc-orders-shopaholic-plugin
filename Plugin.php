<?php namespace Lovata\OrdersShopaholic;

use Event;
use System\Classes\PluginBase;

//Helpers
use Lovata\OrdersShopaholic\Console\RemoveOldCarts;
use Lovata\OrdersShopaholic\Classes\CartData;
use Lovata\OrdersShopaholic\Classes\OrderCreating;

//Items
use Lovata\OrdersShopaholic\Classes\Item\CartElementItem;
use Lovata\OrdersShopaholic\Classes\Item\PaymentMethodItem;
use Lovata\OrdersShopaholic\Classes\Item\ShippingTypeItem;

//Collection
use Lovata\OrdersShopaholic\Classes\Collection\CartElementCollection;
use Lovata\OrdersShopaholic\Classes\Collection\PaymentMethodCollection;
use Lovata\OrdersShopaholic\Classes\Collection\ShippingTypeCollection;

//Events
use Lovata\OrdersShopaholic\Classes\Event\CartItemModelHandler;
use Lovata\OrdersShopaholic\Classes\Event\PaymentMethodModelHandler;
use Lovata\OrdersShopaholic\Classes\Event\ShippingTypeModelHandler;
use Lovata\OrdersShopaholic\Classes\Event\ExtendFieldHandler;

/**
 * Class Plugin
 * @package Lovata\OrdersShopaholic
 * @author Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class Plugin extends PluginBase
{
    /**
     * Register component plugin method
     * @return array
     */
    public function registerComponents()
    {
        return [
            'Lovata\OrdersShopaholic\Components\Cart'              => 'Cart',
            'Lovata\OrdersShopaholic\Components\OrderCreate'       => 'OrderCreate',
            'Lovata\OrdersShopaholic\Components\ShippingTypeList'  => 'ShippingTypeList',
            'Lovata\OrdersShopaholic\Components\PaymentMethodList' => 'PaymentMethodList',
        ];
    }

    /**
     * Register command plugin method
     */
    public function register()
    {
        $this->registerConsoleCommand('shopaholic:remove-old-cart', RemoveOldCarts::class);
    }

    /**
     * Boot plugin method
     */
    public function boot()
    {
        $this->app->singleton(CartData::class, CartData::class);
        $this->app->bind(OrderCreating::class, OrderCreating::class);

        $this->app->bind(CartElementItem::class, CartElementItem::class);
        $this->app->bind(PaymentMethodItem::class, PaymentMethodItem::class);
        $this->app->bind(ShippingTypeItem::class, ShippingTypeItem::class);
        
        $this->app->bind(CartElementCollection::class, CartElementCollection::class);
        $this->app->bind(PaymentMethodCollection::class, PaymentMethodCollection::class);
        $this->app->bind(ShippingTypeCollection::class, ShippingTypeCollection::class);

        $this->addEventListener();
    }

    /**
     * Add event listeners
     */
    protected function addEventListener()
    {
        Event::subscribe(PaymentMethodModelHandler::class);
        Event::subscribe(ShippingTypeModelHandler::class);
        Event::subscribe(CartItemModelHandler::class);
        Event::subscribe(ExtendFieldHandler::class);
    }
}
