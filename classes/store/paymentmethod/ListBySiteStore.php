<?php namespace Lovata\OrdersShopaholic\Classes\Store\PaymentMethod;

use Lovata\Toolbox\Classes\Store\AbstractStoreWithParam;

use Lovata\OrdersShopaholic\Models\PaymentMethod;

/**
 * @package Lovata\OrdersShopaholic\Classes\Store\PaymentMethod
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class ListBySiteStore extends AbstractStoreWithParam
{
    protected static $instance;

    /**
     * Get ID list from database
     * @return array
     */
    protected function getIDListFromDB() : array
    {
        $arElementIDList = (array) PaymentMethod::whereHas('site', function($obQuery) {
            return $obQuery->where('id', $this->sValue);
        })
            ->orDoesntHave('site')
            ->pluck('id')
            ->all();

        return $arElementIDList;
    }
}
