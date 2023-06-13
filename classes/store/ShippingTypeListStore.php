<?php namespace Lovata\OrdersShopaholic\Classes\Store;

use Lovata\Toolbox\Classes\Store\AbstractListStore;

use Lovata\OrdersShopaholic\Classes\Store\ShippingType\ActiveListStore;
use Lovata\OrdersShopaholic\Classes\Store\ShippingType\SortingListStore;
use Lovata\OrdersShopaholic\Classes\Store\ShippingType\ListBySiteStore;

/**
 * Class ShippingTypeListStore
 * @package Lovata\OrdersShopaholic\Classes\Store
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 * @property ActiveListStore  $active
 * @property SortingListStore $sorting
 * @property ListBySiteStore  $site
 */
class ShippingTypeListStore extends AbstractListStore
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
