<?php namespace Lovata\OrdersShopaholic\Classes\Event;

use Event;

use Lovata\OrdersShopaholic\Models\ShippingRestriction;
use Lovata\OrdersShopaholic\Controllers\ShippingTypes;
use Lovata\OrdersShopaholic\Classes\Event\Restriction\ByPrice;

/**
 * Class ExtendShippingRestrictionFieldHandler
 * @package Lovata\OrdersShopaholic\Classes\Event
 */
class ExtendShippingRestrictionFieldHandler
{
    /**
     * Add listeners
     * @param \Illuminate\Events\Dispatcher $obEvent
     */
    public function subscribe($obEvent)
    {
        $obEvent->listen('backend.form.extendFields', function ($obWidget) {
            $this->extendBackendFields($obWidget);
        });

        $obEvent->listen(ShippingRestriction::EVENT_GET_SHIPPING_RESTRICTION_LIST, function () {
            return [
                ByPrice::class => "lovata.ordersshopaholic::lang.restriction.handler.by_price"
            ];
        });
    }

    /**
     * Extend ShippingTypes fields
     * @param \Backend\Widgets\Form $obWidget
     */
    protected function extendBackendFields($obWidget)
    {
        if (!$obWidget->getController() instanceof ShippingTypes || $obWidget->isNested) {
            return;
        }

        if (!$obWidget->model instanceof ShippingRestriction || is_null($obWidget->model->id)) {
            return;
        }

        if (empty($obWidget->model->restriction) || !class_exists($obWidget->model->restriction)) {
            return;
        }

        $obWidget->addTabFields( (new $obWidget->model->restriction)->getFields() );
    }
}
