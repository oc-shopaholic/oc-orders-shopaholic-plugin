<?php namespace Lovata\OrdersShopaholic\Classes\Event\User;

use Lovata\Toolbox\Classes\Helper\UserHelper;

use Lovata\OrdersShopaholic\Models\Task;
use Lovata\OrdersShopaholic\Models\Order;
use Lovata\OrdersShopaholic\Models\UserAddress;
use Lovata\OrdersShopaholic\Classes\Collection\OrderCollection;
use Lovata\OrdersShopaholic\Classes\Store\UserAddressListStore;
use Lovata\OrdersShopaholic\Classes\Collection\UserAddressCollection;

/**
 * Class UserModelHandler
 * @package Lovata\OrdersShopaholic\Classes\Event\User
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class UserModelHandler
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
        $this->addTaskRelation($sModelClass);
        $this->addAddressRelation($sModelClass);

        if ($sUserPluginName == 'Lovata.Buddies') {
            $this->extendAfterMethodModel($sModelClass);
            $this->addAddressAttribute();
            $this->addOrderAttribute();
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

        $sModelClass::extend(function ($obUser) {
            /** @var \Lovata\Buddies\Models\User $obUser */
            $obUser->hasMany['order'] = [
                Order::class
            ];

            $obUser->addDynamicMethod('getOrderListAttribute', function () use ($obUser) {
                return OrderCollection::make()->user($obUser->id);
            });
        });
    }

    /**
     * Add task relation in User model
     * @param string $sModelClass
     */
    protected function addTaskRelation($sModelClass)
    {
        if (empty($sModelClass) || !class_exists($sModelClass)) {
            return;
        }

        $sModelClass::extend(function ($obUser) {
            /** @var \Lovata\Buddies\Models\User $obUser */
            $obUser->hasMany['task'] = [
                Task::class,
            ];
            $obUser->hasMany['active_task'] = [
                Task::class,
                'scope' => 'getActiveTask',
            ];
            $obUser->hasMany['completed_task'] = [
                Task::class,
                'scope' => 'getCompletedTask',
            ];
        });
    }

    /**
     * Add order relation in User model
     * @param string $sModelClass
     */
    protected function addAddressRelation($sModelClass)
    {
        if (empty($sModelClass) || !class_exists($sModelClass)) {
            return;
        }

        $sModelClass::extend(function ($obUser) {
            /** @var \Lovata\Buddies\Models\User $obUser */
            $obUser->hasMany['address'] = [
                UserAddress::class,
                'delete' => true,
            ];

            $obUser->addDynamicMethod('getAddressListAttribute', function () use ($obUser) {
                return UserAddressCollection::make()->user($obUser->id);
            });
        });
    }

    /**
     * Extend afterSave and afterDelete method
     * @param string $sModelClass
     */
    protected function extendAfterMethodModel($sModelClass)
    {
        if (empty($sModelClass) || !class_exists($sModelClass)) {
            return;
        }

        $sModelClass::extend(function ($obUser) {
            /** @var \Lovata\Buddies\Models\User $obUser */
            $obUser->bindEvent('model.afterSave', function () use ($obUser) {
                UserAddressListStore::instance()->user->clear($obUser->id);
            });

            /** @var \Lovata\Buddies\Models\User $obUser */
            $obUser->bindEvent('model.afterDelete', function () use ($obUser) {
                UserAddressListStore::instance()->user->clear($obUser->id);
            });
        });
    }

    /**
     * Extend class UserItem and add "getAddressAttribute" method
     */
    public function addAddressAttribute()
    {
        \Lovata\Buddies\Classes\Item\UserItem::extend(function ($obUserItem) {
            /** @var \Lovata\Buddies\Classes\Item\UserItem $obUserItem */
            $obUserItem->addDynamicMethod('getAddressAttribute', function ($obUserItem) {
                /** @var \Lovata\Buddies\Classes\Item\UserItem $obUserItem */
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
            $obUserItem->addDynamicMethod('getOrderAttribute', function ($obUserItem) {
                /** @var \Lovata\Buddies\Classes\Item\UserItem $obUserItem */
                $obOrderList = OrderCollection::make()->user($obUserItem->id);

                return $obOrderList;
            });
        });
    }
}
