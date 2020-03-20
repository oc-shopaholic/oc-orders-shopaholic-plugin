<?php namespace Lovata\OrdersShopaholic\Classes\PromoMechanism;

/**
 * Interface InterfacePromoMechanism
 * @package Lovata\OrdersShopaholic\Classes\PromoMechanism
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
interface InterfacePromoMechanism
{

    /**
     * Get mechanism type (position|total_position|shipping|total)
     * @return string
     */
    public static function getType() : string;

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

    /**
     * Get priority value
     * @return int
     */
    public function getPriority() : int;

    /**
     * Return true, is discount was applied
     * @return bool
     */
    public function isApplied() : bool;

    /**
     * Return true, if discount is final
     * @return bool
     */
    public function isFinal() : bool;

    /**
     * Return value of increase flag
     * @return bool
     */
    public function getIncreaseFlag() : bool;

    /**
     * Get discount value
     * @return float
     */
    public function getDiscountValue();

    /**
     * Get discount type
     * @return float
     */
    public function getDiscountType();

    /**
     * Set related model
     * @param \Model $obModel
     */
    public function setRelatedModel($obModel);

    /**
     * Set related model
     * @param string $sDescription
     */
    public function setRelatedDescription($sDescription);

    /**
     * Get related model description
     * @return string
     */
    public function getRelatedDescription();

    /**
     * Get property value
     * @param string $sField
     * @return mixed
     */
    public function getProperty($sField);

    /**
     * Calculate item discount and add discount to price container
     * @param ItemPriceContainer                                                                                            $obPriceContainer
     * @param AbstractPromoMechanismProcessor                                                                               $obProcessor
     * @param \Lovata\OrdersShopaholic\Classes\Item\CartPositionItem|\Lovata\OrdersShopaholic\Classes\Item\ShippingTypeItem $obPosition
     * @return ItemPriceContainer
     */
    public function calculateItemDiscount($obPriceContainer, $obProcessor, $obPosition);

    /**
     * Calculate total discount and add discount to price container
     * @param TotalPriceContainer             $obPriceContainer
     * @param AbstractPromoMechanismProcessor $obProcessor
     * @return ItemPriceContainer
     */
    public function calculateTotalDiscount($obPriceContainer, $obProcessor);

    /**
     * Set callback function to check position
     * @param callable $callbackFunction
     */
    public function setCheckPositionCallback($callbackFunction);

    /**
     * Set callback function to check shipping type
     * @param callable $callbackFunction
     */
    public function setCheckShippingTypeCallback($callbackFunction);
}
