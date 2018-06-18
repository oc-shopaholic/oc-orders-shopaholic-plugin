<?php namespace Lovata\OrdersShopaholic\Classes\Store\Status;

use Lovata\Toolbox\Classes\Store\AbstractStoreWithoutParam;

use Lovata\OrdersShopaholic\Models\Status;

/**
 * Class IsUserShowListStore
 * @package Lovata\OrdersShopaholic\Classes\Store\Status
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class IsUserShowListStore extends AbstractStoreWithoutParam
{
    protected static $instance;

    /**
     * Get ID list from database
     * @return array
     */
    protected function getIDListFromDB() : array
    {
        $arElementIDList = (array) Status::isUserShow()->lists('id');

        return $arElementIDList;
    }
}
