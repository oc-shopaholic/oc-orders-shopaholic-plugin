<?php namespace Lovata\OrdersShopaholic\Classes\PromoMechanism;

use Lang;
use Lovata\Toolbox\Classes\Helper\PriceHelper;
use Lovata\OrdersShopaholic\Models\PromoMechanism;
use Lovata\Shopaholic\Classes\Helper\TaxHelper;
use Lovata\Shopaholic\Models\Settings;

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

    const DISCOUNT_FROM_PRICE = 'discount_from_backend_price';
    const DISCOUNT_FROM_PRICE_WITHOUT_TAX = 'discount_from_price_without_tax';
    const DISCOUNT_FROM_PRICE_WITH_TAX = 'discount_from_price_with_tax';

    protected $iPriority;
    protected $fDiscountValue;
    protected $sDiscountType;
    protected $bIsFinalDiscount;
    protected $arPropertyList;
    protected $sRelatedDescription;
    protected $bIncrease = false;

    protected $bWithQuantityLimit = false;
    protected $bCalculatePerUnit = false;
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
     * @param bool   $bIncrease
     */
    public function __construct($iPriority, $fDiscountValue, $sDiscountType, $bIsFinalDiscount, $arPropertyList, $bIncrease)
    {
        $this->iPriority = (int) $iPriority;
        $this->fDiscountValue = PriceHelper::toFloat($fDiscountValue);
        $this->sDiscountType = $sDiscountType;
        $this->bIsFinalDiscount = (bool) $bIsFinalDiscount;
        $this->arPropertyList = (array) $arPropertyList;
        $this->bIncrease = (bool) $bIncrease;
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
     * Return true, if discount is final
     * @return bool
     */
    public function getIncreaseFlag() : bool
    {
        return $this->bIncrease;
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
     * @param \Model $obModel
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
     * Calculate item discount and add discount to price container
     * @param ItemPriceContainer                                     $obPriceContainer
     * @param AbstractPromoMechanismProcessor                        $obProcessor
     * @param \Lovata\OrdersShopaholic\Classes\Item\CartPositionItem $obPosition
     * @return ItemPriceContainer
     */
    public function calculateItemDiscount($obPriceContainer, $obProcessor, $obPosition)
    {
        $this->bApplied = false;
        if (!$this->check($obProcessor, $obPosition)) {
            return $obPriceContainer;
        }

        $this->bApplied = true;

        $iQuantityLimit = (int) $this->getProperty('quantity_limit');
        $iQuantityLimitFrom = (int) $this->getProperty('quantity_limit_from');
        $bPriceIncludeTax = TaxHelper::instance()->isPriceIncludeTax();
        $sFormulaCalculationDiscount = Settings::getValue('formula_calculate_discount_from_price', self::DISCOUNT_FROM_PRICE);

        //Get unit price list
        $arUnitPriceList = $obPriceContainer->getUnitPriceList();

        foreach ($arUnitPriceList as $iKey => &$fPrice) {

            if ($iQuantityLimitFrom > 0 && $iQuantityLimit > 0 && $iQuantityLimitFrom > $iQuantityLimit) {
                $bSkipPrice = $this->bWithQuantityLimit && $iKey % $iQuantityLimitFrom >= $iQuantityLimit;
                if ($bSkipPrice) {
                    continue;
                }
            }

            //Prepare price, before applying discount
            if ($bPriceIncludeTax && $sFormulaCalculationDiscount == self::DISCOUNT_FROM_PRICE_WITHOUT_TAX) {
                $fPrice = TaxHelper::instance()->calculatePriceWithoutTax($fPrice, $obPriceContainer->tax_percent);
            } else if (!$bPriceIncludeTax && $sFormulaCalculationDiscount == self::DISCOUNT_FROM_PRICE_WITH_TAX) {
                $fPrice = TaxHelper::instance()->calculatePriceWithTax($fPrice, $obPriceContainer->tax_percent);
            }

            $fPrice = $this->applyDiscount($fPrice);

            //Calculate price after applying the discount
            if ($bPriceIncludeTax && $sFormulaCalculationDiscount == self::DISCOUNT_FROM_PRICE_WITHOUT_TAX) {
                $fPrice = TaxHelper::instance()->calculatePriceWithTax($fPrice, $obPriceContainer->tax_percent);
            } else if (!$bPriceIncludeTax && $sFormulaCalculationDiscount == self::DISCOUNT_FROM_PRICE_WITH_TAX) {
                $fPrice = TaxHelper::instance()->calculatePriceWithoutTax($fPrice, $obPriceContainer->tax_percent);
            }
        }

        $obPriceContainer->addDiscount($arUnitPriceList, $this);

        return $obPriceContainer;
    }

    /**
     * Calculate total discount and add discount to price container
     * @param TotalPriceContainer             $obPriceContainer
     * @param AbstractPromoMechanismProcessor $obProcessor
     * @return TotalPriceContainer
     */
    public function calculateTotalDiscount($obPriceContainer, $obProcessor)
    {
        $this->bApplied = false;
        if (!$this->check($obProcessor)) {
            return $obPriceContainer;
        }

        $this->bApplied = true;
        if ($obPriceContainer->price_value == 0) {
            return $obPriceContainer;
        }

        $sFormulaCalculationDiscount = Settings::getValue('formula_calculate_discount_from_price', self::DISCOUNT_FROM_PRICE);
        switch ($sFormulaCalculationDiscount) {
            case self::DISCOUNT_FROM_PRICE_WITHOUT_TAX:
                $fPrice = $obPriceContainer->price_without_tax_value;
                $fPrice = $this->applyDiscount($fPrice);
                $obPriceContainer->addDiscountFromPriceWithoutTax($fPrice, $this);
                break;
            case self::DISCOUNT_FROM_PRICE_WITH_TAX:
                $fPrice = $obPriceContainer->price_with_tax_value;
                $fPrice = $this->applyDiscount($fPrice);
                $obPriceContainer->addDiscountFromPriceWithTax($fPrice, $this);
                break;
            default:
                $fPrice = $obPriceContainer->price_value;
                $fPrice = $this->applyDiscount($fPrice);
                $obPriceContainer->addDiscountFromPrice($fPrice, $this);
        }

        return $obPriceContainer;
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
            $iRepeatCount = floor($iQuantity / $iQuantityLimitFrom);
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
        if ($this->bIncrease) {
            $fPrice = PriceHelper::round($fPrice + $this->fDiscountValue);
        } else {
            $fPrice = PriceHelper::round($fPrice - $this->fDiscountValue);
            if ($fPrice < 0) {
                $fPrice = 0;
            }
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
        if ($this->bIncrease) {
            $fPrice = PriceHelper::round($fPrice + $fPrice * ($this->fDiscountValue / 100));
        } else {
            $fPrice = PriceHelper::round($fPrice - $fPrice * ($this->fDiscountValue / 100));
        }

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
    protected function check($obProcessor, $obPosition = null) : bool
    {
        $obShippingType = $obProcessor->getShippingType();
        $arShippingTypeList = $this->getProperty('shipping_type_id');
        $bFail = !empty($arShippingTypeList) && is_array($arShippingTypeList) && (empty($obShippingType) || (!empty($obShippingType)) && !in_array($obShippingType->id, $arShippingTypeList));
        if ($bFail) {
            return false;
        }

        $obPaymentMethod = $obProcessor->getPaymentMethod();
        $arPaymentMethodList = $this->getProperty('payment_method_id');
        $bFail = !empty($arPaymentMethodList) && is_array($arPaymentMethodList) && (empty($obPaymentMethod) || (!empty($obPaymentMethod)) && !in_array($obPaymentMethod->id, $arPaymentMethodList));
        if ($bFail) {
            return false;
        }

        return true;
    }
}