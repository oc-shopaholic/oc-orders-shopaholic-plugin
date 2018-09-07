<?php namespace Lovata\OrdersShopaholic\Classes\PromoMechanism;

use Lang;

/**
 * Class AbstractPromoMechanism
 * @package Lovata\OrdersShopaholic\Classes\PromoMechanism
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
abstract class AbstractPromoMechanism implements InterfacePromoMechanism
{
    const LANG_NAME = '';

    /**
     * Get promo mechanism name (for backend)
     * @return string
     */
    public static function getName() : string
    {
        return Lang::get(static::LANG_NAME);
    }

    /**
     * Get promo mechanism condition description (for backend)
     * @return string
     */
    public static function getDescription() : string
    {
        return Lang::get(static::LANG_NAME.'_description');
    }
}