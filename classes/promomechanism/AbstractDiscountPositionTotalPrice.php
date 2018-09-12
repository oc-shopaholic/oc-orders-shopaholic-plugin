<?php namespace Lovata\OrdersShopaholic\Classes\PromoMechanism;

/**
 * Class AbstractDiscountPositionTotalPrice
 * @package Lovata\OrdersShopaholic\Classes\PromoMechanism
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
abstract class AbstractDiscountPositionTotalPrice extends AbstractPromoMechanism implements InterfacePromoMechanism
{
    const TYPE = 'total_position';

    /**
     * @param float $fPrice
     * @return float
     */
    public function calculate($fPrice)
    {
        $this->bApplied = true;
        $fPrice = $this->applyDiscount($fPrice);

        return $fPrice;
    }
}