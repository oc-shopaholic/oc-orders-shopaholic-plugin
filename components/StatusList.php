<?php namespace Lovata\OrdersShopaholic\Components;

use Cms\Classes\ComponentBase;
use Lovata\OrdersShopaholic\Classes\Collection\StatusCollection;

/**
 * Class StatusList
 * @package Lovata\OrdersShopaholic\Components
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class StatusList extends ComponentBase
{
    /**
     * @return array
     */
    public function componentDetails()
    {
        return [
            'name'        => 'lovata.ordersshopaholic::lang.component.status_list_name',
            'description' => 'lovata.ordersshopaholic::lang.component.status_list_description',
        ];
    }

    /**
     * Make element collection
     * @param array $arElementIDList
     *
     * @return StatusCollection
     */
    public function make($arElementIDList = null)
    {
        return StatusCollection::make($arElementIDList);
    }
}
