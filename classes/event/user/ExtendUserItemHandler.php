<?php namespace Lovata\OrdersShopaholic\Classes\Event\User;

use Lovata\OrdersShopaholic\Classes\Collection\OrderCollection;
use Lovata\Toolbox\Classes\Helper\UserHelper;

use Lovata\OrdersShopaholic\Classes\Collection\UserAddressCollection;

/**
 * Class ExtendUserItemHandler
 * @package Lovata\OrdersShopaholic\Classes\Event\User
 * @author  Sergey Zakharevich, s.zakharevich@lovata.com, LOVATA Group
 */
class ExtendUserItemHandler
{
    /**
     * Add listeners
     */
    public function subscribe()
    {
        $sUserPluginName = UserHelper::instance()->getPluginName();
        if (empty($sUserPluginName) || $sUserPluginName != 'Lovata.Buddies') {
            return;
        }

        $this->addAddressAttribute();
        $this->addOrderAttribute();
    }

    /**
     * Extend class UserItem and add "getAddressAttribute" method
     */
    public function addAddressAttribute()
    {
        \Lovata\Buddies\Classes\Item\UserItem::extend(function ($obUserItem) {
            /** @var \Lovata\Buddies\Classes\Item\UserItem $obUserItem */
            $obUserItem->addDynamicMethod('getAddressAttribute', function () use ($obUserItem) {
                $obAddressList = UserAddressCollection::make()->user($obUserItem->id);

                return $obAddressList;
            });
        });
    }

    /**
     * Extend class UserItem and add "getOrderAttribute" method
     */
    public function addOrderAttribute()
    {
        \Lovata\Buddies\Classes\Item\UserItem::extend(function($obUserItem) {
            /** @var \Lovata\Buddies\Classes\Item\UserItem $obUserItem */
            $obUserItem->addDynamicMethod('getOrderAttribute', function () use($obUserItem) {
                $obOrderList = OrderCollection::make()->user($obUserItem->id);

                return $obOrderList;
            });
        });
    }
}
