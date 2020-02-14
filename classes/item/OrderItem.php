<?php namespace Lovata\OrdersShopaholic\Classes\Item;

use Lovata\Toolbox\Classes\Item\ElementItem;
use Lovata\Toolbox\Traits\Helpers\PriceHelperTrait;

use Lovata\Shopaholic\Classes\Item\CurrencyItem;

use Lovata\OrdersShopaholic\Models\Order;
use Lovata\OrdersShopaholic\Classes\Collection\OrderPositionCollection;
use Lovata\OrdersShopaholic\Classes\Collection\OrderPromoMechanismCollection;
use Lovata\OrdersShopaholic\Classes\PromoMechanism\OrderItemPromoMechanismProcessor;

/**
 * Class OrderItem
 * @package Lovata\OrdersShopaholic\Classes\Item
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 *
 * @property int                                                                             $id
 * @property string                                                                          $order_number
 * @property string                                                                          $secret_key
 * @property CurrencyItem                                                                    $currency
 * @property string                                                                          $currency_symbol
 * @property string                                                                          $currency_code
 * @property int                                                                             $user_id
 * @property int                                                                             $status_id
 * @property int                                                                             $payment_method_id
 * @property int                                                                             $shipping_type_id
 * @property double                                                                          $weight
 * @property string                                                                          $shipping_price
 * @property float                                                                           $shipping_price_value
 * @property string                                                                          $old_shipping_price
 * @property float                                                                           $old_shipping_price_value
 * @property string                                                                          $discount_shipping_price
 * @property float                                                                           $discount_shipping_price_value
 * @property \Lovata\OrdersShopaholic\Classes\PromoMechanism\ItemPriceContainer              $shipping_price_data
 * @property string                                                                          $total_price
 * @property float                                                                           $total_price_value
 * @property string                                                                          $old_total_price
 * @property float                                                                           $old_total_price_value
 * @property string                                                                          $discount_total_price
 * @property float                                                                           $discount_total_price_value
 * @property \Lovata\OrdersShopaholic\Classes\PromoMechanism\TotalPriceContainer             $total_price_data
 * @property string                                                                          $position_total_price
 * @property float                                                                           $position_total_price_value
 * @property string                                                                          $old_position_total_price
 * @property float                                                                           $old_position_total_price_value
 * @property string                                                                          $discount_position_total_price
 * @property float                                                                           $discount_position_total_price_value
 * @property \Lovata\OrdersShopaholic\Classes\PromoMechanism\TotalPriceContainer             $position_total_price_data
 * @property array                                                                           $property
 * @property array                                                                           $order_position_id
 * @property array                                                                           $order_promo_mechanism_id
 * @property float                                                                           $shipping_tax_percent
 *
 * @property \October\Rain\Argon\Argon                                                       $created_at
 * @property \October\Rain\Argon\Argon                                                       $updated_at
 *
 * @property StatusItem                                                                      $status
 * @property PaymentMethodItem                                                               $payment_method
 * @property ShippingTypeItem                                                                $shipping_type
 * @property OrderPositionCollection|OrderPositionItem[]                                     $order_position
 * @property OrderPromoMechanismCollection|OrderPositionCollection|OrderPromoMechanismItem[] $order_promo_mechanism
 *
 * Coupons for Shopaholic plugin
 * @property array                                                                           $coupon_list
 */
class OrderItem extends ElementItem
{
    use PriceHelperTrait;

    const MODEL_CLASS = Order::class;

    public $arPriceField = [
        'shipping_price',
        'old_shipping_price',
        'discount_shipping_price',
        'total_price',
        'old_total_price',
        'discount_total_price',
        'position_total_price',
        'old_position_total_price',
        'discount_position_total_price',
    ];

    public $arRelationList = [
        'status'                => [
            'class' => StatusItem::class,
            'field' => 'status_id',
        ],
        'payment_method'        => [
            'class' => PaymentMethodItem::class,
            'field' => 'payment_method_id',
        ],
        'shipping_type'         => [
            'class' => ShippingTypeItem::class,
            'field' => 'shipping_type_id',
        ],
        'order_position'        => [
            'class' => OrderPositionCollection::class,
            'field' => 'order_position_id',
        ],
        'order_promo_mechanism' => [
            'class' => OrderPromoMechanismCollection::class,
            'field' => 'order_promo_mechanism_id',
        ],
        'currency'              => [
            'class' => CurrencyItem::class,
            'field' => 'currency_id',
        ],
    ];

