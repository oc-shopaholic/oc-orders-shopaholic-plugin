<?php namespace Lovata\OrdersShopaholic\Interfaces;

/**
 * Interface ShippingPriceProcessorInterface
 * @package Lovata\OrdersShopaholic\Interfaces
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
interface ShippingPriceProcessorInterface
{
    /**
     * Get backend additional fields for shipping type form
     * @return array
     */
    public static function getFields() : array;

    /**
     * Returns true, if shipping fields has valid data
     * @return bool
     */
    public function validate() : bool;

    /**
     * Calculate shipping price with using API
     * @return float
     */
    public function getPrice() : float;

    /**
     * Get  response message
     * @return string
     */
    public function getMessage() : string;
}