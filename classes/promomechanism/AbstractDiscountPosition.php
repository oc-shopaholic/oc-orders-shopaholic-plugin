<?php namespace Lovata\OrdersShopaholic\Classes\PromoMechanism;

/**
 * Class AbstractDiscountPosition
 * @package Lovata\OrdersShopaholic\Classes\PromoMechanism
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
abstract class AbstractDiscountPosition extends AbstractPromoMechanism implements InterfacePromoMechanism
{
    const TYPE = 'position';

    /** @var callable */
    protected $callbackFunction;

    /**
     * Set callback function to check position
     * @param callable $callbackFunction
     */
    public function setCheckCallback($callbackFunction)
    {
        $this->callbackFunction = $callbackFunction;
    }

    /**
     * @param \Lovata\OrdersShopaholic\Classes\Item\CartPositionItem|\Lovata\OrdersShopaholic\Models\OrderPosition $obPosition
     * @param float $fPrice
     * @return float
     */
    public function calculate($obPosition, $fPrice)
    {
        if (!$this->check($obPosition)) {
            return $fPrice;
        }

        $this->bApplied = true;
        $fPrice = $this->applyDiscount($fPrice);

        return $fPrice;
    }

    /**
     * Check position is available for discount
     * @param \Lovata\OrdersShopaholic\Classes\Item\CartPositionItem $obPosition
     * @return bool
     */
    protected function check($obPosition)
    {
        if (empty($this->callbackFunction)) {
            return true;
        }

        return call_user_func($this->callbackFunction, $obPosition);
    }
}