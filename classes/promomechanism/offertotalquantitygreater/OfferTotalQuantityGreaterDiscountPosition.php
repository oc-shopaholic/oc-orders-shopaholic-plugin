<?php namespace Lovata\OrdersShopaholic\Classes\PromoMechanism\OfferTotalQuantityGreater;

use Lovata\OrdersShopaholic\Classes\PromoMechanism\InterfacePromoMechanism;

/**
 * Class OfferTotalQuantityGreaterDiscountPosition
 * @package Lovata\OrdersShopaholic\Classes\PromoMechanism\OfferTotalQuantityGreater
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class OfferTotalQuantityGreaterDiscountPosition extends AbstractOfferTotalQuantityGreaterDiscount implements InterfacePromoMechanism
{
    const LANG_NAME = 'lovata.ordersshopaholic::lang.promo_mechanism_type.offer_total_quantity_greater_discount_position';

    protected $bWithQuantityLimit = true;
    protected $bCalculatePerUnit = true;

    /**
     * Get discount type
     * @return string
     */
    public static function getType() : string
    {
        return self::TYPE_POSITION;
    }

    /**
     * Check position is available for discount
     * @param \Lovata\OrdersShopaholic\Classes\PromoMechanism\AbstractPromoMechanismProcessor $obProcessor
     * @param \Lovata\OrdersShopaholic\Classes\Item\CartPositionItem                          $obPosition
     * @return bool
     */
    protected function check($obProcessor, $obPosition = null) : bool
    {
        //Get offer limit value
        if (!$this->checkPosition($obPosition)) {
            return false;
        }

        return parent::check($obProcessor, $obPosition);
    }
}