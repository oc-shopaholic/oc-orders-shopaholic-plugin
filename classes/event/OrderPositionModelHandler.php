<?php namespace Lovata\OrdersShopaholic\Classes\Event;

use Lovata\Toolbox\Classes\Event\ModelHandler;

use Lovata\OrdersShopaholic\Models\OrderPosition;
use Lovata\OrdersShopaholic\Classes\Item\OrderPositionItem;

/**
 * Class OrderPositionModelHandler
 * @package Lovata\OrdersShopaholic\Classes\Event
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class OrderPositionModelHandler extends ModelHandler
{
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
