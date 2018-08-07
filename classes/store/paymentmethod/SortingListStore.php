<?php namespace Lovata\OrdersShopaholic\Classes\Store\PaymentMethod;

use Lovata\Toolbox\Classes\Store\AbstractStoreWithoutParam;

use Lovata\OrdersShopaholic\Models\PaymentMethod;

/**
 * Class SortingListStore
 * @package Lovata\OrdersShopaholic\Classes\Store\PaymentMethod
 * @author Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class SortingListStore extends AbstractStoreWithoutParam
{
    protected static $instance;

    /**
     * Get ID list from database
     * @return array
     */
    protected function getIDListFromDB() : array
    {
        $arElementIDList = (array) PaymentMethod::orderBy('sort_order', 'asc')->lists('id');

        return $arElementIDList;
    }
}
