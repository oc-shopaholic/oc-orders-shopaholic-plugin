<?php namespace Lovata\OrdersShopaholic\Classes\PromoMechanism\PositionTotalPriceGreater;

use Lovata\Toolbox\Classes\Helper\PriceHelper;
use Lovata\OrdersShopaholic\Classes\PromoMechanism\InterfacePromoMechanism;
use Lovata\OrdersShopaholic\Classes\PromoMechanism\AbstractPromoMechanism;

/**
 * Class AbstractPositionTotalPriceGreaterDiscount
 * @package Lovata\OrdersShopaholic\Classes\PromoMechanism\PositionTotalPriceGreater
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
abstract class AbstractPositionTotalPriceGreaterDiscount extends AbstractPromoMechanism implements InterfacePromoMechanism
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

        //Get amount value
        $fAmount = PriceHelper::toFloat($this->getProperty('amount'));
        if ($fAmount >= 0 && $obProcessor->getPositionTotalPrice()->price_value < $fAmount) {
            return false;
        }

        return true;
    }
}