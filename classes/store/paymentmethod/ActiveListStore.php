<?php namespace Lovata\OrdersShopaholic\Classes\Store\PaymentMethod;

use Lovata\Toolbox\Classes\Store\AbstractStoreWithoutParam;

use Lovata\OrdersShopaholic\Models\PaymentMethod;

/**
 * Class ActiveListStore
 * @package Lovata\OrdersShopaholic\Classes\Store\PaymentMethod
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
        $arElementIDList = (array) PaymentMethod::active()->lists('id');

        return $arElementIDList;
    }
}
