<?php namespace Lovata\OrdersShopaholic\Classes\Event;


use Lovata\Toolbox\Classes\Helper\UserHelper;

use Lovata\OrdersShopaholic\Models\Order;
use Lovata\OrdersShopaholic\Classes\Collection\OrderCollection;

/**
 * Class ExtendUserModelHandler
 * @package Lovata\OrdersShopaholic\Classes\Event
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class ExtendUserModelHandler
{
    /**
     * Add listeners
     */
    public function subscribe()
    {
        $sUserPluginName = UserHelper::instance()->getPluginName();
        if (empty($sUserPluginName)) {
            return;
        }

        $sModelClass = UserHelper::instance()->getUserModel();
        $this->addOrderRelation($sModelClass);

        if ($sUserPluginName == 'Lovata.Buddies') {
            $this->extendUserItem();
        }
    }

    /**
     * Add order relation in User model
     * @param string $sModelClass
     */
    protected function addOrderRelation($sModelClass)
    {
        if (empty($sModelClass) || !class_exists($sModelClass)) {
            return;
        }

        $sModelClass::extend(function($obUser) {

            /** @var \Lovata\Buddies\Models\User $obUser */
            $obUser->hasMany['order'] = [
                Order::class
            ];

            $obUser->addDynamicMethod('getOrderListAttribute', function() use ($obUser) {
                return OrderCollection::make()->user($obUser->id);
            });
        });
    }

    /**
     * Extend UserItem class
     */
    protected function extendUserItem()
    {
        \Lovata\Buddies\Classes\Item\UserItem::extend(function($obUserItem) {
            /** @var \Lovata\Buddies\Classes\Item\UserItem $obUserItem */
            $obUserItem->addDynamicMethod('getOrderAttribute', function () use($obUserItem) {
                $obOrderList = $obUserItem->getAttribute('order');
                if (!empty($obOrderList) && $obOrderList instanceof OrderCollection) {
                    return $obOrderList;
                }

                $obOrderList = OrderCollection::make()->user($obUserItem->id);
                $obUserItem->setAttribute('order', $obOrderList);

                return $obOrderList;
            });
        });
    }
}
