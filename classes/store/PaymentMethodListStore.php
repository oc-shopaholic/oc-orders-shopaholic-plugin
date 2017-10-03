<?php namespace Lovata\OrdersShopaholic\Classes\Store;

use Kharanenka\Helper\CCache;

use Lovata\Shopaholic\Plugin;
use Lovata\OrdersShopaholic\Models\PaymentMethod;
use Lovata\Toolbox\Traits\Store\TraitActiveList;

/**
 * Class PaymentMethodListStore
 * @package Lovata\OrdersShopaholic\Classes\Store
 * @author Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class PaymentMethodListStore
{
    use TraitActiveList;
    
    const CACHE_TAG_LIST = 'order-shopaholic-payment-method-list';

    /**
     * Get brand ID list with sorting
     * @return array
     */
    public function getBySorting()
    {
        //Get cache data
        $arCacheTags = [Plugin::CACHE_TAG, self::CACHE_TAG_LIST];
        $sCacheKey = 'sorting';

        $arPaymentMethodIDList = CCache::get($arCacheTags, $sCacheKey);
        if(!empty($arPaymentMethodIDList)) {
            return $arPaymentMethodIDList;
        }

        //Get brand ID list with sorting by sort_order field
        /** @var array $arPaymentMethodIDList */
        $arPaymentMethodIDList = PaymentMethod::orderBy('sort_order', 'asc')->lists('id');

        //Set cache data
        CCache::forever($arCacheTags, $sCacheKey, $arPaymentMethodIDList);

        return $arPaymentMethodIDList;
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
        /** @var array $arPaymentMethodIDList */
        $arPaymentMethodIDList = PaymentMethod::active()->lists('id');
        return $arPaymentMethodIDList;
    }
}