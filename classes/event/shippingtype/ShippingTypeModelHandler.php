<?php namespace Lovata\OrdersShopaholic\Classes\Event\ShippingType;

use Lovata\Toolbox\Classes\Event\ModelHandler;

use Lovata\OrdersShopaholic\Models\ShippingType;
use Lovata\OrdersShopaholic\Classes\Item\ShippingTypeItem;
use Lovata\OrdersShopaholic\Classes\Store\ShippingTypeListStore;

/**
 * Class ShippingTypeModelHandler
 * @package Lovata\OrdersShopaholic\Classes\Event\ShippingType
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class ShippingTypeModelHandler extends ModelHandler
{
    /** @var ShippingType */
    protected $obElement;

    /**
     * Add listeners
     * @param \Illuminate\Events\Dispatcher $obEvent
     */
    public function subscribe($obEvent)
    {
        parent::subscribe($obEvent);

        $obEvent->listen('shopaholic.shipping_type.update.sorting', function () {
            $this->clearSortingList();
        });
    }

    /**
     * Get model class name
     * @return string
     */
    protected function getModelClass()
    {
        return ShippingType::class;
    }

    /**
     * Get item class name
     * @return string
     */
    protected function getItemClass()
    {
        return ShippingTypeItem::class;
    }

    /**
     * After create event handler
     */
    protected function afterCreate()
    {
        parent::afterCreate();
        $this->clearSortingList();
    }

    /**
     * After save event handler
     */
    protected function afterSave()
    {
        parent::afterSave();

        $this->checkFieldChanges('active', ShippingTypeListStore::instance()->active);
    }

    /**
     * After delete event handler
     */
    protected function afterDelete()
    {
        parent::afterDelete();
        $this->clearSortingList();

        if ($this->obElement->active) {
            ShippingTypeListStore::instance()->active->clear();
        }
    }

    /**
     * Clear sorting list
     */
    public function clearSortingList()
    {
        ShippingTypeListStore::instance()->sorting->clear();
    }
}
