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
        if (!preg_match('%^.+_value$%', $sField)) {
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

            'description' => !empty($this->obMechanism) ? $this->obMechanism->getRelatedDescription() : '',
        ];

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
