<?php namespace Lovata\OrdersShopaholic\Classes\Item;

use Lovata\OrdersShopaholic\Classes\Processor\CartProcessor;

use Lovata\Shopaholic\Classes\Helper\MeasureHelper;
use Lovata\Shopaholic\Models\Offer;
use Lovata\Shopaholic\Classes\Item\OfferItem;
use Lovata\Shopaholic\Classes\Helper\CurrencyHelper;

use Lovata\OrdersShopaholic\Models\CartPosition;

/**
 * Class CartPositionItem
 * @package Lovata\OrdersShopaholic\Classes\Item
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 *
 * @property int                                                                                           $item_id
 * @property string                                                                                        $item_type
 * @property array                                                                                         $property
 * @property integer                                                                                       $quantity
 * @property string                                                                                        $currency
 * @property string                                                                                        $currency_code
 * @property double                                                                                        $weight
 * @property \Lovata\Shopaholic\Classes\Item\MeasureItem                                                   $weight_measure
 *
 * @property string                                                                                        $price
 * @property float                                                                                         $price_value
 * @property string                                                                                        $tax_price
 * @property float                                                                                         $tax_price_value
 * @property string                                                                                        $price_without_tax
 * @property float                                                                                         $price_without_tax_value
 * @property string                                                                                        $price_with_tax
 * @property float                                                                                         $price_with_tax_value
 *
 * @property string                                                                                        $old_price
 * @property float                                                                                         $old_price_value
 * @property string                                                                                        $tax_old_price
 * @property float                                                                                         $tax_old_price_value
 * @property string                                                                                        $old_price_without_tax
 * @property float                                                                                         $old_price_without_tax_value
 * @property string                                                                                        $old_price_with_tax
 * @property float                                                                                         $old_price_with_tax_value
 *
 * @property string                                                                                        $discount_price
 * @property float                                                                                         $discount_price_value
 * @property string                                                                                        $tax_discount_price
 * @property float                                                                                         $tax_discount_price_value
 * @property string                                                                                        $discount_price_without_tax
 * @property float                                                                                         $discount_price_without_tax_value
 * @property string                                                                                        $discount_price_with_tax
 * @property float                                                                                         $discount_price_with_tax_value
 *
 * @property string                                                                                        $increase_price
 * @property float                                                                                         $increase_price_value
 * @property string                                                                                        $tax_increase_price
 * @property float                                                                                         $tax_increase_price_value
 * @property string                                                                                        $increase_price_without_tax
 * @property float                                                                                         $increase_price_without_tax_value
 * @property string                                                                                        $increase_price_with_tax
 * @property float                                                                                         $increase_price_with_tax_value
 *
 * @property string                                                                                        $price_per_unit
 * @property float                                                                                         $price_per_unit_value
 * @property string                                                                                        $tax_price_per_unit
 * @property float                                                                                         $tax_price_per_unit_value
 * @property string                                                                                        $price_per_unit_without_tax
 * @property float                                                                                         $price_per_unit_without_tax_value
 * @property string                                                                                        $price_per_unit_with_tax
 * @property float                                                                                         $price_per_unit_with_tax_value
 *
 * @property string                                                                                        $old_price_per_unit
 * @property float                                                                                         $old_price_per_unit_value
 * @property string                                                                                        $tax_old_price_per_unit
 * @property float                                                                                         $tax_old_price_per_unit_value
 * @property string                                                                                        $old_price_per_unit_without_tax
 * @property float                                                                                         $old_price_per_unit_without_tax_value
 * @property string                                                                                        $old_price_per_unit_with_tax
 * @property float                                                                                         $old_price_per_unit_with_tax_value
 *
 * @property string                                                                                        $discount_price_per_unit
 * @property float                                                                                         $discount_price_per_unit_value
 * @property string                                                                                        $tax_discount_price_per_unit
 * @property float                                                                                         $tax_discount_price_per_unit_value
 * @property string                                                                                        $discount_price_per_unit_without_tax
 * @property float                                                                                         $discount_price_per_unit_without_tax_value
 * @property string                                                                                        $discount_price_per_unit_with_tax
 * @property float                                                                                         $discount_price_per_unit_with_tax_value
 *
 * @property string                                                                                        $increase_price_per_unit
 * @property float                                                                                         $increase_price_per_unit_value
 * @property string                                                                                        $tax_increase_price_per_unit
 * @property float                                                                                         $tax_increase_price_per_unit_value
 * @property string                                                                                        $increase_price_per_unit_without_tax
 * @property float                                                                                         $increase_price_per_unit_without_tax_value
 * @property string                                                                                        $increase_price_per_unit_with_tax
 * @property float                                                                                         $increase_price_per_unit_with_tax_value
 *
 * @property \Lovata\OrdersShopaholic\Classes\PromoMechanism\ItemPriceContainer                            $price_data
 *
 * @property float                                                                                         $tax_percent
 * @property \Lovata\Shopaholic\Classes\Collection\TaxCollection|\Lovata\Shopaholic\Classes\Item\TaxItem[] $tax_list
 *
 * @property \Lovata\Shopaholic\Classes\Item\OfferItem                                                     $item
 * @property \Lovata\Shopaholic\Classes\Item\OfferItem                                                     $offer
 */
