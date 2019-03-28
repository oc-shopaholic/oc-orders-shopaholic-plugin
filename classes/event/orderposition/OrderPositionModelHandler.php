<?php namespace Lovata\OrdersShopaholic\Classes\Event\OrderPosition;

use Lovata\OrdersShopaholic\Classes\Item\OrderItem;
use Lovata\Toolbox\Classes\Event\ModelHandler;

use Lovata\OrdersShopaholic\Models\OrderPosition;
use Lovata\OrdersShopaholic\Classes\Item\OrderPositionItem;

/**
 * Class OrderPositionModelHandler
 * @package Lovata\OrdersShopaholic\Classes\Event\OrderPosition
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class OrderPositionModelHandler extends ModelHandler
{
    /** @var OrderPosition */
    protected $obElement;

    /**
     * After create event handler
     */
    protected function afterCreate()
    {
        OrderItem::clearCache($this->obElement->order_id);
    }

    /**
     * After delete event handler
     */
    protected function afterDelete()
    {
        parent::afterDelete();

        OrderItem::clearCache($this->obElement->order_id);
    }

    /**
     * Get model class name
     * @return string
     */
    protected function getModelClass()
    {
        return OrderPosition::class;
    }

    /**
     * Get item class name
     * @return string
     */
    protected function getItemClass()
    {
        return OrderPositionItem::class;
    }
}
