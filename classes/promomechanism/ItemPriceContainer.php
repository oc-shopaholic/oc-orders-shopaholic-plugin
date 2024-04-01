<?php namespace Lovata\OrdersShopaholic\Classes\PromoMechanism;

use Lovata\Toolbox\Classes\Helper\PriceHelper;
use Lovata\Shopaholic\Classes\Helper\TaxHelper;

/**
 * Class ItemPriceContainer
 * @package Lovata\OrdersShopaholic\Classes\PromoMechanism
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 *
 * @property string                        $price
 * @property float                         $price_value
 * @property string                        $tax_price
 * @property float                         $tax_price_value
 * @property string                        $price_without_tax
 * @property float                         $price_without_tax_value
 * @property string                        $price_with_tax
 * @property float                         $price_with_tax_value
 *
 * @property string                        $old_price
 * @property float                         $old_price_value
 * @property string                        $tax_old_price
 * @property float                         $tax_old_price_value
 * @property string                        $old_price_without_tax
 * @property float                         $old_price_without_tax_value
 * @property string                        $old_price_with_tax
 * @property float                         $old_price_with_tax_value
 *
 * @property string                        $discount_price
 * @property float                         $discount_price_value
 * @property string                        $tax_discount_price
 * @property float                         $tax_discount_price_value
 * @property string                        $discount_price_without_tax
 * @property float                         $discount_price_without_tax_value
 * @property string                        $discount_price_with_tax
 * @property float                         $discount_price_with_tax_value
 *
 * @property string                        $increase_price
 * @property float                         $increase_price_value
 * @property string                        $tax_increase_price
 * @property float                         $tax_increase_price_value
 * @property string                        $increase_price_without_tax
 * @property float                         $increase_price_without_tax_value
 * @property string                        $increase_price_with_tax
 * @property float                         $increase_price_with_tax_value
 *
 * @property string                        $price_per_unit
 * @property float                         $price_per_unit_value
 * @property string                        $tax_price_per_unit
 * @property float                         $tax_price_per_unit_value
 * @property string                        $price_per_unit_without_tax
 * @property float                         $price_per_unit_without_tax_value
 * @property string                        $price_per_unit_with_tax
 * @property float                         $price_per_unit_with_tax_value
 *
 * @property string                        $old_price_per_unit
 * @property float                         $old_price_per_unit_value
 * @property string                        $tax_old_price_per_unit
 * @property float                         $tax_old_price_per_unit_value
 * @property string                        $old_price_per_unit_without_tax
 * @property float                         $old_price_per_unit_without_tax_value
 * @property string                        $old_price_per_unit_with_tax
 * @property float                         $old_price_per_unit_with_tax_value
 *
 * @property string                        $discount_price_per_unit
 * @property float                         $discount_price_per_unit_value
 * @property string                        $tax_discount_price_per_unit
 * @property float                         $tax_discount_price_per_unit_value
 * @property string                        $discount_price_per_unit_without_tax
 * @property float                         $discount_price_per_unit_without_tax_value
 * @property string                        $discount_price_per_unit_with_tax
 * @property float                         $discount_price_per_unit_with_tax_value
 *
 * @property string                        $increase_price_per_unit
 * @property float                         $increase_price_per_unit_value
 * @property string                        $tax_increase_price_per_unit
 * @property float                         $tax_increase_price_per_unit_value
 * @property string                        $increase_price_per_unit_without_tax
 * @property float                         $increase_price_per_unit_without_tax_value
 * @property string                        $increase_price_per_unit_with_tax
 * @property float                         $increase_price_per_unit_with_tax_value
 *
 * @property float                         $tax_percent
 * @property int                           $quantity
 * @property array|ItemPriceContainerLog[] $log
 */
class ItemPriceContainer
{
    protected $iQuantity = 0;
    protected $fOldPricePerUnit = 0;
    protected $fTaxPercent = 0;
    protected $arLogData = [];
    protected $arUnitPriceList = [];

