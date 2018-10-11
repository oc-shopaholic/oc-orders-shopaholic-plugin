<?php namespace Lovata\OrdersShopaholic\Classes\PromoMechanism;

use Lovata\Toolbox\Classes\Helper\PriceHelper;

/**
 * Class PriceContainer
 * @package Lovata\OrdersShopaholic\Classes\PromoMechanism
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 *
 * @property string                    $price
 * @property float                     $price_value
 * @property string                    $old_price
 * @property float                     $old_price_value
 * @property string                    $discount_price
 * @property float                     $discount_price_value
 * @property string                    $price_per_unit
 * @property float                     $price_per_unit_value
 * @property array|PriceContainerLog[] $log
 */
class PriceContainer
{
    protected $iQuantity = 0;
    protected $fPrice = 0;
    protected $fOldPrice = 0;
    protected $fDiscountPrice = 0;
    protected $arLogData = [];

    /**
     * PriceData constructor.
     * @param float $fPrice
     * @param float $fOldPrice
     * @param int   $iQuantity
     */
    public function __construct($fPrice, $fOldPrice, $iQuantity = null)
    {
        $this->fOldPrice = PriceHelper::toFloat($fOldPrice);
        $this->fPrice = PriceHelper::toFloat($fPrice);
        $this->iQuantity = $iQuantity;

        $this->fDiscountPrice = PriceHelper::round($this->fOldPrice - $this->fPrice);
    }

    /**
     * @param string $sField
     * @param float  $fValue
     */
    public function __set($sField, $fValue)
    {
        $fValue = PriceHelper::toFloat($fValue);

        if ($sField == 'price_value' || $sField == 'price') {
            $this->fPrice = $fValue;
        } elseif ($sField == 'old_price_value' || $sField == 'old_price') {
            $this->fOldPrice = $fValue;
        } elseif ($sField == 'discount_price_value' || $sField == 'discount_price') {
            $this->fDiscountPrice = $fValue;
        }
    }

    /**
     * @param string $sField
     * @return mixed
     */
    public function __get($sField)
    {
        switch ($sField) {
            case ('price_value'):
                return $this->fPrice;
            case ('price'):
                return PriceHelper::format($this->fPrice);
            case ('old_price_value'):
                return $this->fOldPrice;
            case ('old_price'):
                return PriceHelper::format($this->fOldPrice);
            case ('discount_price_value'):
                return $this->fDiscountPrice;
            case ('discount_price'):
                return PriceHelper::format($this->fDiscountPrice);
            case ('price_per_unit_value'):
                if ($this->iQuantity === null) {
                    return $this->fPrice;
                }

                $fPricePerUnit = PriceHelper::round($this->fPrice / $this->iQuantity);
                return $fPricePerUnit;
            case ('price_per_unit'):
                if ($this->iQuantity === null) {
                    return PriceHelper::format($this->fPrice);
                }

                $fPricePerUnit = PriceHelper::round($this->fPrice / $this->iQuantity);

                return PriceHelper::format($fPricePerUnit);
            case ('log'):
                return $this->arLogData;
            default:
                return null;
        }
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
     * @param array $arResult
     * @return array
     */
    public function getData($arResult = [])
    {
        if (empty($arResult)) {
            $arResult = [];
        }

        $arResult['price'] = $this->price;
        $arResult['price_value'] = $this->price_value;
        $arResult['old_price'] = $this->old_price;
        $arResult['old_price_value'] = $this->old_price_value;
        $arResult['discount_price'] = $this->discount_price;
        $arResult['discount_price_value'] = $this->discount_price_value;
        $arResult['log'] = [];
        if (empty($this->arLogData)) {
            return $arResult;
        }

        foreach ($this->arLogData as $obPriceData) {
            $arResult['log'][] = $obPriceData->getData();
        }

        return $arResult;
    }

    /**
     * @param float                   $fPrice
     * @param InterfacePromoMechanism $obMechanism
     */
    public function addDiscount($fPrice, $obMechanism)
    {
        $fPrice = PriceHelper::toFloat($fPrice);
        $fDiscount = PriceHelper::round($this->fPrice - $fPrice);

        $this->addLogData($fPrice, $this->fPrice, $fDiscount, $obMechanism);

        $this->fPrice = $fPrice;
        $this->fDiscountPrice = PriceHelper::round($this->fOldPrice - $this->fPrice);
    }

    /**
     * Get log data
     * @param float                   $fNewPrice
     * @param float                   $fPrice
     * @param float                   $fDiscount
     * @param InterfacePromoMechanism $obMechanism
     */
    protected function addLogData($fNewPrice, $fPrice, $fDiscount, $obMechanism = null)
    {
        $this->arLogData[] = new PriceContainerLog($fNewPrice, $fPrice, $fDiscount, $obMechanism);
    }
}