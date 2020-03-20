<?php namespace Lovata\OrdersShopaholic\Classes\PromoMechanism\PositionCountGreater;

use Lovata\OrdersShopaholic\Classes\PromoMechanism\InterfacePromoMechanism;
use Lovata\OrdersShopaholic\Classes\PromoMechanism\AbstractPromoMechanism;

/**
 * Class AbstractPositionCountGreaterDiscount
 * @package Lovata\OrdersShopaholic\Classes\PromoMechanism\PositionCountGreater
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
abstract class AbstractPositionCountGreaterDiscount extends AbstractPromoMechanism implements InterfacePromoMechanism
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

        //Get position limit value
        $iPositionLimit = (int) $this->getProperty('position_limit');

        //Get order position list
        $obPositionList = $obProcessor->getPositionList();
        if ($obPositionList->isEmpty() || empty($iPositionLimit)) {
            return false;
        }

        $arPositionList = [];

        //Collect position count
        /** @var \Lovata\OrdersShopaholic\Classes\Item\CartPositionItem|\Lovata\OrdersShopaholic\Models\OrderPosition $obPositionItem */
        foreach ($obPositionList as $obPositionItem)
        {
            if (!$this->checkPosition($obPositionItem)) {
                continue;
            }

            $sKey = $obPositionItem->item_id.$obPositionItem->item_type;
            $arPositionList[] = $sKey;
        }

        $arPositionList = array_unique($arPositionList);

        $bResult = count($arPositionList) >= $iPositionLimit;

        return $bResult;
    }
}