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
     * Get payment method type item by code
     * @param string $sCode
     *
     * @return PaymentMethodItem
     */
    public function getByCode($sCode)
    {
        if ($this->isEmpty() || empty($sCode)) {
            return PaymentMethodItem::make(null);
        }

        $arPaymentMethodList = $this->all();

        /** @var PaymentMethodItem $obPaymentMethodItem */
        foreach ($arPaymentMethodList as $obPaymentMethodItem) {
            if ($obPaymentMethodItem->code == $sCode) {
                return $obPaymentMethodItem;
            }
        }

        return PaymentMethodItem::make(null);
    }
}
