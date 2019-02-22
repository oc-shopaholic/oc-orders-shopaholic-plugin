<?php namespace Lovata\OrdersShopaholic\Classes\Event\Tax;

use Lovata\Shopaholic\Models\Tax;

/**
 * Class TaxModelHandler
 * @package Lovata\OrdersShopaholic\Classes\Event\Tax
 * @author Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class TaxModelHandler
{
    /**
     * Add listeners
     */
    public function subscribe()
    {
        Tax::extend(function (Tax $obTax) {
            $obTax->addFillable(['applied_to_shipping_price']);
            $obTax->addCachedField(['applied_to_shipping_price']);
        });
    }
}
