<?php namespace Lovata\OrdersShopaholic\Classes\Item;

use Lovata\Toolbox\Classes\Item\ElementItem;
use Lovata\Toolbox\Traits\Helpers\PriceHelperTrait;

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
 * @property string                                                                          $currency
 * @property int                                                                             $user_id
 * @property int                                                                             $status_id
 * @property int                                                                             $payment_method_id
 * @property int                                                                             $shipping_type_id
 * @property string                                                                          $shipping_price
 * @property float                                                                           $shipping_price_value
 * @property string                                                                          $total_price
 * @property float                                                                           $total_price_value
 * @property string                                                                          $old_total_price
 * @property float                                                                           $old_total_price_value
 * @property string                                                                          $discount_price
 * @property float                                                                           $discount_price_value
 * @property string                                                                          $position_total_price
 * @property float                                                                           $position_total_price_value
 * @property array                                                                           $property
 * @property array                                                                           $order_position_id
 * @property array                                                                           $order_promo_mechanism_id
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

    public $arPriceField = ['shipping_price', 'total_price', 'position_total_price', 'old_total_price', 'discount_price'];

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
     * Get price container object
     * @return \Lovata\OrdersShopaholic\Classes\PromoMechanism\PriceContainer
     */
    public function getTotalPriceData()
    {
        $obPriceData = $this->getPromoMechanismProcessor()->getTotalPrice();

        return $obPriceData;
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
     * Get position total price value
     * @return float
     */
    protected function getPositionTotalPriceValueAttribute()
    {
        $obPriceData = $this->getPromoMechanismProcessor()->getPositionTotalPrice();

        return $obPriceData->price_value;
    }

    /**
     * Get shipping price value
     * @return float
     */
    protected function getShippingPriceValueAttribute()
    {
        $obPriceData = $this->getPromoMechanismProcessor()->getShippingPrice();

        return $obPriceData->price_value;
    }

    /**
     * Get total price value
     * @return float
     */
    protected function getTotalPriceValueAttribute()
    {
        $obPriceData = $this->getTotalPriceData();

        return $obPriceData->price_value;
    }

    /**
     * Get total price value
     * @return float
     */
    protected function getOldTotalPriceValueAttribute()
    {
        $obPriceData = $this->getTotalPriceData();

        return $obPriceData->old_price_value;
    }

    /**
     * Get total price value
     * @return float
     */
    protected function getDiscountPriceValueAttribute()
    {
        $obPriceData = $this->getTotalPriceData();

        return $obPriceData->discount_price_value;
    }
}
