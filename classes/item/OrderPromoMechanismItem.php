<?php namespace Lovata\OrdersShopaholic\Classes\Item;

use Event;
use Lovata\Toolbox\Classes\Item\ElementItem;

use Lovata\OrdersShopaholic\Models\OrderPromoMechanism;

/**
 * Class OrderPromoMechanismItem
 * @package Lovata\OrdersShopaholic\Classes\Item
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 *
 * @property int       $id
 * @property int       $order_id
 * @property int       $mechanism_id
 * @property string    $name
 * @property string    $type
 * @property bool      $increase
 * @property int       $priority
 * @property float     $discount_value
 * @property string    $discount_type
 * @property bool      $final_discount
 * @property array     $property
 * @property int       $element_id
 * @property string    $element_type
 * @property array     $element_data
 * @property string    $description
 * @property OrderItem $order
 */
class OrderPromoMechanismItem extends ElementItem
{
    const MODEL_CLASS = OrderPromoMechanism::class;

    public $arRelationList = [
        'order' => [
            'class' => OrderItem::class,
            'field' => 'order_id',
        ],
    ];

    /** @var OrderPromoMechanism */
    protected $obElement = null;

    /**
     * Get description attribute value
     * @return string
     */
    protected function getDescriptionAttribute()
    {
        $sDescription = Event::fire(OrderPromoMechanism::EVENT_GET_DESCRIPTION, $this, true);

        return $sDescription;
    }
}
