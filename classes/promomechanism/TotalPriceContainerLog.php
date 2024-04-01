<?php namespace Lovata\OrdersShopaholic\Classes\PromoMechanism;

use Lovata\Toolbox\Classes\Helper\PriceHelper;

/**
 * Class TotalPriceContainerLog
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
 * @property string                         $increase_price
 * @property float                          $increase_price_value
 * @property string                         $tax_increase_price
 * @property float                          $tax_increase_price_value
 * @property string                         $increase_price_without_tax
 * @property float                          $increase_price_without_tax_value
 * @property string                         $increase_price_with_tax
 * @property float                          $increase_price_with_tax_value
 *
 * @property InterfacePromoMechanism $mechanism
 */
class TotalPriceContainerLog
{
    protected $fPrice = 0;
    protected $fPriceWithoutTax = 0;
    protected $fPriceWithTax = 0;
    protected $fTaxPrice = 0;

    protected $fOldPrice = 0;
    protected $fOldPriceWithoutTax = 0;
    protected $fOldPriceWithTax = 0;
    protected $fTaxOldPrice = 0;

    protected $fDiscountPrice = 0;
    protected $fDiscountPriceWithoutTax = 0;
    protected $fDiscountPriceWithTax = 0;
    protected $fTaxDiscountPrice = 0;

    protected $fIncreasePrice = 0;
    protected $fIncreasePriceWithoutTax = 0;
    protected $fIncreasePriceWithTax = 0;
    protected $fTaxIncreasePrice = 0;

    /** @var InterfacePromoMechanism */
    protected $obMechanism;

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
    ];

    /**
     * Get log data
     * @param TotalPriceContainer     $obNewPriceData
     * @param TotalPriceContainer     $obPriceData
     * @param InterfacePromoMechanism $obMechanism
     */
    public function __construct($obNewPriceData, $obPriceData, $obMechanism = null)
    {
        $this->fPrice = $obNewPriceData->price_value;
        $this->fPriceWithoutTax = $obNewPriceData->price_without_tax_value;
        $this->fPriceWithTax = $obNewPriceData->price_with_tax_value;
        $this->fTaxPrice = $obNewPriceData->tax_price_value;

        $this->fOldPrice = $obPriceData->price_value;
        $this->fOldPriceWithoutTax = $obPriceData->price_without_tax_value;
        $this->fOldPriceWithTax = $obPriceData->price_with_tax_value;
        $this->fTaxOldPrice = $obPriceData->tax_price_value;

        $this->fDiscountPrice = PriceHelper::round($this->fOldPrice - $this->fPrice);
        $this->fDiscountPriceWithoutTax = PriceHelper::round($this->fOldPriceWithoutTax - $this->fPriceWithoutTax);
        $this->fDiscountPriceWithTax = PriceHelper::round($this->fOldPriceWithTax - $this->fPriceWithTax);
        $this->fTaxDiscountPrice = PriceHelper::round($this->fTaxOldPrice - $this->fTaxPrice);

        $this->fIncreasePrice = PriceHelper::round($this->fPrice - $this->fOldPrice);
        $this->fIncreasePriceWithoutTax = PriceHelper::round($this->fPriceWithoutTax - $this->fOldPriceWithoutTax);
        $this->fIncreasePriceWithTax = PriceHelper::round($this->fPriceWithTax - $this->fOldPriceWithTax);
        $this->fTaxIncreasePrice = PriceHelper::round($this->fTaxPrice - $this->fTaxOldPrice);

        $this->obMechanism = $obMechanism;
    }

    /**
     * @param string $sField
     * @return mixed
     */
    public function __get($sField)
    {
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

        $sClassField = 'f'.$sFieldName;
        $sClassField = str_replace('Value', '', $sClassField);
        if (property_exists($this, $sClassField)) {
            return $this->$sClassField;
        }

        return null;
    }

    /**
     * Get log data
     * @return array
     */
    public function getData()
    {
        $arResult = [];
        foreach ($this->arAvailableFieldList as $sField) {
            $arResult[$sField] = $this->$sField;
            $sField = $sField.'_value';
            $arResult[$sField] = $this->$sField;
        }

        $arResult['description'] = !empty($this->obMechanism) ? $this->obMechanism->getRelatedDescription() : '';

        return $arResult;
    }

    /**
     * @return InterfacePromoMechanism|null
     */
    public function getMechanismAttribute()
    {
        return $this->obMechanism;
    }
}
