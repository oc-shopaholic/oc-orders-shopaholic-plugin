<?php namespace Lovata\OrdersShopaholic\Classes\Item;

use Lovata\Toolbox\Classes\Item\ElementItem;
use Lovata\OrdersShopaholic\Models\ShippingRestriction;

/**
 * Class ShippingRestrictionItem
 * @package Lovata\Shopaholic\Classes\Item
 */
class ShippingRestrictionItem extends ElementItem
{
    const MODEL_CLASS = ShippingRestriction::class;

    /** @var ShippingRestriction */
    protected $obElement = null;

    
}