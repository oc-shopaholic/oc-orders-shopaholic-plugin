<?php namespace Lovata\OrdersShopaholic\Classes\Event;

use Lovata\Toolbox\Classes\Event\ModelHandler;

use Lovata\OrdersShopaholic\Models\Status;
use Lovata\OrdersShopaholic\Classes\Item\StatusItem;
use Lovata\OrdersShopaholic\Classes\Store\StatusListStore;

/**
 * Class StatusModelHandler
 * @package Lovata\OrdersShopaholic\Classes\Event
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class StatusModelHandler extends ModelHandler
{
    /** @var  StatusListStore */
    protected $obListStore;

    /**
     * StatusModelHandler constructor.
     */
    public function __construct()
    {
        $this->obListStore = StatusListStore::instance();
    }

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

        $this->checkFieldChanges('is_user_show', $this->obListStore->is_user_show);
    }

    /**
     * After delete event handler
     */
    protected function afterDelete()
    {
        parent::afterDelete();
        $this->clearSortingList();

        if ($this->obListStore->is_user_show) {
            $this->obListStore->is_user_show->clear();
        }
    }

    /**
     * Clear sorting list
     */
    public function clearSortingList()
    {
        $this->obListStore->sorting->clear();
    }
}
