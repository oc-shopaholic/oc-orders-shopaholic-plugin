<?php namespace Lovata\OrdersShopaholic\Classes\Collection;

use Lovata\Toolbox\Classes\Collection\ElementCollection;

use Lovata\OrdersShopaholic\Classes\Item\PaymentMethodItem;
use Lovata\OrdersShopaholic\Classes\Store\PaymentMethodListStore;

/**
 * Class PaymentMethodCollection
 * @package Lovata\Shopaholic\Classes\Collection
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class PaymentMethodCollection extends ElementCollection
{
    const ITEM_CLASS = PaymentMethodItem::class;

    /**
     * Sort list
     * @return $this
     */
    public function sort()
    {
        //Get sorting list
        $arElementIDList = PaymentMethodListStore::instance()->sorting->get();

        return $this->applySorting($arElementIDList);
    }

    /**
     * Apply filter by active field
     * @return $this
     */
    public function active()
    {
        $arElementIDList = PaymentMethodListStore::instance()->active->get();

        return $this->intersect($arElementIDList);
    }
}
