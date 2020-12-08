<?php namespace Lovata\OrdersShopaholic\Classes\Item;

use Lovata\Toolbox\Classes\Helper\PriceHelper;
use Lovata\Toolbox\Traits\Helpers\PriceHelperTrait;

use Lovata\Shopaholic\Classes\Helper\MeasureHelper;
use Lovata\Shopaholic\Models\Offer;
use Lovata\Shopaholic\Classes\Item\OfferItem;
use Lovata\Shopaholic\Classes\Helper\TaxHelper;

use Lovata\OrdersShopaholic\Models\OrderPosition;
use Lovata\OrdersShopaholic\Classes\PromoMechanism\ItemPriceContainer;

/**
 * Class OrderPositionItem
 * @package Lovata\OrdersShopaholic\Classes\Item
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 *
 * @property int                                                                 $id
 * @property int                                                                 $order_id
 * @property int                                                                 $quantity
 * @property double                                                              $weight
 * @property double                                                              $height
 * @property double                                                              $length
 * @property double                                                              $width
 * @property \Lovata\Shopaholic\Classes\Item\MeasureItem                         $dimensions_measure
 * @property \Lovata\Shopaholic\Classes\Item\MeasureItem                         $weight_measure
 *
 * @property string                                                              $price
 * @property float                                                               $price_value
 * @property string                                                              $tax_price
 * @property float                                                               $tax_price_value
 * @property string                                                              $price_without_tax
 * @property float                                                               $price_without_tax_value
 * @property string                                                              $price_with_tax
 * @property float                                                               $price_with_tax_value
 *
 * @property string                                                              $old_price
 * @property float                                                               $old_price_value
 * @property string                                                              $tax_old_price
 * @property float                                                               $tax_old_price_value
 * @property string                                                              $old_price_without_tax
 * @property float                                                               $old_price_without_tax_value
 * @property string                                                              $old_price_with_tax
 * @property float                                                               $old_price_with_tax_value
 *
 * @property string                                                              $total_price
 * @property float                                                               $total_price_value
 * @property string                                                              $tax_total_price
 * @property float                                                               $tax_total_price_value
 * @property string                                                              $total_price_without_tax
 * @property float                                                               $total_price_without_tax_value
 * @property string                                                              $total_price_with_tax
 * @property float                                                               $total_price_with_tax_value
 *
 * @property string                                                              $old_total_price
 * @property float                                                               $old_total_price_value
 * @property string                                                              $tax_old_total_price
 * @property float                                                               $tax_old_total_price_value
 * @property string                                                              $old_total_price_without_tax
 * @property float                                                               $old_total_price_without_tax_value
 * @property string                                                              $old_total_price_with_tax
 * @property float                                                               $old_total_price_with_tax_value
 *
 * @property string                                                              $discount_total_price
 * @property float                                                               $discount_total_price_value
 * @property string                                                              $tax_discount_total_price
 * @property float                                                               $tax_discount_total_price_value
 * @property string                                                              $discount_total_price_without_tax
 * @property float                                                               $discount_total_price_without_tax_value
 * @property string                                                              $discount_total_price_with_tax
 * @property float                                                               $discount_total_price_with_tax_value
 *
 * @property string                                                              $increase_total_price
 * @property float                                                               $increase_total_price_value
 * @property string                                                              $tax_increase_total_price
 * @property float                                                               $tax_increase_total_price_value
 * @property string                                                              $increase_total_price_without_tax
 * @property float                                                               $increase_total_price_without_tax_value
 * @property string                                                              $increase_total_price_with_tax
 * @property float                                                               $increase_total_price_with_tax_value
 *
 * @property string                                                              $total_price_per_unit
 * @property float                                                               $total_price_per_unit_value
 * @property string                                                              $tax_total_price_per_unit
 * @property float                                                               $tax_total_price_per_unit_value
 * @property string                                                              $total_price_per_unit_without_tax
 * @property float                                                               $total_price_per_unit_without_tax_value
 * @property string                                                              $total_price_per_unit_with_tax
 * @property float                                                               $total_price_per_unit_with_tax_value
 *
 * @property string                                                              $old_total_price_per_unit
 * @property float                                                               $old_total_price_per_unit_value
 * @property string                                                              $tax_old_total_price_per_unit
 * @property float                                                               $tax_old_total_price_per_unit_value
 * @property string                                                              $old_total_price_per_unit_without_tax
 * @property float                                                               $old_total_price_per_unit_without_tax_value
 * @property string                                                              $old_total_price_per_unit_with_tax
 * @property float                                                               $old_total_price_per_unit_with_tax_value
 *
 * @property string                                                              $discount_total_price_per_unit
 * @property float                                                               $discount_total_price_per_unit_value
 * @property string                                                              $tax_discount_total_price_per_unit
 * @property float                                                               $tax_discount_total_price_per_unit_value
 * @property string                                                              $discount_total_price_per_unit_without_tax
 * @property float                                                               $discount_total_price_per_unit_without_tax_value
 * @property string                                                              $discount_total_price_per_unit_with_tax
 * @property float                                                               $discount_total_price_per_unit_with_tax_value
 *
 * @property string                                                              $increase_total_price_per_unit
 * @property float                                                               $increase_total_price_per_unit_value
 * @property string                                                              $tax_increase_total_price_per_unit
 * @property float                                                               $tax_increase_total_price_per_unit_value
 * @property string                                                              $increase_total_price_per_unit_without_tax
 * @property float                                                               $increase_total_price_per_unit_without_tax_value
 * @property string                                                              $increase_total_price_per_unit_with_tax
 * @property float                                                               $increase_total_price_per_unit_with_tax_value
 *
 * @property \Lovata\OrdersShopaholic\Classes\PromoMechanism\ItemPriceContainer  $price_data
 * @property float                                                               $tax_percent
 *
 * @property string                                                              $code
 * @property \Lovata\Shopaholic\Classes\Item\CurrencyItem                        $currency
 * @property string                                                              $currency_symbol
 * @property string                                                              $currency_code
 *
 * @property OrderItem                                                           $order
 * @property \Lovata\Shopaholic\Classes\Item\OfferItem                           $item
 * @property \Lovata\Shopaholic\Classes\Item\OfferItem                           $offer
 *
 * Subscriptions for Shopaholic
 * @property bool                                                                $is_subscription
 * @property \October\Rain\Argon\Argon                                           $expire_at
 * @property int                                                                 $subscription_period_id
 * @property \Lovata\SubscriptionsShopaholic\Classes\Item\SubscriptionPeriodItem $subscription_period
 * @property int                                                                 $subscription_access_id
 * @property \Lovata\SubscriptionsShopaholic\Classes\Item\SubscriptionAccessItem $subscription_access
 */
