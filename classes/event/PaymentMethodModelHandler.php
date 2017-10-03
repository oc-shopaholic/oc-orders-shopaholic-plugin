<?php namespace Lovata\OrdersShopaholic\Classes\Event;

use Lovata\Toolbox\Classes\Event\ModelHandler;

use Lovata\OrdersShopaholic\Models\PaymentMethod;
use Lovata\OrdersShopaholic\Classes\Item\PaymentMethodItem;
use Lovata\OrdersShopaholic\Classes\Store\PaymentMethodListStore;

/**
 * Class PaymentMethodModelHandler
 * @package Lovata\OrdersShopaholic\Classes\Event
 * @author Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class PaymentMethodModelHandler extends ModelHandler
{
    /** @var  PaymentMethodListStore */
    protected $obListStore;

    /**
     * PaymentMethodModelHandler constructor.
     *
     * @param PaymentMethodListStore $obPaymentMethodListStore
     */
    public function __construct(PaymentMethodListStore $obPaymentMethodListStore)
    {
        $this->obListStore = $obPaymentMethodListStore;
    }

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
     * After delete event handler
     */
    protected function afterDelete()
    {
        parent::afterDelete();
        $this->clearSortingList();
    }

    /**
     * Clear sorting list
     */
    public function clearSortingList()
    {
        $this->obListStore->clearSortingList();
    }
}