    /** @var Order */
    protected $obElement = null;

    /** @var OrderItemPromoMechanismProcessor */
    protected $obPromoProcessor;

    /**
     * Create object of OrderPromoMechanismProcessor class for Order
     */
    public function getPromoMechanismProcessor() : OrderItemPromoMechanismProcessor
    {
        if (!empty($this->obPromoProcessor) && $this->obPromoProcessor instanceof OrderItemPromoMechanismProcessor) {
            return $this->obPromoProcessor;
        }

        $this->obPromoProcessor = new OrderItemPromoMechanismProcessor($this);

        return $this->obPromoProcessor;
    }

    /**
     * Get shipping price value from model
     * @return float
     */
    public function getShippingPriceValue()
    {
        $fPrice = $this->getAttribute('shipping_price_value');

        return $fPrice;
    }

    /**
     * @return array
     */
    protected function getElementData()
    {
        $arResult = [
            'order_position_id'        => (array) $this->obElement->order_position->lists('id'),
            'order_promo_mechanism_id' => (array) $this->obElement->order_promo_mechanism->lists('id'),
            'shipping_price_value'     => $this->obElement->getShippingPriceValue(),
        ];

        return $arResult;
    }

    /**
     * Get price container object for total price
     * @return \Lovata\OrdersShopaholic\Classes\PromoMechanism\TotalPriceContainer
     */
    protected function getTotalPriceDataAttribute()
    {
        $obPriceData = $this->getPromoMechanismProcessor()->getTotalPrice();

        return $obPriceData;
    }

    /**
     * Get price container object for shipping price
     * @return \Lovata\OrdersShopaholic\Classes\PromoMechanism\ItemPriceContainer
     */
    protected function getShippingPriceDataAttribute()
    {
        $obPriceData = $this->getPromoMechanismProcessor()->getShippingPrice();

        return $obPriceData;
    }

    /**
     * Get price container object for position total price
     * @return \Lovata\OrdersShopaholic\Classes\PromoMechanism\TotalPriceContainer
     */
    protected function getPositionTotalPriceDataAttribute()
    {
        $obPriceData = $this->getPromoMechanismProcessor()->getPositionTotalPrice();

        return $obPriceData;
    }

    /**
     * Get shipping price value
     * @return float
     */
    protected function getShippingPriceValueAttribute()
    {
        return $this->shipping_price_data->price_value;
    }

    /**
     * Get shipping old price value
     * @return float
     */
    protected function getOldShippingPriceValueAttribute()
    {
        return $this->shipping_price_data->old_price_value;
    }

    /**
     * Get shipping discount price value
     * @return float
     */
    protected function getDiscountShippingPriceValueAttribute()
    {
        return $this->shipping_price_data->discount_price_value;
    }

    /**
     * Get position total price value
     * @return float
     */
    protected function getPositionTotalPriceValueAttribute()
    {
        return $this->position_total_price_data->price_value;
    }

    /**
     * Get position total old price value
     * @return float
     */
    protected function getOldPositionTotalPriceValueAttribute()
    {
        return $this->position_total_price_data->old_price_value;
    }

    /**
     * Get position total discount price value
     * @return float
     */
    protected function getDiscountPositionTotalPriceValueAttribute()
    {
        return $this->position_total_price_data->discount_price_value;
    }

    /**
     * Get total price value
     * @return float
     */
    protected function getTotalPriceValueAttribute()
    {
        return $this->total_price_data->price_value;
    }

    /**
     * Get total price value
     * @return float
     */
    protected function getOldTotalPriceValueAttribute()
    {
        return $this->total_price_data->old_price_value;
    }

    /**
     * Get total price value
     * @return float
     */
    protected function getDiscountTotalPriceValueAttribute()
    {
        return $this->total_price_data->discount_price_value;
    }

    /**
     * Get currency_symbol value
     * @return float
     */
    protected function getCurrencySymbolAttribute()
    {
        return $this->currency->symbol;
    }

    /**
     * Get currency_code value
     * @return float
     */
    protected function getCurrencyCodeAttribute()
    {
        return $this->currency->code;
    }

    /**
     * Get total weight
     * @return int
     */
    protected function getWeightAttribute()
    {
        $obOrderPositionList = $this->order_position;
        if ($obOrderPositionList->isEmpty()) {
            return 0;
        }

        $iWeight = 0;
        foreach ($obOrderPositionList as $obOrderPosition) {
            $iWeight += (float) $obOrderPosition->weight;
        }

        return $iWeight;
    }
}