class OrderPositionItem extends AbstractPositionItem
{
    use PriceHelperTrait;

    const MODEL_CLASS = OrderPosition::class;

    public $arPriceField = [
        'price',
        'old_price',
        'tax_price',
        'tax_old_price',
        'price_with_tax',
        'old_price_with_tax',
        'price_without_tax',
        'old_price_without_tax',
    ];

    public $arRelationList = [
        'order' => [
            'class' => OrderItem::class,
            'field' => 'order_id',
        ],
    ];

    /** @var OrderPosition */
    protected $obElement = null;

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

        $sName = str_replace('total_', '', $sName);

        return $this->price_data->$sName;
    }

    /**
     * Get total price value
     * @return \Lovata\OrdersShopaholic\Classes\PromoMechanism\ItemPriceContainer
     */
    protected function getPriceDataAttribute()
    {
        $obPriceData = $this->order->getPromoMechanismProcessor()->getPositionPrice($this->id);
        if (empty($obPriceData)) {
            return ItemPriceContainer::makeEmpty();
        }

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
     * Get tax_price_value attribute value
     * @return float
     */
    protected function getTaxPriceValueAttribute()
    {
        $fPrice = PriceHelper::round($this->price_with_tax_value - $this->price_without_tax_value);

        return $fPrice;
    }

    /**
     * Get tax_old_price_value attribute value
     * @return float
     */
    protected function getTaxOldPriceValueAttribute()
    {
        $fPrice = PriceHelper::round($this->old_price_with_tax_value - $this->old_price_without_tax_value);

        return $fPrice;
    }

    /**
     * Get price_with_tax_value attribute value
     * @return float
     */
    protected function getPriceWithTaxValueAttribute()
    {
        $fPrice = TaxHelper::instance()->getPriceWithTax($this->price_value, $this->tax_percent);

        return $fPrice;
    }

    /**
     * Get old_price_with_tax_value attribute value
     * @return float
     */
    protected function getOldPriceWithTaxValueAttribute()
    {
        $fPrice = TaxHelper::instance()->getPriceWithTax($this->old_price_value, $this->tax_percent);

        return $fPrice;
    }

    /**
     * Get price_without_tax_value attribute value
     * @return float
     */
    protected function getPriceWithoutTaxValueAttribute()
    {
        $fPrice = TaxHelper::instance()->getPriceWithoutTax($this->price_value, $this->tax_percent);

        return $fPrice;
    }

    /**
     * Get old_price_without_tax_value attribute value
     * @return float
     */
    protected function getOldPriceWithoutTaxValueAttribute()
    {
        $fPrice = TaxHelper::instance()->getPriceWithoutTax($this->old_price_value, $this->tax_percent);

        return $fPrice;
    }

    /**
     * Get currency attribute
     * @return null|string
     */
    protected function getCurrencyAttribute()
    {
        return $this->order->currency;
    }

    /**
     * Get currency_symbol value
     * @return float
     */
    protected function getCurrencySymbolAttribute()
    {
        return $this->order->currency_symbol;
    }

    /**
     * Get currency_code value
     * @return float
     */
    protected function getCurrencyCodeAttribute()
    {
        return $this->order->currency_code;
    }

    /**
     * Get dimensions unit measure
     * @return \Lovata\Shopaholic\Classes\Item\MeasureItem
     */
    protected function getDimensionsMeasureAttribute()
    {
        $obMeasureItem = MeasureHelper::instance()->getDimensionsMeasureItem();

        return $obMeasureItem;
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
