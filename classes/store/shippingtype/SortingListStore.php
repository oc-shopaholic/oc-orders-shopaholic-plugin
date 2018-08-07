<?php namespace Lovata\OrdersShopaholic\Classes\Store\ShippingType;

use Lovata\Toolbox\Classes\Store\AbstractStoreWithoutParam;

use Lovata\OrdersShopaholic\Models\ShippingType;

/**
 * Class SortingListStore
 * @package Lovata\OrdersShopaholic\Classes\Store\ShippingType
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
        $arElementIDList = (array) ShippingType::orderBy('sort_order', 'asc')->lists('id');

        return $arElementIDList;
    }
}
