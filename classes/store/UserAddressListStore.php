<?php namespace Lovata\OrdersShopaholic\Classes\Store;

use Lovata\Toolbox\Classes\Store\AbstractListStore;

use Lovata\OrdersShopaholic\Classes\Store\UserAddress\ListByUserStore;

/**
 * Class UserAddressListStore
 * @package Lovata\OrdersShopaholic\Classes\Store
 * @author  Sergey Zakharevich, s.zakharevich@lovata.com, LOVATA Group
 * @property ListByUserStore $user
 */
class UserAddressListStore extends AbstractListStore
{
    protected static $instance;

    /**
     * Init store method
     */
    protected function init()
    {
        $this->addToStoreList('user', ListByUserStore::class);
    }
}
