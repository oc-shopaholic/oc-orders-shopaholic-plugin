<?php namespace Lovata\OrdersShopaholic\Classes\PromoMechanism\OfferQuantityGreater;

use Lovata\OrdersShopaholic\Classes\PromoMechanism\InterfacePromoMechanism;
use Lovata\OrdersShopaholic\Classes\PromoMechanism\AbstractPromoMechanism;

/**
 * Class OfferQuantityGreaterDiscountPosition
 * @package Lovata\OrdersShopaholic\Classes\PromoMechanism\OfferQuantityGreater
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class OfferQuantityGreaterDiscountPosition extends AbstractPromoMechanism implements InterfacePromoMechanism
{
    const LANG_NAME = 'lovata.ordersshopaholic::lang.promo_mechanism_type.offer_quantity_greater_discount_position';

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
        if (!parent::check($obProcessor, $obPosition)) {
            return false;
        }

        //Get offer limit value
        $iOfferLimit = (int) $this->getProperty('offer_limit');
        if (empty($iOfferLimit) || !$this->checkPosition($obPosition)) {
            return false;
        }

        $iQuantity = 0;

        //Get order position list
        $obPositionList = $obProcessor->getPositionList();

        /** @var \Lovata\OrdersShopaholic\Classes\Item\CartPositionItem|\Lovata\OrdersShopaholic\Models\OrderPosition $obPositionItem */
        foreach ($obPositionList as $obPositionItem) {
            if ($obPositionItem->item_id != $obPosition->item_id || $obPositionItem->item_type != $obPosition->item_type) {
                continue;
            }

            $iQuantity += $obPositionItem->quantity;
        }

        $bResult = $iQuantity >= $iOfferLimit;

        return $bResult;
    }
}