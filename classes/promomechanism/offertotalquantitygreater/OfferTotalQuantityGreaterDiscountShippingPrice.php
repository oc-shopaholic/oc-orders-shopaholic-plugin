<?php namespace Lovata\OrdersShopaholic\Classes\PromoMechanism\OfferTotalQuantityGreater;

use Lovata\OrdersShopaholic\Classes\PromoMechanism\InterfacePromoMechanism;

/**
 * Class OfferTotalQuantityGreaterDiscountShippingPrice
 * @package Lovata\OrdersShopaholic\Classes\PromoMechanism\OfferTotalQuantityGreater
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class OfferTotalQuantityGreaterDiscountShippingPrice extends AbstractOfferTotalQuantityGreaterDiscount implements InterfacePromoMechanism
{
    const LANG_NAME = 'lovata.ordersshopaholic::lang.promo_mechanism_type.offer_total_quantity_greater_discount_shipping_price';

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