    protected $arAvailableFieldList = [
        'price',
        'tax_price',
        'price_without_tax',
        'price_with_tax',

        'old_price',
        'tax_old_price',
        'old_price_without_tax',
        'old_price_with_tax',

        'discount_price',
        'tax_discount_price',
        'discount_price_without_tax',
        'discount_price_with_tax',

        'increase_price',
        'tax_increase_price',
        'increase_price_without_tax',
        'increase_price_with_tax',

        'price_per_unit',
        'tax_price_per_unit',
        'price_per_unit_without_tax',
        'price_per_unit_with_tax',

        'old_price_per_unit',
        'tax_old_price_per_unit',
        'old_price_per_unit_without_tax',
        'old_price_per_unit_with_tax',

        'discount_price_per_unit',
        'tax_discount_price_per_unit',
        'discount_price_per_unit_without_tax',
        'discount_price_per_unit_with_tax',

        'increase_price_per_unit',
        'tax_increase_price_per_unit',
        'increase_price_per_unit_without_tax',
        'increase_price_per_unit_with_tax',
    ];

    /**
     * @return ItemPriceContainer
     */
    public static function makeEmpty()
    {
        return new ItemPriceContainer(0, 0, 0);
    }

    /**
     * PriceData constructor.
     * @param float $fPricePerUnit
     * @param float $fOldPricePerUnit
     * @param float $fTaxPercent
     * @param int   $iQuantity
     */
    public function __construct($fPricePerUnit, $fOldPricePerUnit, $fTaxPercent, $iQuantity = 1)
    {
        $this->fOldPricePerUnit = PriceHelper::toFloat($fOldPricePerUnit);
        $this->fTaxPercent = PriceHelper::toFloat($fTaxPercent);
        $this->iQuantity = $iQuantity;
        for ($iCount = 0; $iCount < $iQuantity; $iCount++) {
            $this->arUnitPriceList[] = PriceHelper::toFloat($fPricePerUnit);
        }
    }

    /**
     * @param string $sField
     * @return mixed
     */
    public function __get($sField)
    {
        //Fin getter method
        $sFieldName = studly_case($sField);
        $sMethodName = 'get'.$sFieldName.'Attribute';
        if (method_exists($this, $sMethodName)) {
            return $this->$sMethodName();
        }

        //Add to field name _value prefix
        if (!preg_match('%^.+_value$%', $sField) && in_array($sField, $this->arAvailableFieldList)) {
            $sField .= '_value';
            return PriceHelper::format($this->$sField);
        }

        //Check field name has _with_tax prefix
        if (preg_match('%^.+_with_tax_value$%', $sField)) {
            $sOriginField = str_replace('_with_tax', '', $sField);
            $fPrice = TaxHelper::instance()->getPriceWithTax($this->$sOriginField, $this->tax_percent);

            return $fPrice;
        }

        //Check field name has _without_tax prefix
        if (preg_match('%^.+_without_tax_value$%', $sField)) {
            $sOriginField = str_replace('_without_tax', '', $sField);
            $fPrice = TaxHelper::instance()->getPriceWithoutTax($this->$sOriginField, $this->tax_percent);

            return $fPrice;
        }

        //Check field name has tax_ suffix
        if (preg_match('%^tax_.+_value$%', $sField)) {
            $sOriginField = str_replace('tax_', '', $sField);
            $sWithTaxField = str_replace('_value', '_with_tax_value', $sOriginField);
            $sWithoutTaxField = str_replace('_value', '_without_tax_value', $sOriginField);

            $fPrice = PriceHelper::round($this->$sWithTaxField - $this->$sWithoutTaxField);

            return $fPrice;
        }

        return null;
    }

    /**
     * @param string $sMethod
     * @param array  $arParamLst
     * @return mixed
     */
    public function __call($sMethod, $arParamLst)
    {
        return $this->$sMethod;
    }

