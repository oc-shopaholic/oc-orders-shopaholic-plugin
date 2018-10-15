<?php namespace Lovata\OrdersShopaholic\Classes\Collection;

use Lovata\Toolbox\Classes\Helper\UserHelper;
use Lovata\Toolbox\Classes\Collection\ElementCollection;

use Lovata\OrdersShopaholic\Classes\Item\OrderItem;
use Lovata\OrdersShopaholic\Classes\Store\OrderListStore;

/**
 * Class OrderCollection
 * @package Lovata\Shopaholic\Classes\Collection
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class OrderCollection extends ElementCollection
{
    const ITEM_CLASS = OrderItem::class;
    
    /**
     * Get order list with filter by user ID
     * @param int $iUserID
     * @return $this
     */
    public function user($iUserID = null)
    {
        if (empty($iUserID)) {
            $iUserID = UserHelper::instance()->getUserID();
        }

        //Get sorting list
        $arElementIDList = OrderListStore::instance()->user->get($iUserID);

        return $this->applySorting($arElementIDList);
    }

    /**
     * Apply filter by status ID
     * @param int $iStatusID
     * @param int $iUserID
     * @return $this
     */
    public function status($iStatusID, $iUserID = null)
    {
        $arElementIDList = OrderListStore::instance()->status->get($iStatusID, $iUserID);

        return $this->intersect($arElementIDList);
    }

    /**
     * Apply filter by shipping type ID
     * @param int $iShippingTypeID
     * @param int $iUserID
     * @return $this
     */
    public function shippingType($iShippingTypeID, $iUserID = null)
    {
        $arElementIDList = OrderListStore::instance()->shipping_type->get($iShippingTypeID, $iUserID);

        return $this->intersect($arElementIDList);
    }

    /**
     * Apply filter by status ID
     * @param int $iPaymentMethodID
     * @param int $iUserID
     * @return $this
     */
    public function paymentMethod($iPaymentMethodID, $iUserID = null)
    {
        $arElementIDList = OrderListStore::instance()->payment_method->get($iPaymentMethodID, $iUserID);

        return $this->intersect($arElementIDList);
    }
}
