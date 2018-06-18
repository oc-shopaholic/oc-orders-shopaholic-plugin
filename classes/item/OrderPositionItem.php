<?php namespace Lovata\OrdersShopaholic\Classes\Item;

use Lovata\Shopaholic\Models\Offer;
use Lovata\Shopaholic\Classes\Item\OfferItem;
use Lovata\OrdersShopaholic\Models\OrderPosition;

/**
 * Class OrderPositionItem
 * @package Lovata\OrdersShopaholic\Classes\Item
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 *
 * @property int                                       $id
 * @property int                                       $order_id
 * @property string                                    $price
 * @property float                                     $price_value
 * @property string                                    $old_price
 * @property float                                     $old_price_value
 * @property string                                    $total_price
 * @property float                                     $total_price_value
 * @property string                                    $code
 *
 * @property OrderItem                                 $order
 * @property \Lovata\Shopaholic\Classes\Item\OfferItem $item
 * @property \Lovata\Shopaholic\Classes\Item\OfferItem $offer
 */
class OrderPositionItem extends AbstractPositionItem
{
    const MODEL_CLASS = OrderPosition::class;

    public $arPriceField = ['price', 'total_price', 'old_price'];

    public $arRelationList = [
        'order' => [
            'class' => OrderItem::class,
            'field' => 'order_id',
        ],
    ];

    /** @var OrderPosition */
    protected $obElement = null;

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
}
