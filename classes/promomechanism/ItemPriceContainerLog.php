<?php namespace Lovata\OrdersShopaholic\Classes\PromoMechanism;

use Lovata\Toolbox\Classes\Helper\PriceHelper;
use Lovata\Shopaholic\Classes\Helper\TaxHelper;

/**
 * Class ItemPriceContainerLog
 * @package Lovata\OrdersShopaholic\Classes\PromoMechanism
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 *
 * @property string                  $price
 * @property float                   $price_value
 * @property string                  $tax_price
 * @property float                   $tax_price_value
 * @property string                  $price_without_tax
 * @property float                   $price_without_tax_value
 * @property string                  $price_with_tax
 * @property float                   $price_with_tax_value
 *
 * @property string                  $old_price
 * @property float                   $old_price_value
 * @property string                  $tax_old_price
 * @property float                   $tax_old_price_value
 * @property string                  $old_price_without_tax
 * @property float                   $old_price_without_tax_value
 * @property string                  $old_price_with_tax
 * @property float                   $old_price_with_tax_value
 *
 * @property string                  $discount_price
 * @property float                   $discount_price_value
 * @property string                  $tax_discount_price
 * @property float                   $tax_discount_price_value
 * @property string                  $discount_price_without_tax
 * @property float                   $discount_price_without_tax_value
 * @property string                  $discount_price_with_tax
 * @property float                   $discount_price_with_tax_value
 *
 * @property string                  $increase_price
 * @property float                   $increase_price_value
 * @property string                  $tax_increase_price
 * @property float                   $tax_increase_price_value
 * @property string                  $increase_price_without_tax
 * @property float                   $increase_price_without_tax_value
 * @property string                  $increase_price_with_tax
 * @property float                   $increase_price_with_tax_value
 *
 * @property string                  $price_per_unit
 * @property float                   $price_per_unit_value
 * @property string                  $tax_price_per_unit
 * @property float                   $tax_price_per_unit_value
 * @property string                  $price_per_unit_without_tax
 * @property float                   $price_per_unit_without_tax_value
 * @property string                  $price_per_unit_with_tax
 * @property float                   $price_per_unit_with_tax_value
 *
 * @property string                  $old_price_per_unit
 * @property float                   $old_price_per_unit_value
 * @property string                  $tax_old_price_per_unit
 * @property float                   $tax_old_price_per_unit_value
 * @property string                  $old_price_per_unit_without_tax
 * @property float                   $old_price_per_unit_without_tax_value
 * @property string                  $old_price_per_unit_with_tax
 * @property float                   $old_price_per_unit_with_tax_value
 *
 * @property string                  $discount_price_per_unit
 * @property float                   $discount_price_per_unit_value
 * @property string                  $tax_discount_price_per_unit
 * @property float                   $tax_discount_price_per_unit_value
 * @property string                  $discount_price_per_unit_without_tax
 * @property float                   $discount_price_per_unit_without_tax_value
 * @property string                  $discount_price_per_unit_with_tax
 * @property float                   $discount_price_per_unit_with_tax_value
 *
 * @property string                  $increase_price_per_unit
 * @property float                   $increase_price_per_unit_value
 * @property string                  $tax_increase_price_per_unit
 * @property float                   $tax_increase_price_per_unit_value
 * @property string                  $increase_price_per_unit_without_tax
 * @property float                   $increase_price_per_unit_without_tax_value
 * @property string                  $increase_price_per_unit_with_tax
 * @property float                   $increase_price_per_unit_with_tax_value
 *
 * @property float                   $tax_percent
 * @property int                     $quantity
 * @property InterfacePromoMechanism $mechanism
 */
class ItemPriceContainerLog
{
    protected $iQuantity = 0;
    protected $fTaxPercent = 0;
    protected $arUnitPriceList = [];
    protected $arOldUnitPriceList = [];

    /** @var InterfacePromoMechanism */
    protected $obMechanism;

