<?php namespace Lovata\OrdersShopaholic\Classes\Store;

use Lovata\Toolbox\Classes\Store\AbstractListStore;

use Lovata\OrdersShopaholic\Classes\Store\Status\IsUserShowListStore;
use Lovata\OrdersShopaholic\Classes\Store\Status\SortingListStore;

/**
 * Class StatusListStore
 * @package Lovata\OrdersShopaholic\Classes\Store
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 * @property IsUserShowListStore $is_user_show
 * @property SortingListStore    $sorting
 */
class StatusListStore extends AbstractListStore
{
    protected static $instance;

    /**
     * Init store method
     */
    protected function init()
    {
        $this->addToStoreList('sorting', SortingListStore::class);
        $this->addToStoreList('is_user_show', IsUserShowListStore::class);
    }
}
