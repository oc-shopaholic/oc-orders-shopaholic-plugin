<?php namespace Lovata\OrdersShopaholic\Classes\Store\UserAddress;

use Lovata\Toolbox\Classes\Store\AbstractStoreWithParam;

use Lovata\OrdersShopaholic\Models\UserAddress;

/**
 * Class ListByUserStore
 * @package Lovata\Shopaholic\Classes\Store\Product
 * @author  Sergey Zakharevich, s.zakharevich@lovata.com, LOVATA Group
 */
class ListByUserStore extends AbstractStoreWithParam
{
    protected static $instance;

    /**
     * Get ID list from database
     * @return array
     */
    protected function getIDListFromDB() : array
    {
        $arElementIDList = (array) UserAddress::getByUser($this->sValue)->lists('id');

        return $arElementIDList;
    }
}