class CartPositionItem extends AbstractPositionItem
{
    const MODEL_CLASS = CartPosition::class;

    /** @var CartPosition */
    protected $obElement = null;

    /** @var \Lovata\OrdersShopaholic\Classes\Processor\AbstractOrderPositionProcessor|null */
    protected $obOrderPositionProcessor;

    /**
     * Get param from model data
     * @param string $sName
     * @return mixed|null
     */
    public function __get($sName)
    {
        $sValue = parent::__get($sName);
        if ($sValue !== null || $this->isEmpty()) {
            return $sValue;
        }

        return $this->price_data->$sName;
    }

    /**
     * Get item field value
     * @return \Lovata\OrdersShopaholic\Classes\Processor\AbstractOrderPositionProcessor|null
     */
    public function getOrderPositionProcessor()
    {
        if ($this->obOrderPositionProcessor !== null) {
            return $this->obOrderPositionProcessor;
        }

        $sModelClass = $this->item_type;
        if (!isset($this->arItemTypeList[$sModelClass])) {
            return null;
        }

        $sClassName = $this->arItemTypeList[$sModelClass]['order_processor'];

        $obPositionProcessor = app($sClassName, [$this]);
        $this->obOrderPositionProcessor = $obPositionProcessor;

        return $obPositionProcessor;
    }

    /**
     * Get price data for position
     * @return \Lovata\OrdersShopaholic\Classes\PromoMechanism\ItemPriceContainer
     */
    protected function getPriceDataAttribute()
    {
        $obPriceData = CartProcessor::instance()->getCartPositionPriceData($this->id);

        return $obPriceData;
    }

    /**
     * Get offer field value
     * @return OfferItem
     */
    protected function getOfferAttribute()
    {
        if ($this->item_type != Offer::class) {
            return OfferItem::make(null);
        }

        return OfferItem::make($this->item_id);
    }

    /**
     * Get currency value
     * @return null|string
     */
    protected function getCurrencyAttribute()
    {
        return CurrencyHelper::instance()->getActiveCurrencySymbol();
    }

    /**
     * Get currency code value
     * @return null|string
     */
    protected function getCurrencyCodeAttribute()
    {
        return CurrencyHelper::instance()->getActiveCurrencyCode();
    }

    /**
     * Get weight
     * @return float
     */
    protected function getWeightAttribute()
    {
        $fWeight = $this->quantity * $this->item->weight;

        return $fWeight;
    }

    /**
     * Get weight unit measure
     * @return \Lovata\Shopaholic\Classes\Item\MeasureItem
     */
    protected function getWeightMeasureAttribute()
    {
        $obMeasureItem = MeasureHelper::instance()->getWeightMeasureItem();

        return $obMeasureItem;
    }
}
