<?php namespace Lovata\OrdersShopaholic\Classes\Store;

use Lovata\Toolbox\Classes\Store\AbstractListStore;

use Lovata\OrdersShopaholic\Classes\Store\PaymentMethod\ActiveListStore;
use Lovata\OrdersShopaholic\Classes\Store\PaymentMethod\SortingListStore;
use Lovata\OrdersShopaholic\Classes\Store\PaymentMethod\ListBySiteStore;

/**
 * Class PaymentMethodListStore
 * @package Lovata\OrdersShopaholic\Classes\Store
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 * @property ActiveListStore  $active
 * @property SortingListStore $sorting
 * @property ListBySiteStore  $site
 */
class PaymentMethodListStore extends AbstractListStore
{
    protected static $instance;

    /**
     * Init store method
     */
    protected function init()
    {
        $this->addToStoreList('sorting', SortingListStore::class);
        $this->addToStoreList('active', ActiveListStore::class);
        $this->addToStoreList('site', ListBySiteStore::class);
    }
}
