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

    const TYPE_POSITION = 'position';
    const TYPE_POSITION_MIN_PRICE = 'min_price';
    const TYPE_TOTAL_POSITION = 'total_position';
    const TYPE_SHIPPING = 'shipping';
    const TYPE_TOTAL_PRICE = 'total';

    protected $iPriority;
    protected $fDiscountValue;
    protected $sDiscountType;
    protected $bIsFinalDiscount;
    protected $arPropertyList;
    protected $sRelatedDescription;

    protected $bWithQuantityLimit = false;
    protected $bApplied = false;

    /** @var \Lovata\CouponsShopaholic\Models\CouponGroup */
    protected $obRelatedModel;

    /** @var callable */
    protected $callbackCheckPosition;

    /** @var callable */
    protected $callbackCheckShippingType;

    /**
     * Get mechanism type (position|total_position|shipping|total)
     * @return string
     */
    abstract public static function getType() : string;

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
     * @return string
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
     * Get property value
     * @param string $sField
     * @return mixed
     */
    public function getProperty($sField)
    {
        return array_get($this->arPropertyList, $sField);
    }

    /**
     * Calculate new price value
     * @param float                                                  $fPrice
     * @param AbstractPromoMechanismProcessor                        $obProcessor
     * @param \Lovata\OrdersShopaholic\Classes\Item\CartPositionItem $obPosition
     * @return float
     */
    public function calculate($fPrice, $obProcessor, $obPosition = null)
    {
        $this->bApplied = false;
        if (!$this->check($obProcessor, $obPosition)) {
            return $fPrice;
        }

        $this->bApplied = true;

        if ($this->bWithQuantityLimit && !empty($obPosition)) {
            $fPrice = $this->applyQuantityLimitDiscount($fPrice, $obPosition->quantity);
        } else {
            $fPrice = $this->applyDiscount($fPrice);
        }

        return $fPrice;
    }

    /**
     * Set callback function to check position
     * @param callable $callbackFunction
     */
    public function setCheckPositionCallback($callbackFunction)
    {
        $this->callbackCheckPosition = $callbackFunction;
    }

    /**
     * Set callback function to check shipping type
     * @param callable $callbackFunction
     */
    public function setCheckShippingTypeCallback($callbackFunction)
    {
        $this->callbackCheckShippingType = $callbackFunction;
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
     * Apply discount
     * @param float $fPrice
     * @param int   $iQuantity
     * @return float
     */
    protected function applyQuantityLimitDiscount($fPrice, $iQuantity)
    {
        $iQuantityLimit = (int) $this->getProperty('quantity_limit');
        $iQuantityLimitFrom = (int) $this->getProperty('quantity_limit_from');
        if (!empty($iQuantityLimitFrom) && $iQuantityLimitFrom > $iQuantity) {
            return $fPrice;
        }


        if (!empty($iQuantityLimitFrom) && !empty($iQuantityLimit) && $iQuantityLimitFrom > $iQuantityLimit) {
            $iRepeatCount = floor($iQuantity/$iQuantityLimitFrom);
            $iQuantityLimit *= $iRepeatCount;
        }

        if (empty($fPrice) || empty($iQuantity) || empty($iQuantityLimit) || $iQuantityLimit >= $iQuantity) {
            return $this->applyDiscount($fPrice);
        }

        $fPricePerUnit = PriceHelper::round($fPrice / $iQuantity);
        $fResultPrice = PriceHelper::round($fPricePerUnit * ($iQuantity - $iQuantityLimit));
        $fPrice = PriceHelper::round($fPrice - $fResultPrice);

        if ($this->sDiscountType == PromoMechanism::FIXED_TYPE) {
            $fPrice = $this->applyFixedDiscount($fPrice);
        }

        $fPrice = $this->applyPercentDiscount($fPrice);

        $fResultPrice = PriceHelper::round($fPrice + $fResultPrice);

        return $fResultPrice;
    }

    /**
     * Apply fixed discount
     * @param float $fPrice
     * @return float
     */
    protected function applyFixedDiscount($fPrice)
    {
        $fPrice = PriceHelper::round($fPrice - $this->fDiscountValue);
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

    /**
     * Check position is available for discount
     * @param \Lovata\OrdersShopaholic\Classes\Item\CartPositionItem $obPosition
     * @return bool
     */
    protected function checkPosition($obPosition) : bool
    {
        if (empty($this->callbackCheckPosition)) {
            return true;
        }

        return (bool) call_user_func($this->callbackCheckPosition, $obPosition);
    }

    /**
     * Check shipping type is available for discount
     * @param \Lovata\OrdersShopaholic\Classes\Item\ShippingTypeItem $obShippingType
     * @return bool
     */
    protected function checkShippingType($obShippingType) : bool
    {
        if (empty($this->callbackCheckShippingType)) {
            return true;
        }

        return (bool) call_user_func($this->callbackCheckShippingType, $obShippingType);
    }

    /**
     * Check discount condition
     * @param AbstractPromoMechanismProcessor                        $obProcessor
     * @param \Lovata\OrdersShopaholic\Classes\Item\CartPositionItem $obPosition
     * @return bool
     */
    abstract protected function check($obProcessor, $obPosition = null) : bool;
}