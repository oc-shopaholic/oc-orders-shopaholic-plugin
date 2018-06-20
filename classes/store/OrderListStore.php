<?php namespace Lovata\OrdersShopaholic\Classes\Store;

use Lovata\Toolbox\Classes\Store\AbstractListStore;

use Lovata\OrdersShopaholic\Classes\Store\Order\UserListStore;
use Lovata\OrdersShopaholic\Classes\Store\Order\StatusListStore;
use Lovata\OrdersShopaholic\Classes\Store\Order\ShippingTypeListStore;
use Lovata\OrdersShopaholic\Classes\Store\Order\PaymentMethodListStore;

/**
 * Class OrderListStore
 * @package Lovata\OrdersShopaholic\Classes\Store
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 * @property UserListStore $user
 * @property StatusListStore $status
 * @property ShippingTypeListStore $shipping_type
 * @property PaymentMethodListStore $payment_method
 */
class OrderListStore extends AbstractListStore
{
    protected static $instance;

    /**
     * Init store method
     */
    protected function init()
    {
        $this->addToStoreList('user', UserListStore::class);
        $this->addToStoreList('status', StatusListStore::class);
        $this->addToStoreList('shipping_type', ShippingTypeListStore::class);
        $this->addToStoreList('payment_method', PaymentMethodListStore::class);
    }
}
