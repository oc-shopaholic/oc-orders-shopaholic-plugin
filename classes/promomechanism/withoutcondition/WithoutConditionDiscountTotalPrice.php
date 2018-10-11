<?php namespace Lovata\OrdersShopaholic\Classes\PromoMechanism\WithoutCondition;

use Lovata\OrdersShopaholic\Classes\PromoMechanism\InterfacePromoMechanism;

/**
 * Class WithoutConditionDiscountTotalPrice
 * @package Lovata\OrdersShopaholic\Classes\PromoMechanism\WithoutCondition
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class WithoutConditionDiscountTotalPrice extends AbstractWithoutConditionDiscount implements InterfacePromoMechanism
{
    const LANG_NAME = 'lovata.ordersshopaholic::lang.promo_mechanism_type.without_condition_discount_total_price';

    /**
     * Get discount type
     * @return string
     */
    public static function getType() : string
    {
        return self::TYPE_TOTAL_PRICE;
    }
}