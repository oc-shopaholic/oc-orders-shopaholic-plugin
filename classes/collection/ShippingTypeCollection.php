<?php namespace Lovata\OrdersShopaholic\Classes\Collection;

use Lovata\Toolbox\Classes\Collection\ElementCollection;

use Lovata\OrdersShopaholic\Classes\Item\ShippingTypeItem;
use Lovata\OrdersShopaholic\Classes\Store\ShippingTypeListStore;

/**
 * Class ShippingTypeCollection
 * @package Lovata\Shopaholic\Classes\Collection
 * @author Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class ShippingTypeCollection extends ElementCollection
{
    /** @var ShippingTypeListStore */
    protected $obShippingTypeListStore;

    /**
     * ShippingTypeCollection constructor.
     * @param ShippingTypeListStore $obShippingTypeListStore
     */
    public function __construct(ShippingTypeListStore $obShippingTypeListStore)
    {
        $this->obShippingTypeListStore = $obShippingTypeListStore;
        parent::__construct();
    }
    
    /**
     * Make element item
     * @param int   $iElementID
     * @param \Lovata\OrdersShopaholic\Models\ShippingType  $obElement
     *
     * @return ShippingTypeItem
     */
    protected function makeItem($iElementID, $obElement = null)
    {
        return ShippingTypeItem::make($iElementID, $obElement);
    }

    /**
     * Sort list
     * @return $this
     */
    public function sort()
    {
        if(!$this->isClear() && $this->isEmpty()) {
            return $this->returnThis();
        }

        //Get sorting list
        $arElementIDList = $this->obShippingTypeListStore->getBySorting();
        if(empty($arElementIDList)) {
            return $this->clear();
        }

        if($this->isClear()) {
            $this->arElementIDList = $arElementIDList;
            return $this->returnThis();
        }

        $this->arElementIDList = array_intersect($arElementIDList, $this->arElementIDList);
        return $this->returnThis();
    }
    
    /**
     * Apply filter by active product list0
     * @return $this
     */
    public function active()
    {
        $arElementIDList = $this->obShippingTypeListStore->getActiveList();
        return $this->intersect($arElementIDList);
    }
}