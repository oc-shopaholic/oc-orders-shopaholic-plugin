<?php namespace Lovata\OrdersShopaholic\Classes\Item;

use Lovata\Toolbox\Classes\Item\ElementItem;
use Lovata\Toolbox\Traits\Helpers\PriceHelperTrait;

use Lovata\Shopaholic\Classes\Helper\CurrencyHelper;

use Lovata\OrdersShopaholic\Models\ShippingType;
use Lovata\OrdersShopaholic\Classes\Processor\CartProcessor;
use Lovata\OrdersShopaholic\Classes\PromoMechanism\PriceContainer;

/**
 * Class ShippingTypeItem
 * @package Lovata\Shopaholic\Classes\Item
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 *
 * @property                                                                $id
 * @property string                                                         $name
 * @property string                                                         $code
 * @property string                                                         $preview_text
 *
 * @property string                                                         $price
 * @property float                                                          $price_value
 * @property string                                                         $old_price
 * @property float                                                          $old_price_value
 * @property string                                                         $discount_price
 * @property float                                                          $discount_price_value
 * @property \Lovata\OrdersShopaholic\Classes\PromoMechanism\PriceContainer $price_data
 * @property string                                                         $currency
 */
class ShippingTypeItem extends ElementItem
{
    use PriceHelperTrait;

    const MODEL_CLASS = ShippingType::class;

    public $arPriceField = ['price', 'old_price', 'discount_price'];

    /** @var ShippingType */
    protected $obElement = null;

    /**
     * Get currency value
     * @return null|string
     */
    protected function getCurrencyAttribute()
    {
        return CurrencyHelper::instance()->getActive();
    }

    /**
     * Get price value
     * @return float
     */
    protected function getPriceValueAttribute()
    {
        $obPriceData = $this->price_data;

        return $obPriceData->price_value;
    }

    /**
     * Get old price value
     * @return float
     */
    protected function getOldPriceValueAttribute()
    {
        $obPriceData = $this->price_data;

        return $obPriceData->old_price_value;
    }

    /**
     * Get discount price value
     * @return float
     */
    protected function getDiscountPriceValueAttribute()
    {
        $obPriceData = $this->price_data;

        return $obPriceData->discount_price_value;
    }

    /**
     * Get price data for position
     * @return \Lovata\OrdersShopaholic\Classes\PromoMechanism\PriceContainer
     */
    protected function getPriceDataAttribute()
    {
        $obPriceData = $this->getAttribute('price_data');
        if (!empty($obPriceData) && $obPriceData instanceof PriceContainer) {
            return $obPriceData;
        }

        CartProcessor::instance()->setActiveShippingType($this);
        $obPriceData = CartProcessor::instance()->getShippingPriceData();

        $this->setAttribute('price_data', $obPriceData);

        return $obPriceData;
    }
}