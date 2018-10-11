<?php namespace Lovata\OrdersShopaholic\Classes\PromoMechanism;

use Lovata\Toolbox\Classes\Helper\PriceHelper;

/**
 * Class PriceContainerLog
 * @package Lovata\OrdersShopaholic\Classes\PromoMechanism
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 *
 * @property string                  $price
 * @property float                   $price_value
 * @property string                  $old_price
 * @property float                   $old_price_value
 * @property string                  $discount_price
 * @property float                   $discount_price_value
 * @property InterfacePromoMechanism $mechanism
 */
class PriceContainerLog
{
    protected $fPrice = 0;
    protected $fOldPrice = 0;
    protected $fDiscountPrice = 0;
    /** @var InterfacePromoMechanism */
    protected $obMechanism;

    /**
     * Get log data
     * @param float                   $fNewPrice
     * @param float                   $fPrice
     * @param float                   $fDiscount
     * @param InterfacePromoMechanism $obMechanism
     */
    public function __construct($fNewPrice, $fPrice, $fDiscount, $obMechanism = null)
    {
        $this->fOldPrice = PriceHelper::toFloat($fPrice);
        $this->fPrice = PriceHelper::toFloat($fNewPrice);
        $this->fDiscountPrice = PriceHelper::toFloat($fDiscount);

        $this->obMechanism = $obMechanism;
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
                return PriceHelper::toFloat($this->fPrice);
            case ('old_price_value'):
                return $this->fOldPrice;
            case ('old_price'):
                return PriceHelper::toFloat($this->fOldPrice);
            case ('discount_price_value'):
                return $this->fDiscountPrice;
            case ('discount_price'):
                return PriceHelper::toFloat($this->fDiscountPrice);
            case ('mechanism'):
                return $this->obMechanism;
            default:
                return null;
        }
    }

    /**
     * Get log data
     * @return array
     */
    public function getData()
    {
        $arResult = [
            'price'                => $this->price,
            'price_value'          => $this->price_value,
            'old_price'            => $this->old_price,
            'old_price_value'      => $this->old_price_value,
            'discount_price'       => $this->discount_price,
            'discount_price_value' => $this->discount_price_value,
            'description'          => !empty($this->obMechanism) ? $this->obMechanism->getRelatedDescription() : '',
        ];

        return $arResult;
    }
}