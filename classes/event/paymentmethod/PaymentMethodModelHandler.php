<?php namespace Lovata\OrdersShopaholic\Classes\Event\PaymentMethod;

use Lovata\Toolbox\Classes\Event\ModelHandler;

use Lovata\OrdersShopaholic\Models\PaymentMethod;
use Lovata\OrdersShopaholic\Classes\Item\PaymentMethodItem;
use Lovata\OrdersShopaholic\Classes\Store\PaymentMethodListStore;

/**
 * Class PaymentMethodModelHandler
 * @package Lovata\OrdersShopaholic\Classes\Event\PaymentMethod
 * @author Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class PaymentMethodModelHandler extends ModelHandler
{
    /** @var PaymentMethod */
    protected $obElement;

    /**
     * Add listeners
     * @param \Illuminate\Events\Dispatcher $obEvent
     */
    public function subscribe($obEvent)
    {
        parent::subscribe($obEvent);

        $obEvent->listen('shopaholic.payment_method.update.sorting', function () {
            $this->clearSortingList();
        });
    }

    /**
     * Get model class name
     * @return string
     */
    protected function getModelClass()
    {
        return PaymentMethod::class;
    }
    
    /**
     * Get item class name
     * @return string
     */
    protected function getItemClass()
    {
        return PaymentMethodItem::class;
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

        $this->checkFieldChanges('active', PaymentMethodListStore::instance()->active);
    }

    /**
     * After delete event handler
     */
    protected function afterDelete()
    {
        parent::afterDelete();
        $this->clearSortingList();

        if ($this->obElement->active) {
            PaymentMethodListStore::instance()->active->clear();
        }
    }

    /**
     * Clear sorting list
     */
    public function clearSortingList()
    {
        PaymentMethodListStore::instance()->sorting->clear();
    }
}
