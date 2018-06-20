<?php namespace Lovata\OrdersShopaholic\Classes\Collection;

use Lovata\Toolbox\Classes\Collection\ElementCollection;

use Lovata\OrdersShopaholic\Classes\Item\OrderPositionItem;

/**
 * Class OrderPositionCollectionOrderCollection
 * @package Lovata\Shopaholic\Classes\Collection
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class OrderPositionCollection extends ElementCollection
{
    const ITEM_CLASS = OrderPositionItem::class;
}
