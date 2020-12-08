<?php namespace Lovata\OrdersShopaholic\Classes\PromoMechanism;

use Lovata\Toolbox\Classes\Helper\PriceHelper;

use Lovata\Shopaholic\Classes\Helper\TaxHelper;

/**
 * Class TotalPriceContainer
 * @package Lovata\OrdersShopaholic\Classes\PromoMechanism
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 *
 * @property string                         $price
 * @property float                          $price_value
 * @property string                         $tax_price
 * @property float                          $tax_price_value
 * @property string                         $price_without_tax
 * @property float                          $price_without_tax_value
 * @property string                         $price_with_tax
 * @property float                          $price_with_tax_value
 *
 * @property string                         $old_price
 * @property float                          $old_price_value
 * @property string                         $tax_old_price
 * @property float                          $tax_old_price_value
 * @property string                         $old_price_without_tax
 * @property float                          $old_price_without_tax_value
 * @property string                         $old_price_with_tax
 * @property float                          $old_price_with_tax_value
 *
 * @property string                         $discount_price
 * @property float                          $discount_price_value
 * @property string                         $tax_discount_price
 * @property float                          $tax_discount_price_value
 * @property string                         $discount_price_without_tax
 * @property float                          $discount_price_without_tax_value
 * @property string                         $discount_price_with_tax
 * @property float                          $discount_price_with_tax_value
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
 * @property array|TotalPriceContainerLog[] $log
 */
