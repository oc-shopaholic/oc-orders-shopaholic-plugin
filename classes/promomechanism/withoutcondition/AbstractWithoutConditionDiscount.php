<?php namespace Lovata\OrdersShopaholic\Classes\PromoMechanism\WithoutCondition;

use Lovata\OrdersShopaholic\Classes\PromoMechanism\InterfacePromoMechanism;
use Lovata\OrdersShopaholic\Classes\PromoMechanism\AbstractPromoMechanism;

/**
 * Class AbstractWithoutConditionDiscount
 * @package Lovata\OrdersShopaholic\Classes\PromoMechanism\WithoutCondition
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
abstract class AbstractWithoutConditionDiscount extends AbstractPromoMechanism implements InterfacePromoMechanism
{
    /**
     * Check discount condition
     * @param \Lovata\OrdersShopaholic\Classes\PromoMechanism\AbstractPromoMechanismProcessor $obProcessor
     * @param \Lovata\OrdersShopaholic\Classes\Item\CartPositionItem $obPosition
     * @return bool
     */
    protected function check($obProcessor, $obPosition = null) : bool
    {
        if (!parent::check($obProcessor, $obPosition)) {
            return false;
        }

        return true;
    }
}