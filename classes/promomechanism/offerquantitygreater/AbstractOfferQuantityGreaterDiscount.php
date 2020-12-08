<?php namespace Lovata\OrdersShopaholic\Classes\PromoMechanism\OfferQuantityGreater;

use Lovata\OrdersShopaholic\Classes\PromoMechanism\InterfacePromoMechanism;
use Lovata\OrdersShopaholic\Classes\PromoMechanism\AbstractPromoMechanism;

/**
 * Class AbstractOfferQuantityGreaterDiscount
 * @package Lovata\OrdersShopaholic\Classes\PromoMechanism\OfferQuantityGreater
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
abstract class AbstractOfferQuantityGreaterDiscount extends AbstractPromoMechanism implements InterfacePromoMechanism
{
    /**
     * Check discount condition
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

        //Get order position list
        $obPositionList = $obProcessor->getPositionList();
        if ($obPositionList->isEmpty() || empty($iOfferLimit)) {
            return false;
        }

        $arPositionQuantity = [];

        //Collect position quantity
        /** @var \Lovata\OrdersShopaholic\Classes\Item\CartPositionItem|\Lovata\OrdersShopaholic\Models\OrderPosition $obPositionItem */
        foreach ($obPositionList as $obPositionItem)
        {
            if (!$this->checkPosition($obPositionItem)) {
                continue;
            }

            $sKey = $obPositionItem->item_id.$obPositionItem->item_type;
            if (!isset($arPositionQuantity[$sKey])) {
                $arPositionQuantity[$sKey] = 0;
            }

            $arPositionQuantity[$sKey] += $obPositionItem->quantity;
        }

        //Process offer quantity, and return true, if quantity greater then limit
        foreach ($arPositionQuantity as $iQuantity) {
            if ($iQuantity < $iOfferLimit) {
                continue;
            }

            return true;
        }

        return false;
    }
}