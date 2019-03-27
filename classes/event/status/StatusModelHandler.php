<?php namespace Lovata\OrdersShopaholic\Classes\Event\Status;

use Lovata\Toolbox\Classes\Event\ModelHandler;

use Lovata\OrdersShopaholic\Models\Status;
use Lovata\OrdersShopaholic\Classes\Item\StatusItem;
use Lovata\OrdersShopaholic\Classes\Store\StatusListStore;

/**
 * Class StatusModelHandler
 * @package Lovata\OrdersShopaholic\Classes\Event\Status
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class StatusModelHandler extends ModelHandler
{
    /** @var Status */
    protected $obElement;

    /**
     * Add listeners
     * @param \Illuminate\Events\Dispatcher $obEvent
     */
    public function subscribe($obEvent)
    {
        parent::subscribe($obEvent);

        $obEvent->listen('shopaholic.order_status.update.sorting', function () {
            $this->clearSortingList();
        });
    }

    /**
     * Get model class name
     * @return string
     */
    protected function getModelClass()
    {
        return Status::class;
    }

    /**
     * Get item class name
     * @return string
     */
    protected function getItemClass()
    {
        return StatusItem::class;
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

        $this->checkFieldChanges('is_user_show', StatusListStore::instance()->is_user_show);
    }

    /**
     * After delete event handler
     */
    protected function afterDelete()
    {
        parent::afterDelete();
        $this->clearSortingList();

        if ($this->obElement->is_user_show) {
            StatusListStore::instance()->is_user_show->clear();
        }
    }

    /**
     * Clear sorting list
     */
    public function clearSortingList()
    {
        StatusListStore::instance()->sorting->clear();
    }
}
