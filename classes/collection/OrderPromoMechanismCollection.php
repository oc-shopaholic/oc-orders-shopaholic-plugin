<?php namespace Lovata\OrdersShopaholic\Classes\Collection;

use Lovata\Toolbox\Classes\Collection\ElementCollection;

use Lovata\OrdersShopaholic\Classes\Item\OrderPromoMechanismItem;

/**
 * Class OrderPromoMechanismCollection
 * @package Lovata\OrdersShopaholic\Classes\Collection
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class OrderPromoMechanismCollection extends ElementCollection
{
    const ITEM_CLASS = OrderPromoMechanismItem::class;
}
