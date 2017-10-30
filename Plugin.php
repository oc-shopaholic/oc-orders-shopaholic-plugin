<?php namespace Lovata\OrdersShopaholic;

use Event;
use System\Classes\PluginBase;

//Helpers
use Lovata\OrdersShopaholic\Classes\CartProcessor;
use Lovata\OrdersShopaholic\Classes\OrderProcessor;

//Items
use Lovata\OrdersShopaholic\Classes\Item\CartElementItem;
use Lovata\OrdersShopaholic\Classes\Item\PaymentMethodItem;
use Lovata\OrdersShopaholic\Classes\Item\ShippingTypeItem;

//Collection
use Lovata\OrdersShopaholic\Classes\Collection\CartElementCollection;
use Lovata\OrdersShopaholic\Classes\Collection\PaymentMethodCollection;
use Lovata\OrdersShopaholic\Classes\Collection\ShippingTypeCollection;

//Events
use Lovata\OrdersShopaholic\Classes\Event\CartElementModelHandler;
use Lovata\OrdersShopaholic\Classes\Event\PaymentMethodModelHandler;
use Lovata\OrdersShopaholic\Classes\Event\ShippingTypeModelHandler;
use Lovata\OrdersShopaholic\Classes\Event\ExtendFieldHandler;
use Lovata\OrdersShopaholic\Classes\Event\OfferModelHandler;
use Lovata\OrdersShopaholic\Classes\Event\ProductModelHandler;

/**
 *
 * Class Plugin
 * @package Lovata\OrdersShopaholic
 * @author Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class Plugin extends PluginBase
{
    public $require = ['Lovata.Shopaholic', 'Lovata.Toolbox', 'Lovata.Buddies'];

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
        ];
    }

    /**
     * Boot plugin method
     */
    public function boot()
    {
        $this->app->singleton(CartProcessor::class, CartProcessor::class);
        $this->app->singleton(OrderProcessor::class, OrderProcessor::class);

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
        Event::subscribe(CartElementModelHandler::class);
        Event::subscribe(ExtendFieldHandler::class);
        Event::subscribe(OfferModelHandler::class);
        Event::subscribe(ProductModelHandler::class);
    }
}
