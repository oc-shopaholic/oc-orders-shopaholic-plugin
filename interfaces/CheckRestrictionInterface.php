<?php namespace Lovata\OrdersShopaholic\Interfaces;

/**
 * Interface CheckRestrictionInterface
 * @package Lovata\OrdersShopaholic\Interfaces
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
interface CheckRestrictionInterface
{
    /**
     * CheckRestrictionInterface constructor.
     * @param \Lovata\OrdersShopaholic\Classes\Item\ShippingTypeItem $obShippingTypeItem
     * @param array                                                  $arData
     * @param array                                                  $arProperty
     * @param string                                                 $sCode
     */
    public function __construct($obShippingTypeItem, $arData, $arProperty, $sCode);

    /**
     * Get backend fields for restriction settings
     * @return array
     */
    public static function getFields() : array;

    /**
     * Check restriction of shipping type
     * @return bool
     */
    public function check() : bool;
}