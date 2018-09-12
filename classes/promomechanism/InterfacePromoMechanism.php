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

    /**
     * Get mechanism type (position|total_position|shipping|total)
     * @return string
     */
    public static function getType() : string;

    /**
     * Get priority value
     * @return int
     */
    public function getPriority() : int;

    /**
     * Return true, is discount was applied
     * @return int
     */
    public function isApplied() : bool;

    /**
     * Return true, if discount is final
     * @return int
     */
    public function isFinal() : bool;

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
}