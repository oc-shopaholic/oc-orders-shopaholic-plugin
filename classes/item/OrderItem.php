<?php namespace Lovata\OrdersShopaholic\Classes\Item;

use Lovata\Toolbox\Classes\Item\ElementItem;
use Lovata\Toolbox\Traits\Helpers\PriceHelperTrait;

use Lovata\OrdersShopaholic\Models\Order;
use Lovata\OrdersShopaholic\Classes\Collection\OrderPositionCollection;

/**
 * Class OrderItem
 * @package Lovata\OrdersShopaholic\Classes\Item
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 *
 * @property int                       $id
 * @property string                    $order_number
 * @property string                    $secret_key
 * @property int                       $user_id
 * @property int                       $status_id
 * @property int                       $payment_method_id
 * @property int                       $shipping_type_id
 * @property string                    $shipping_price
 * @property float                     $shipping_price_value
 * @property string                    $total_price
 * @property float                     $total_price_value
 * @property string                    $position_total_price
 * @property float                     $position_total_price_value
 * @property array                     $property
 * @property array                     $order_position_id
 *
 * @property \October\Rain\Argon\Argon $created_at
 * @property \October\Rain\Argon\Argon $updated_at
 *
 * @property StatusItem $status
 * @property PaymentMethodItem $payment_method
 * @property ShippingTypeItem $shipping_type
 * @property OrderPositionCollection|OrderPositionItem[] $order_position
 */
class OrderItem extends ElementItem
{
    use PriceHelperTrait;

    const MODEL_CLASS = Order::class;

    public $arPriceField = ['shipping_price', 'total_price', 'position_total_price'];

    public $arRelationList = [
        'status' => [
            'class' => StatusItem::class,
            'field' => 'status_id',
        ],
        'payment_method' => [
            'class' => PaymentMethodItem::class,
            'field' => 'payment_method_id',
        ],
        'shipping_type' => [
            'class' => ShippingTypeItem::class,
            'field' => 'shipping_type_id',
        ],
        'order_position' => [
            'class' => OrderPositionCollection::class,
            'field' => 'order_position_id',
        ],
    ];

    /** @var Order */
    protected $obElement = null;

    /**
     * @return array
     */
    protected function getElementData()
    {
        $arResult = [
            'order_position_id' => (array) $this->obElement->order_position->lists('id'),
        ];

        return $arResult;
    }
}