    /**
     * Get log data
     * @param array                   $arNewUnitPriceList
     * @param array                   $arUnitPriceList
     * @param float                   $fTaxPercent
     * @param int                     $iQuantity
     * @param InterfacePromoMechanism $obMechanism
     */
    public function __construct($arNewUnitPriceList, $arUnitPriceList, $fTaxPercent, $iQuantity, $obMechanism = null)
    {
        $this->iQuantity = $iQuantity;
        $this->fTaxPercent = $fTaxPercent;
        $this->arUnitPriceList = $arNewUnitPriceList;
        $this->arOldUnitPriceList = $arUnitPriceList;
        $this->obMechanism = $obMechanism;
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
        if (!preg_match('%^.+_value$%', $sField)) {
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
     * Get log data
     * @return array
     */
    public function getData()
    {
        $arResult = [
            'price'                   => $this->price,
            'price_value'             => $this->price_value,
            'tax_price'               => $this->tax_price,
            'tax_price_value'         => $this->tax_price_value,
            'price_without_tax'       => $this->price_without_tax,
            'price_without_tax_value' => $this->price_without_tax_value,
            'price_with_tax'          => $this->price_with_tax,
            'price_with_tax_value'    => $this->price_with_tax_value,

            'old_price'                   => $this->old_price,
            'old_price_value'             => $this->old_price_value,
            'tax_old_price'               => $this->tax_old_price,
            'tax_old_price_value'         => $this->tax_old_price_value,
            'old_price_without_tax'       => $this->old_price_without_tax,
            'old_price_without_tax_value' => $this->old_price_without_tax_value,
            'old_price_with_tax'          => $this->old_price_with_tax,
            'old_price_with_tax_value'    => $this->old_price_with_tax_value,

            'discount_price'                   => $this->discount_price,
            'discount_price_value'             => $this->discount_price_value,
            'tax_discount_price'               => $this->tax_discount_price,
            'tax_discount_price_value'         => $this->tax_discount_price_value,
            'discount_price_without_tax'       => $this->discount_price_without_tax,
            'discount_price_without_tax_value' => $this->discount_price_without_tax_value,
            'discount_price_with_tax'          => $this->discount_price_with_tax,
            'discount_price_with_tax_value'    => $this->discount_price_with_tax_value,

            'increase_price'                   => $this->increase_price,
            'increase_price_value'             => $this->increase_price_value,
            'tax_increase_price'               => $this->tax_increase_price,
            'tax_increase_price_value'         => $this->tax_increase_price_value,
            'increase_price_without_tax'       => $this->increase_price_without_tax,
            'increase_price_without_tax_value' => $this->increase_price_without_tax_value,
            'increase_price_with_tax'          => $this->increase_price_with_tax,
            'increase_price_with_tax_value'    => $this->increase_price_with_tax_value,

            'price_per_unit'                   => $this->price_per_unit,
            'price_per_unit_value'             => $this->price_per_unit_value,
            'tax_price_per_unit'               => $this->tax_price_per_unit,
            'tax_price_per_unit_value'         => $this->tax_price_per_unit_value,
            'price_per_unit_without_tax'       => $this->price_per_unit_without_tax,
            'price_per_unit_without_tax_value' => $this->price_per_unit_without_tax_value,
            'price_per_unit_with_tax'          => $this->price_per_unit_with_tax,
            'price_per_unit_with_tax_value'    => $this->price_per_unit_with_tax_value,

            'old_price_per_unit'                   => $this->old_price_per_unit,
            'old_price_per_unit_value'             => $this->old_price_per_unit_value,
            'tax_old_price_per_unit'               => $this->tax_old_price_per_unit,
            'tax_old_price_per_unit_value'         => $this->tax_old_price_per_unit_value,
            'old_price_per_unit_without_tax'       => $this->old_price_per_unit_without_tax,
            'old_price_per_unit_without_tax_value' => $this->old_price_per_unit_without_tax_value,
            'old_price_per_unit_with_tax'          => $this->old_price_per_unit_with_tax,
            'old_price_per_unit_with_tax_value'    => $this->old_price_per_unit_with_tax_value,

            'discount_price_per_unit'                   => $this->discount_price_per_unit,
            'discount_price_per_unit_value'             => $this->discount_price_per_unit_value,
            'tax_discount_price_per_unit'               => $this->tax_discount_price_per_unit,
            'tax_discount_price_per_unit_value'         => $this->tax_discount_price_per_unit_value,
            'discount_price_per_unit_without_tax'       => $this->discount_price_per_unit_without_tax,
            'discount_price_per_unit_without_tax_value' => $this->discount_price_per_unit_without_tax_value,
            'discount_price_per_unit_with_tax'          => $this->discount_price_per_unit_with_tax,
            'discount_price_per_unit_with_tax_value'    => $this->discount_price_per_unit_with_tax_value,

            'increase_price_per_unit'                   => $this->increase_price_per_unit,
            'increase_price_per_unit_value'             => $this->increase_price_per_unit_value,
            'tax_increase_price_per_unit'               => $this->tax_increase_price_per_unit,
            'tax_increase_price_per_unit_value'         => $this->tax_increase_price_per_unit_value,
            'increase_price_per_unit_without_tax'       => $this->increase_price_per_unit_without_tax,
            'increase_price_per_unit_without_tax_value' => $this->increase_price_per_unit_without_tax_value,
            'increase_price_per_unit_with_tax'          => $this->increase_price_per_unit_with_tax,
            'increase_price_per_unit_with_tax_value'    => $this->increase_price_per_unit_with_tax_value,

            'description' => !empty($this->obMechanism) ? $this->obMechanism->getRelatedDescription() : '',
        ];

        return $arResult;
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
        $fPrice = 0;
        foreach ($this->arOldUnitPriceList as $fUnitPrice) {
            $fPrice += $fUnitPrice;
        }

        $fPrice = PriceHelper::round($fPrice);

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
        return PriceHelper::round($this->old_price_value / $this->iQuantity);
    }

    /**
     * @return float|int
     */
    protected function getIncreasePricePerUnitValueAttribute()
    {
        return PriceHelper::round($this->price_per_unit_value - $this->old_price_per_unit_value);
    }

    /**
     * @return float|int
     */
    protected function getDiscountPricePerUnitValueAttribute()
    {
        return PriceHelper::round($this->old_price_per_unit_value - $this->price_per_unit_value);
    }

    /**
     * @return InterfacePromoMechanism|null
     */
    public function getMechanismAttribute()
    {
        return $this->obMechanism;
    }
}