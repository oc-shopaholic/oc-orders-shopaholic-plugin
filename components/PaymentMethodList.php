<?php namespace Lovata\OrdersShopaholic\Components;

use Cms\Classes\ComponentBase;
use Lovata\OrdersShopaholic\Classes\Collection\PaymentMethodCollection;

/**
 * Class PaymentMethodList
 * @package Lovata\OrdersShopaholic\Components
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class PaymentMethodList extends ComponentBase
{
    /**
     * @return array
     */
    public function componentDetails()
    {
        return [
            'name'        => 'lovata.ordersshopaholic::lang.component.payment_method_list_name',
            'description' => 'lovata.ordersshopaholic::lang.component.payment_method_list_description',
        ];
    }

    /**
     * Make element collection
     * @param array $arElementIDList
     *
     * @return PaymentMethodCollection
     */
    public function make($arElementIDList = null)
    {
        return PaymentMethodCollection::make($arElementIDList);
    }
}