class TotalPriceContainer
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
    protected $arLogData = [];

    /**
     * @return TotalPriceContainer
     */
    public static function makeEmpty()
    {
        return new TotalPriceContainer();
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
     * @param string $sMethod
     * @param array  $arParamLst
     * @return mixed
     */
    public function __call($sMethod, $arParamLst)
    {
        return $this->$sMethod;
    }

    /**
     * Added prices from item price container
     * @param ItemPriceContainer|TotalPriceContainer $obPriceContainer
     */
    public function addPriceContainer($obPriceContainer)
    {
        $this->fPrice += $obPriceContainer->price_value;
        $this->fPriceWithoutTax += $obPriceContainer->price_without_tax_value;
        $this->fPriceWithTax += $obPriceContainer->price_with_tax_value;
        $this->fTaxPrice += $obPriceContainer->tax_price_value;

        $this->fOldPrice += $obPriceContainer->old_price_value;
        $this->fOldPriceWithoutTax += $obPriceContainer->old_price_without_tax_value;
        $this->fOldPriceWithTax += $obPriceContainer->old_price_with_tax_value;
        $this->fTaxOldPrice += $obPriceContainer->tax_old_price_value;

        $this->fDiscountPrice += $obPriceContainer->discount_price_value;
        $this->fDiscountPriceWithoutTax += $obPriceContainer->discount_price_without_tax_value;
        $this->fDiscountPriceWithTax += $obPriceContainer->discount_price_with_tax_value;
        $this->fTaxDiscountPrice += $obPriceContainer->tax_discount_price_value;

        $this->fIncreasePrice += $obPriceContainer->increase_price_value;
        $this->fIncreasePriceWithoutTax += $obPriceContainer->increase_price_without_tax_value;
        $this->fIncreasePriceWithTax += $obPriceContainer->increase_price_with_tax_value;
        $this->fTaxIncreasePrice += $obPriceContainer->tax_increase_price_value;
    }

    /**
     * Added prices from item price container
     * @param ItemPriceContainer|TotalPriceContainer $obPriceContainer
     */
    public function subPriceContainer($obPriceContainer)
    {
        $this->fPrice -= $obPriceContainer->price_value;
        $this->fPriceWithoutTax -= $obPriceContainer->price_without_tax_value;
        $this->fPriceWithTax -= $obPriceContainer->price_with_tax_value;
        $this->fTaxPrice -= $obPriceContainer->tax_price_value;

        $this->fOldPrice -= $obPriceContainer->old_price_value;
        $this->fOldPriceWithoutTax -= $obPriceContainer->old_price_without_tax_value;
        $this->fOldPriceWithTax -= $obPriceContainer->old_price_with_tax_value;
        $this->fTaxOldPrice -= $obPriceContainer->tax_old_price_value;

        $this->fDiscountPrice -= $obPriceContainer->discount_price_value;
        $this->fDiscountPriceWithoutTax -= $obPriceContainer->discount_price_without_tax_value;
        $this->fDiscountPriceWithTax -= $obPriceContainer->discount_price_with_tax_value;
        $this->fTaxDiscountPrice -= $obPriceContainer->tax_discount_price_value;

        $this->fIncreasePrice -= $obPriceContainer->increase_price_value;
        $this->fIncreasePriceWithoutTax -= $obPriceContainer->increase_price_without_tax_value;
        $this->fIncreasePriceWithTax -= $obPriceContainer->increase_price_with_tax_value;
        $this->fTaxIncreasePrice -= $obPriceContainer->tax_increase_price_value;
    }

    /**
     * Add price data
     * @param array $arAdditionResult
     * @return array
     */
    public function getData($arAdditionResult = [])
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

            'log' => [],
        ];

        $arResult['price'] = $this->price;
        $arResult['price_value'] = $this->price_value;
        $arResult['old_price'] = $this->old_price;
        $arResult['old_price_value'] = $this->old_price_value;
        $arResult['discount_price'] = $this->discount_price;
        $arResult['discount_price_value'] = $this->discount_price_value;
        $arResult['increase_price'] = $this->increase_price;
        $arResult['increase_price_value'] = $this->increase_price_value;
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
     * Add discount data from price field
     * @param float                   $fPrice
     * @param InterfacePromoMechanism $obMechanism
     */
    public function addDiscountFromPrice($fPrice, $obMechanism)
    {
        $fPrice = PriceHelper::toFloat($fPrice);
        $fTaxRate = PriceHelper::round($this->fPriceWithTax / $this->fPriceWithoutTax);

        if (TaxHelper::instance()->isPriceIncludeTax()) {
            $fPriceWithoutTax = PriceHelper::round($fPrice / $fTaxRate);
            $fPriceWithTax = $fPrice;
        } else {
            $fPriceWithoutTax = $fPrice;
            $fPriceWithTax = PriceHelper::round($fPrice * $fTaxRate);
        }

        $fTaxPrice = $fPriceWithTax - $fPriceWithoutTax;
        $obOldPriceData = clone $this;

        $this->fPrice = $fPrice;
        $this->fPriceWithoutTax = $fPriceWithoutTax;
        $this->fPriceWithTax = $fPriceWithTax;
        $this->fTaxPrice = $fTaxPrice;

        $this->fDiscountPrice = PriceHelper::round($this->fOldPrice - $this->fPrice);
        $this->fDiscountPriceWithoutTax = PriceHelper::round($this->fOldPriceWithoutTax - $this->fPriceWithoutTax);
        $this->fDiscountPriceWithTax = PriceHelper::round($this->fOldPriceWithTax - $this->fPriceWithTax);
        $this->fTaxDiscountPrice = PriceHelper::round($this->fTaxOldPrice - $this->fTaxPrice);

        $this->fIncreasePrice = PriceHelper::round($this->fPrice - $this->fOldPrice);
        $this->fIncreasePriceWithoutTax = PriceHelper::round($this->fPriceWithoutTax - $this->fOldPriceWithoutTax);
        $this->fIncreasePriceWithTax = PriceHelper::round($this->fPriceWithTax - $this->fOldPriceWithTax);
        $this->fTaxIncreasePrice = PriceHelper::round($this->fTaxPrice - $this->fTaxOldPrice);

        $this->addLogData($this, $obOldPriceData, $obMechanism);
    }

    /**
     * Add discount data from price without tax
     * @param float                   $fPriceWithoutTax
     * @param InterfacePromoMechanism $obMechanism
     */
    public function addDiscountFromPriceWithoutTax($fPriceWithoutTax, $obMechanism)
    {
        $fPriceWithoutTax = PriceHelper::toFloat($fPriceWithoutTax);
        $fTaxRate = PriceHelper::round($this->fPriceWithTax / $this->fPriceWithoutTax);

        $fPriceWithTax = PriceHelper::round($fPriceWithoutTax * $fTaxRate);
        if (TaxHelper::instance()->isPriceIncludeTax()) {
            $fPrice = $fPriceWithTax;
        } else {
            $fPrice = $fPriceWithoutTax;
        }

        $fTaxPrice = $fPriceWithTax - $fPriceWithoutTax;
        $obOldPriceData = clone $this;

        $this->fPrice = $fPrice;
        $this->fPriceWithoutTax = $fPriceWithoutTax;
        $this->fPriceWithTax = $fPriceWithTax;
        $this->fTaxPrice = $fTaxPrice;

        $this->fDiscountPrice = PriceHelper::round($this->fOldPrice - $this->fPrice);
        $this->fDiscountPriceWithoutTax = PriceHelper::round($this->fOldPriceWithoutTax - $this->fPriceWithoutTax);
        $this->fDiscountPriceWithTax = PriceHelper::round($this->fOldPriceWithTax - $this->fPriceWithTax);
        $this->fTaxDiscountPrice = PriceHelper::round($this->fTaxOldPrice - $this->fTaxPrice);

        $this->fIncreasePrice = PriceHelper::round($this->fPrice - $this->fOldPrice);
        $this->fIncreasePriceWithoutTax = PriceHelper::round($this->fPriceWithoutTax - $this->fOldPriceWithoutTax);
        $this->fIncreasePriceWithTax = PriceHelper::round($this->fPriceWithTax - $this->fOldPriceWithTax);
        $this->fTaxIncreasePrice = PriceHelper::round($this->fTaxPrice - $this->fTaxOldPrice);

        $this->addLogData($this, $obOldPriceData, $obMechanism);
    }

    /**
     * Add discount data from price with tax
     * @param float                   $fPriceWithTax
     * @param InterfacePromoMechanism $obMechanism
     */
    public function addDiscountFromPriceWithTax($fPriceWithTax, $obMechanism)
    {
        $fPriceWithTax = PriceHelper::toFloat($fPriceWithTax);
        $fTaxRate = PriceHelper::round($this->fPriceWithTax / $this->fPriceWithoutTax);

        $fPriceWithoutTax = PriceHelper::round($fPriceWithTax / $fTaxRate);
        if (TaxHelper::instance()->isPriceIncludeTax()) {
            $fPrice = $fPriceWithTax;
        } else {
            $fPrice = $fPriceWithoutTax;
        }

        $fTaxPrice = $fPriceWithTax - $fPriceWithoutTax;
        $obOldPriceData = clone $this;

        $this->fPrice = $fPrice;
        $this->fPriceWithoutTax = $fPriceWithoutTax;
        $this->fPriceWithTax = $fPriceWithTax;
        $this->fTaxPrice = $fTaxPrice;

        $this->fDiscountPrice = PriceHelper::round($this->fOldPrice - $this->fPrice);
        $this->fDiscountPriceWithoutTax = PriceHelper::round($this->fOldPriceWithoutTax - $this->fPriceWithoutTax);
        $this->fDiscountPriceWithTax = PriceHelper::round($this->fOldPriceWithTax - $this->fPriceWithTax);
        $this->fTaxDiscountPrice = PriceHelper::round($this->fTaxOldPrice - $this->fTaxPrice);

        $this->fIncreasePrice = PriceHelper::round($this->fPrice - $this->fOldPrice);
        $this->fIncreasePriceWithoutTax = PriceHelper::round($this->fPriceWithoutTax - $this->fOldPriceWithoutTax);
        $this->fIncreasePriceWithTax = PriceHelper::round($this->fPriceWithTax - $this->fOldPriceWithTax);
        $this->fTaxIncreasePrice = PriceHelper::round($this->fTaxPrice - $this->fTaxOldPrice);

        $this->addLogData($this, $obOldPriceData, $obMechanism);
    }

    /**
     * Get log data
     * @param TotalPriceContainer     $obNewPriceData
     * @param TotalPriceContainer     $obPriceData
     * @param InterfacePromoMechanism $obMechanism
     */
    protected function addLogData($obNewPriceData, $obPriceData, $obMechanism = null)
    {
        $this->arLogData[] = new TotalPriceContainerLog($obNewPriceData, $obPriceData, $obMechanism);
    }

    /**
     * @return array|TotalPriceContainerLog[]
     */
    protected function getLogAttribute()
    {
        return $this->arLogData;
    }
}