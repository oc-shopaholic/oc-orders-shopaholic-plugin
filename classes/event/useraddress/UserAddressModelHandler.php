<?php namespace Lovata\OrdersShopaholic\Classes\Event\UserAddress;

use Lovata\OrdersShopaholic\Classes\Item\UserAddressItem;
use Lovata\Toolbox\Classes\Event\ModelHandler;
use Lovata\Toolbox\Classes\Helper\UserHelper;

use Lovata\OrdersShopaholic\Classes\Store\UserAddressListStore;

use Lovata\OrdersShopaholic\Models\UserAddress;

/**
 * Class UserAddressModelHandler
 * @package Lovata\OrdersShopaholic\Classes\Event\UserAddress
 * @author  Sergey Zakharevich, s.zakharevich@lovata.com, LOVATA Group
 */
class UserAddressModelHandler extends ModelHandler
{
    /** @var UserAddress */
    protected $obElement;

    /**
     * Add listeners
     * @param \Illuminate\Events\Dispatcher $obEvent
     */
    public function subscribe($obEvent)
    {
        parent::subscribe($obEvent);

        $sUserPluginName = UserHelper::instance()->getPluginName();
        if (empty($sUserPluginName)) {
            return;
        }

        UserAddress::extend(function ($obUserAddress) {
            $this->addAddressRelation($obUserAddress);
        });
    }

    /**
     * Add user relation belongTo for UserAddress model
     * @param UserAddress $obUserAddress
     */
    protected function addAddressRelation($obUserAddress)
    {
        $sUserModelClass = UserHelper::instance()->getUserModel();
        if (empty($sUserModelClass) || !class_exists($sUserModelClass)) {
            return;
        }

        $obUserAddress->belongsTo['user'] = $sUserModelClass;
    }

    /**
     * Get model class name
     * @return string
     */
    protected function getModelClass()
    {
        return UserAddress::class;
    }

    /**
     * Get item class name
     * @return string
     */
    protected function getItemClass()
    {
        return UserAddressItem::class;
    }

    /**
     * After save event handler
     */
    protected function afterSave()
    {
        parent::afterSave();

        $this->checkFieldChanges('user_id', UserAddressListStore::instance()->user);
    }

    /**
     * After delete event handler
     */
    protected function afterDelete()
    {
        parent::afterDelete();

        UserAddressListStore::instance()->user->clear($this->obElement->user_id);
    }
}
