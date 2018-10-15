<?php namespace Lovata\OrdersShopaholic\Classes\PromoMechanism\OfferTotalQuantityGreater;

use Lovata\OrdersShopaholic\Classes\PromoMechanism\InterfacePromoMechanism;

/**
 * Class OfferTotalQuantityGreaterDiscountTotalPrice
 * @package Lovata\OrdersShopaholic\Classes\PromoMechanism\OfferTotalQuantityGreater
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class OfferTotalQuantityGreaterDiscountTotalPrice extends AbstractOfferTotalQuantityGreaterDiscount implements InterfacePromoMechanism
{
    const LANG_NAME = 'lovata.ordersshopaholic::lang.promo_mechanism_type.offer_total_quantity_greater_discount_total_price';

    /**
     * Get discount type
     * @return string
     */
    public static function getType() : string
    {
        return self::TYPE_TOTAL_PRICE;
    }
}