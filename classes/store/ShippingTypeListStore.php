<?php namespace Lovata\OrdersShopaholic\Classes\Store;

use Kharanenka\Helper\CCache;
use Lovata\Toolbox\Traits\Store\TraitActiveList;

use Lovata\Shopaholic\Plugin;
use Lovata\OrdersShopaholic\Models\ShippingType;

/**
 * Class ShippingTypeListStore
 * @package Lovata\OrdersShopaholic\Classes\Store
 * @author Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class ShippingTypeListStore
{
    use TraitActiveList;
    
    const CACHE_TAG_LIST = 'order-shopaholic-shipping-type-list';

    /**
     * Get brand ID list with sorting
     * @return array
     */
    public function getBySorting()
    {
        //Get cache data
        $arCacheTags = [Plugin::CACHE_TAG, self::CACHE_TAG_LIST];
        $sCacheKey = 'sorting';

        $arShippingTypeIDList = CCache::get($arCacheTags, $sCacheKey);
        if(!empty($arShippingTypeIDList)) {
            return $arShippingTypeIDList;
        }

        //Get brand ID list with sorting by sort_order field
        /** @var array $arShippingTypeIDList */
        $arShippingTypeIDList = ShippingType::orderBy('sort_order', 'asc')->lists('id');

        //Set cache data
        CCache::forever($arCacheTags, $sCacheKey, $arShippingTypeIDList);

        return $arShippingTypeIDList;
    }

    /**
     * Clear sorting list
     */
    public function clearSortingList()
    {
        //Get cache data
        $arCacheTags = [Plugin::CACHE_TAG, self::CACHE_TAG_LIST];
        $sCacheKey = 'sorting';

        //Clear cache data
        CCache::clear($arCacheTags, $sCacheKey);
        $this->getBySorting();
    }

    /**
     * Get brand active ID list
     * @return array
     */
    protected function getActiveIDList()
    {
        /** @var array $arShippingTypeIDList */
        $arShippingTypeIDList = ShippingType::active()->lists('id');
        return $arShippingTypeIDList;
    }
}