    /**
     * Add price data
     * @param array $arAdditionResult
     * @return array
     */
    public function getData($arAdditionResult = [])
    {
        $arResult = [];
        foreach ($this->arAvailableFieldList as $sField) {
            $arResult[$sField] = $this->$sField;
            $sField = $sField.'_value';
            $arResult[$sField] = $this->$sField;
        }

        $arResult['log'] = [];

        if (!empty($arAdditionResult) && is_array($arAdditionResult)) {
            $arResult = array_merge($arResult, $arAdditionResult);
        }

        if (empty($this->arLogData)) {
            return $arResult;
        }

        foreach ($this->arLogData as $obPriceData) {
            $arResult['log'][] = $obPriceData->getData();
        }

        return $arResult;
    }

    /**
     * @param array                   $arUnitPriceList
     * @param InterfacePromoMechanism $obMechanism
     */
    public function addDiscount($arUnitPriceList, $obMechanism)
    {
        if ($arUnitPriceList == $this->arUnitPriceList) {
            return;
        }

        $this->addLogData($arUnitPriceList, $this->arUnitPriceList, $obMechanism);
        $this->arUnitPriceList = $arUnitPriceList;
    }

    /**
     * Get array with unit price list
     * @return array
     */
    public function getUnitPriceList()
    {
        rsort($this->arUnitPriceList);
        return array_values($this->arUnitPriceList);
    }

    /**
     * Get log data
     * @param array                   $arNewUnitPriceList
     * @param array                   $arUnitPriceList
     * @param InterfacePromoMechanism $obMechanism
     */
    protected function addLogData($arNewUnitPriceList, $arUnitPriceList, $obMechanism = null)
    {
        $this->arLogData[] = new ItemPriceContainerLog($arNewUnitPriceList, $arUnitPriceList, $this->fTaxPercent, $this->iQuantity, $obMechanism);
    }

    /**
     * @return float|int
     */
    protected function getTaxPercentAttribute()
    {
        return $this->fTaxPercent;
    }

    /**
     * @return float|int
     */
    protected function getPriceValueAttribute()
    {
        $fPrice = 0;
        foreach ($this->arUnitPriceList as $fUnitPrice) {
            $fPrice += $fUnitPrice;
        }

        $fPrice = PriceHelper::round($fPrice);

        return $fPrice;
    }

    /**
     * @return float|int
     */
    protected function getOldPriceValueAttribute()
    {
        $fPrice = PriceHelper::round($this->fOldPricePerUnit * $this->iQuantity);

        return $fPrice;
    }

    /**
     * @return float|int
     */
    protected function getDiscountPriceValueAttribute()
    {
        $fPrice = PriceHelper::round($this->old_price_value - $this->price_value);

        return $fPrice;
    }

    /**
     * @return float|int
     */
    protected function getIncreasePriceValueAttribute()
    {
        $fPrice = PriceHelper::round($this->price_value - $this->old_price_value);

        return $fPrice;
    }

    /**
     * Calculate price per unit
     * @return float|int
     */
    protected function getPricePerUnitValueAttribute()
    {
        return PriceHelper::round($this->price_value / $this->iQuantity);
    }

    /**
     * @return float|int
     */
    protected function getOldPricePerUnitValueAttribute()
    {
        return $this->fOldPricePerUnit;
    }

    /**
     * @return float|int
     */
    protected function getDiscountPricePerUnitValueAttribute()
    {
        return PriceHelper::round($this->old_price_per_unit_value - $this->price_per_unit_value);
    }

    /**
     * @return float|int
     */
    protected function getIncreasePricePerUnitValueAttribute()
    {
        return PriceHelper::round($this->price_per_unit_value - $this->old_price_per_unit_value);
    }

    /**
     * @return int
     */
    protected function getQuantityAttribute()
    {
        return $this->iQuantity;
    }

    /**
     * @return array|ItemPriceContainerLog[]
     */
    protected function getLogAttribute()
    {
        return $this->arLogData;
    }
}
