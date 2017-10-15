<?php namespace Lovata\OrdersShopaholic\Classes\Event;

use Lovata\Toolbox\Classes\Event\ModelHandler;

use Lovata\OrdersShopaholic\Models\CartElement;
use Lovata\OrdersShopaholic\Classes\Item\CartElementItem;

/**
 * Class CartElementModelHandler
 * @package Lovata\OrdersShopaholic\Classes\Event
 * @author Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class CartElementModelHandler extends ModelHandler
{
    /**
     * Get model class name
     * @return string
     */
    protected function getModelClass()
    {
        return CartElement::class;
    }
    
    /**
     * Get item class name
     * @return string
     */
    protected function getItemClass()
    {
        return CartElementItem::class;
    }
}