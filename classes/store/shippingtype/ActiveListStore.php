<?php namespace Lovata\OrdersShopaholic\Classes\Store\ShippingType;

use Lovata\Toolbox\Classes\Store\AbstractStoreWithoutParam;

use Lovata\OrdersShopaholic\Models\ShippingType;

/**
 * Class ActiveListStore
 * @package Lovata\OrdersShopaholic\Classes\Store\ShippingType
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class ActiveListStore extends AbstractStoreWithoutParam
{
    protected static $instance;

    /**
     * Get ID list from database
     * @return array
     */
    protected function getIDListFromDB() : array
    {
        $arElementIDList = (array) ShippingType::active()->lists('id');

        return $arElementIDList;
    }
}
