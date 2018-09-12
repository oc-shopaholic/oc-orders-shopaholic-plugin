<?php namespace Lovata\OrdersShopaholic\Classes\PromoMechanism;

/**
 * Class AbstractDiscountTotalPrice
 * @package Lovata\OrdersShopaholic\Classes\PromoMechanism
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
abstract class AbstractDiscountTotalPrice extends AbstractPromoMechanism implements InterfacePromoMechanism
{
    const TYPE = 'total';

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