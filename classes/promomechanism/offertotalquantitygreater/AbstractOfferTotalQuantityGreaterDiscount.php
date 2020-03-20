<?php namespace Lovata\OrdersShopaholic\Classes\PromoMechanism\OfferTotalQuantityGreater;

use Lovata\OrdersShopaholic\Classes\PromoMechanism\InterfacePromoMechanism;
use Lovata\OrdersShopaholic\Classes\PromoMechanism\AbstractPromoMechanism;

/**
 * Class AbstractOfferTotalQuantityGreaterDiscount
 * @package Lovata\OrdersShopaholic\Classes\PromoMechanism\OfferTotalQuantityGreater
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
abstract class AbstractOfferTotalQuantityGreaterDiscount extends AbstractPromoMechanism implements InterfacePromoMechanism
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

        $iTotalQuantity = 0;

        //Collect position quantity
        /** @var \Lovata\OrdersShopaholic\Classes\Item\CartPositionItem|\Lovata\OrdersShopaholic\Models\OrderPosition $obPositionItem */
        foreach ($obPositionList as $obPositionItem)
        {
            if (!$this->checkPosition($obPositionItem)) {
                continue;
            }

            $iTotalQuantity += $obPositionItem->quantity;
        }

        $bResult = $iTotalQuantity >= $iOfferLimit;

        return $bResult;
    }
}