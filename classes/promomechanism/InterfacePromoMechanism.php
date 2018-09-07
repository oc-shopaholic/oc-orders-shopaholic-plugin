<?php namespace Lovata\OrdersShopaholic\Classes\PromoMechanism;

/**
 * Interface InterfacePromoMechanism
 * @package Lovata\OrdersShopaholic\Classes\PromoMechanism
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
interface InterfacePromoMechanism
{
    /**
     * Get promo mechanism name (for backend)
     * @return string
     */
    public static function getName() : string;

    /**
     * Get promo mechanism condition description (for backend)
     * @return string
     */
    public static function getDescription() : string;
}