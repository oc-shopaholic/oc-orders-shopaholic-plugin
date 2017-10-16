<?php namespace Lovata\OrdersShopaholic\Classes\Event;

use Lovata\Toolbox\Classes\Event\ModelHandler;

use Lovata\OrdersShopaholic\Models\ShippingType;
use Lovata\OrdersShopaholic\Classes\Item\ShippingTypeItem;
use Lovata\OrdersShopaholic\Classes\Store\ShippingTypeListStore;

/**
 * Class ShippingTypeModelHandler
 * @package Lovata\OrdersShopaholic\Classes\Event
 * @author Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class ShippingTypeModelHandler extends ModelHandler
{
    /** @var  ShippingTypeListStore */
    protected $obListStore;

    /**
     * ShippingTypeModelHandler constructor.
     *
     * @param ShippingTypeListStore $obShippingTypeListStore
     */
    public function __construct(ShippingTypeListStore $obShippingTypeListStore)
    {
        $this->obListStore = $obShippingTypeListStore;
    }

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