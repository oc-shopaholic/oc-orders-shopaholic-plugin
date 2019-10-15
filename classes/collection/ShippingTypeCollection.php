<?php namespace Lovata\OrdersShopaholic\Classes\Collection;

use Lovata\Toolbox\Classes\Collection\ElementCollection;

use Lovata\OrdersShopaholic\Classes\Item\ShippingTypeItem;
use Lovata\OrdersShopaholic\Classes\Store\ShippingTypeListStore;

/**
 * Class ShippingTypeCollection
 * @package Lovata\Shopaholic\Classes\Collection
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class ShippingTypeCollection extends ElementCollection
{
    const ITEM_CLASS = ShippingTypeItem::class;

    /**
     * Sort list
     * @return $this
     */
    public function sort()
    {
        //Get sorting list
        $arElementIDList = ShippingTypeListStore::instance()->sorting->get();

        return $this->applySorting($arElementIDList);
    }

    /**
     * Apply filter by active field
     * @return $this
     */
    public function active()
    {
        $arElementIDList = ShippingTypeListStore::instance()->active->get();

        return $this->intersect($arElementIDList);
    }

    /**
     * Apply filter by restrictions
     * @param array $arData
     * @return $this
     */
    public function available($arData = null)
    {
        if ($this->isEmpty()) {
            return $this->returnThis();
        }

        $arElementList = $this->all();
        $arExcludeIDList = [];

        /** @var ShippingTypeItem $obElement */
        foreach ($arElementList as $obElement) {
            if (!$obElement->isAvailable($arData)) {
                $arExcludeIDList[] = $obElement->id;
            }
        }

        if (empty($arExcludeIDList)) {
            $this->returnThis();
        }

        return $this->diff($arExcludeIDList);
    }

    /**
     * Get shipping type item by code
     * @param string $sCode
     *
     * @return ShippingTypeItem
     */
    public function getByCode($sCode)
    {
        if ($this->isEmpty() || empty($sCode)) {
            return ShippingTypeItem::make(null);
        }

        $arShippingTypeList = $this->all();

        /** @var ShippingTypeItem $obShippingTypeItem */
        foreach ($arShippingTypeList as $obShippingTypeItem) {
            if ($obShippingTypeItem->code == $sCode) {
                return $obShippingTypeItem;
            }
        }

        return ShippingTypeItem::make(null);
    }
}
