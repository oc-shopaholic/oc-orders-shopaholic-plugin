<?php namespace Lovata\OrdersShopaholic\Classes\PromoMechanism\PositionCountGreater;

use Lovata\OrdersShopaholic\Classes\PromoMechanism\InterfacePromoMechanism;

/**
 * Class PositionCountGreaterDiscountShippingPrice
 * @package Lovata\OrdersShopaholic\Classes\PromoMechanism\PositionCountGreater
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class PositionCountGreaterDiscountShippingPrice extends AbstractPositionCountGreaterDiscount implements InterfacePromoMechanism
{
    const LANG_NAME = 'lovata.ordersshopaholic::lang.promo_mechanism_type.position_count_greater_discount_shipping_price';

    /**
     * Get discount type
     * @return string
     */
    public static function getType() : string
    {
        return self::TYPE_SHIPPING;
    }

    /**
     * Check discount condition
     * @param \Lovata\OrdersShopaholic\Classes\PromoMechanism\AbstractPromoMechanismProcessor $obProcessor
     * @param \Lovata\OrdersShopaholic\Classes\Item\ShippingTypeItem                          $obShippingType
     * @return bool
     */
    protected function check($obProcessor, $obShippingType = null) : bool
    {
        return $this->checkShippingType($obShippingType) && parent::check($obProcessor, $obShippingType);
    }
}
