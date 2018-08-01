<?php namespace Lovata\OrdersShopaholic\Interfaces;

/**
 * Interface PaymentGatewayInterface
 * @package Lovata\OrdersShopaholic\Interfaces
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
interface PaymentGatewayInterface
{
    /**
     * @param \Lovata\OrdersShopaholic\Models\Order $obOrder
     */
    public function purchase($obOrder);

    /**
     * Return true, if response has redirect URL
     * @return bool
     */
    public function isRedirect() : bool;

    /**
     * Return true, if result of purchase is successful
     * @return bool
     */
    public function isSuccessful() : bool;

    /**
     * Get redirect URL
     * @return string
     */
    public function getRedirectURL() : string;

    /**
     * Get  response message
     * @return string
     */
    public function getMessage() : string;

    /**
     * Get response array
     * @return array
     */
    public function getResponse() : array;
}