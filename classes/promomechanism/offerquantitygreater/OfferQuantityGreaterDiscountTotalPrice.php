<?php namespace Lovata\OrdersShopaholic\Classes\PromoMechanism\OfferQuantityGreater;

use Lovata\OrdersShopaholic\Classes\PromoMechanism\InterfacePromoMechanism;

/**
 * Class OfferQuantityGreaterDiscountTotalPrice
 * @package Lovata\OrdersShopaholic\Classes\PromoMechanism\OfferQuantityGreater
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class OfferQuantityGreaterDiscountTotalPrice extends AbstractOfferQuantityGreaterDiscount implements InterfacePromoMechanism
{
    const LANG_NAME = 'lovata.ordersshopaholic::lang.promo_mechanism_type.offer_quantity_greater_discount_total_price';

    /**
     * Get discount type
     * @return string
     */
    public static function getType() : string
    {
        return self::TYPE_TOTAL_PRICE;
    }
}