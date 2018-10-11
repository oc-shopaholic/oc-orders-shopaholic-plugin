<?php namespace Lovata\OrdersShopaholic\Classes\PromoMechanism\PositionCountGreater;

use Lovata\OrdersShopaholic\Classes\PromoMechanism\InterfacePromoMechanism;

/**
 * Class PositionCountGreaterDiscountPositionTotalPrice
 * @package Lovata\OrdersShopaholic\Classes\PromoMechanism\PositionCountGreater
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class PositionCountGreaterDiscountPositionTotalPrice extends AbstractPositionCountGreaterDiscount implements InterfacePromoMechanism
{
    const LANG_NAME = 'lovata.ordersshopaholic::lang.promo_mechanism_type.position_count_greater_discount_position_total_price';

    /**
     * Get discount type
     * @return string
     */
    public static function getType() : string
    {
        return self::TYPE_TOTAL_POSITION;
    }
}