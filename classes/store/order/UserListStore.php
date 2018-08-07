<?php namespace Lovata\OrdersShopaholic\Classes\Store\Order;

use Lovata\Toolbox\Classes\Store\AbstractStoreWithParam;

use Lovata\OrdersShopaholic\Models\Order;

/**
 * Class SortingListStore
 * @package Lovata\OrdersShopaholic\Classes\Store\Order
 * @author Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class UserListStore extends AbstractStoreWithParam
{
    protected static $instance;

    /**
     * Get ID list from database
     * @return array
     */
    protected function getIDListFromDB() : array
    {
        $arElementIDList = (array) Order::getByUser($this->sValue)->orderBy('id', 'desc')->lists('id');

        return $arElementIDList;
    }
}
