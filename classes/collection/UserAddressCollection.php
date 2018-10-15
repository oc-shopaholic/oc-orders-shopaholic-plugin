<?php namespace Lovata\OrdersShopaholic\Classes\Collection;

use Lovata\Toolbox\Classes\Helper\UserHelper;
use Lovata\Toolbox\Classes\Collection\ElementCollection;

use Lovata\OrdersShopaholic\Classes\Item\UserAddressItem;
use Lovata\OrdersShopaholic\Classes\Store\UserAddressListStore;

/**
 * Class UserAddressCollection
 * @package Lovata\OrdersShopaholic\Classes\Collection
 * @author  Sergey Zakharevich, s.zakharevich@lovata.com, LOVATA Group
 */
class UserAddressCollection extends ElementCollection
{
    const ITEM_CLASS = UserAddressItem::class;

    /**
     * Filter address list by user ID
     * @param $iUserID
     * @return $this
     */
    public function user($iUserID = null)
    {
        if (empty($iUserID)) {
            $iUserID = UserHelper::instance()->getUserID();
        }

        $arResultIDList = UserAddressListStore::instance()->user->get($iUserID);

        return $this->intersect($arResultIDList);
    }
}
