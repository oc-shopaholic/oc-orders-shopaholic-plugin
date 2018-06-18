<?php namespace Lovata\OrdersShopaholic\Components;

use Cms\Classes\ComponentBase;
use Lovata\OrdersShopaholic\Classes\Collection\ShippingTypeCollection;

/**
 * Class ShippingTypeList
 * @package Lovata\OrdersShopaholic\Components
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class ShippingTypeList extends ComponentBase
{
    /**
     * @return array
     */
    public function componentDetails()
    {
        return [
            'name'        => 'lovata.ordersshopaholic::lang.component.shipping_type_list_name',
            'description' => 'lovata.ordersshopaholic::lang.component.shipping_type_list_description',
        ];
    }

    /**
     * Make element collection
     * @param array $arElementIDList
     *
     * @return ShippingTypeCollection
     */
    public function make($arElementIDList = null)
    {
        return ShippingTypeCollection::make($arElementIDList);
    }
}
