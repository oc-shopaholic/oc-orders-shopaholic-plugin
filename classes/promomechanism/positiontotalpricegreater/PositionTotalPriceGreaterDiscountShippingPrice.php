<?php namespace Lovata\OrdersShopaholic\Classes\PromoMechanism\PositionTotalPriceGreater;

use Lovata\OrdersShopaholic\Classes\PromoMechanism\InterfacePromoMechanism;

/**
 * Class PositionTotalPriceGreaterDiscountShippingPrice
 * @package Lovata\OrdersShopaholic\Classes\PromoMechanism\PositionTotalPriceGreater
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class PositionTotalPriceGreaterDiscountShippingPrice extends AbstractPositionTotalPriceGreaterDiscount implements InterfacePromoMechanism
{
    const LANG_NAME = 'lovata.ordersshopaholic::lang.promo_mechanism_type.position_total_price_greater_discount_shipping_price';

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