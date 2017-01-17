<?php namespace Lovata\OrdersShopaholic\Components;

use Cms\Classes\CodeBase;
use Cms\Classes\ComponentBase;
use Lang;
use Lovata\OrdersShopaholic\Classes\COrder;
use Lovata\OrdersShopaholic\Models\PaymentMethod;
use Lovata\OrdersShopaholic\Models\ShippingType;

/**
 * Class Ordering
 * @package Lovata\OrdersShopaholic\Components
 * @author Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 * @author Denis Plisko, d.plisko@lovata.com, LOVATA Group
 */
class Ordering extends ComponentBase
{

    public function componentDetails()
    {
        return [
            'name' => Lang::get('lovata.ordersshopaholic::lang.component.ordering_name'),
            'description' => Lang::get('lovata.ordersshopaholic::lang.component.ordering_description'),
        ];
    }

    public function __construct(CodeBase $cmsObject = null, $properties = [])
    {
        parent::__construct($cmsObject, $properties);
    }

    /**
     * Order create
     * @return array
     */
    public function onMakeOrder()
    {
        $arData = post('order_data');
        
        $obCOrder = new COrder();
        return $obCOrder->makeOrder($arData);
    }

    /**
     * Get payment methods list
     * @return array
     */
    public function getPaymentMethods()
    {
        return PaymentMethod::getPaymentMethods();
    }

    /**
     * Get shipping types list
     * @return array
     */
    public function getShippingTypes()
    {
        return ShippingType::getShippingTypes();
    }
}