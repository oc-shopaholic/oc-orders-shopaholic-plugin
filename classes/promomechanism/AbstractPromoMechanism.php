<?php namespace Lovata\OrdersShopaholic\Classes\PromoMechanism;

use Lang;
use Lovata\OrdersShopaholic\Models\PromoMechanism;
use Lovata\Toolbox\Classes\Helper\PriceHelper;

/**
 * Class AbstractPromoMechanism
 * @package Lovata\OrdersShopaholic\Classes\PromoMechanism
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
abstract class AbstractPromoMechanism implements InterfacePromoMechanism
{
    const LANG_NAME = '';
    const TYPE = '';

    const TYPE_POSITION = 'position';
    const TYPE_TOTAL_POSITION = 'total_position';
    const TYPE_SHIPPING = 'shipping';
    const TYPE_TOTAL_PRICE = 'total';

    protected $iPriority;
    protected $fDiscountValue;
    protected $sDiscountType;
    protected $bIsFinalDiscount;
    protected $arPropertyList;
    protected $sRelatedDescription;

    protected $bApplied = false;

    /** @var \Lovata\CouponsShopaholic\Models\CouponGroup */
    protected $obRelatedModel;

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

    /**
     * Get mechanism type (position|total_position|shipping|total)
     * @return string
     */
    public static function getType() : string
    {
        return static::TYPE;
    }

    /**
     * AbstractPromoMechanism constructor.
     * @param int    $iPriority
     * @param float  $fDiscountValue
     * @param string $sDiscountType
     * @param bool   $bIsFinalDiscount
     * @param array  $arPropertyList
     */
    public function __construct($iPriority, $fDiscountValue, $sDiscountType, $bIsFinalDiscount, $arPropertyList)
    {
        $this->iPriority = (int) $iPriority;
        $this->fDiscountValue = PriceHelper::toFloat($fDiscountValue);
        $this->sDiscountType = $sDiscountType;
        $this->bIsFinalDiscount = (bool) $bIsFinalDiscount;
        $this->arPropertyList = (array) $arPropertyList;
    }

    /**
     * Get priority value
     * @return int
     */
    public function getPriority() : int
    {
        return (int) $this->iPriority;
    }

    /**
     * Return true, is discount was applied
     * @return bool
     */
    public function isApplied() : bool
    {
        return (bool) $this->bApplied;
    }

    /**
     * Return true, if discount is final
     * @return bool
     */
    public function isFinal() : bool
    {
        return $this->bApplied && $this->bIsFinalDiscount;
    }

    /**
     * Get discount value
     * @return float
     */
    public function getDiscountValue()
    {
        return $this->fDiscountValue;
    }

    /**
     * Get discount type
     * @return float
     */
    public function getDiscountType()
    {
        return $this->sDiscountType;
    }

    /**
     * Set related model
     * @param  \Model $obModel
     */
    public function setRelatedModel($obModel)
    {
        $this->obRelatedModel = $obModel;
    }

    /**
     * Set related description
     * @param string $sDescription
     */
    public function setRelatedDescription($sDescription)
    {
        $this->sRelatedDescription = $sDescription;
    }

    /**
     * Get related description
     * @return string
     */
    public function getRelatedDescription()
    {
        return $this->sRelatedDescription;
    }

    /**
     * Apply discount
     * @param float $fPrice
     * @return float
     */
    protected function applyDiscount($fPrice)
    {
        if ($this->sDiscountType == PromoMechanism::FIXED_TYPE) {
            return $this->applyFixedDiscount($fPrice);
        }

        return $this->applyPercentDiscount($fPrice);
    }

    /**
     * Apply fixed discount
     * @param float $fPrice
     * @return float
     */
    protected function applyFixedDiscount($fPrice)
    {
        $fPrice = PriceHelper::round($fPrice- - $this->fDiscountValue);
        if ($fPrice < 0) {
            $fPrice = 0;
        }

        return $fPrice;
    }

    /**
     * Apply percent discount
     * @param float $fPrice
     * @return float
     */
    protected function applyPercentDiscount($fPrice)
    {
        $fPrice = PriceHelper::round($fPrice - $fPrice * ($this->fDiscountValue / 100));

        return $fPrice;
    }
}