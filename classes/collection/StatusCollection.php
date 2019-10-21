<?php namespace Lovata\OrdersShopaholic\Classes\Collection;

use Lovata\Toolbox\Classes\Collection\ElementCollection;

use Lovata\OrdersShopaholic\Classes\Item\StatusItem;
use Lovata\OrdersShopaholic\Classes\Store\StatusListStore;

/**
 * Class StatusCollection
 * @package Lovata\Shopaholic\Classes\Collection
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class StatusCollection extends ElementCollection
{
    const ITEM_CLASS = StatusItem::class;
    
    /**
     * Sort list
     * @return $this
     */
    public function sort()
    {
        //Get sorting list
        $arElementIDList = StatusListStore::instance()->sorting->get();

        return $this->applySorting($arElementIDList);
    }

    /**
     * Apply filter by active field
     * @return $this
     */
    public function isUserShow()
    {
        $arElementIDList = StatusListStore::instance()->is_user_show->get();

        return $this->intersect($arElementIDList);
    }

    /**
     * Get status item by code
     * @param string $sCode
     *
     * @return StatusItem
     */
    public function getByCode($sCode)
    {
        if ($this->isEmpty() || empty($sCode)) {
            return StatusItem::make(null);
        }

        $arStatusList = $this->all();

        /** @var StatusItem $obStatusItem */
        foreach ($arStatusList as $obStatusItem) {
            if ($obStatusItem->code == $sCode) {
                return $obStatusItem;
            }
        }

        return StatusItem::make(null);
    }
}
