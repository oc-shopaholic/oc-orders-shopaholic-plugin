<?php namespace Lovata\OrdersShopaholic\Classes\PromoMechanism\PositionTotalPriceGreater;

use Lovata\OrdersShopaholic\Classes\PromoMechanism\InterfacePromoMechanism;

/**
 * Class PositionTotalPriceGreaterTotalPrice
 * @package Lovata\OrdersShopaholic\Classes\PromoMechanism\PositionTotalPriceGreater
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class PositionTotalPriceGreaterDiscountTotalPrice extends AbstractPositionTotalPriceGreaterDiscount implements InterfacePromoMechanism
{
    const LANG_NAME = 'lovata.ordersshopaholic::lang.promo_mechanism_type.position_total_price_greater_discount_total_price';

    /**
     * Get discount type
     * @return string
     */
    public static function getType() : string
    {
        return self::TYPE_TOTAL_PRICE;
    }
}