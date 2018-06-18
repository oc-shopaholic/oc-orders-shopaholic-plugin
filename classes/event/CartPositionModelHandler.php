<?php namespace Lovata\OrdersShopaholic\Classes\Event;

use Lovata\Toolbox\Classes\Event\ModelHandler;

use Lovata\OrdersShopaholic\Models\CartPosition;
use Lovata\OrdersShopaholic\Classes\Item\CartPositionItem;

/**
 * Class CartPositionModelHandler
 * @package Lovata\OrdersShopaholic\Classes\Event
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class CartPositionModelHandler extends ModelHandler
{
    /**
     * Get model class name
     * @return string
     */
    protected function getModelClass()
    {
        return CartPosition::class;
    }

    /**
     * Get item class name
     * @return string
     */
    protected function getItemClass()
    {
        return CartPositionItem::class;
    }
}
