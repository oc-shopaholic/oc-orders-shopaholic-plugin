<?php namespace Lovata\OrdersShopaholic\Classes\Event\Shipping;

use Lovata\OrdersShopaholic\Classes\Item\ShippingTypeItem;

class Standard {

    /**
     * Add shipping fields
     * @param \Backend\Widgets\Form $obWidget
     */
    public function getFields()
    {
        return [

            // empty
        ];
    }

    public function run($obShipping, $obCart) {

        return $obShipping->price_value;
    }
}