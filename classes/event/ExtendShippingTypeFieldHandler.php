<?php namespace Lovata\OrdersShopaholic\Classes\Event;

use Lang;
use Event;
use Exception;
use Lovata\OrdersShopaholic\Models\ShippingType;
use Lovata\OrdersShopaholic\Controllers\ShippingTypes;
use Lovata\OrdersShopaholic\Classes\Event\Shipping\Standard;
use Lovata\OrdersShopaholic\Classes\Processor\CartProcessor;
use Lovata\OrdersShopaholic\Classes\Processor\OrderProcessor;

/**
 * Class ExtendShippingTypeFieldHandler
 * @package Lovata\OrdersShopaholic\Classes\Event
 */
class ExtendShippingTypeFieldHandler
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

        $obEvent->listen(OrderProcessor::EVENT_GET_SHIPPING_PRICE, function ($arOrderData) {

            $obShipping = ShippingType::find($arOrderData['shipping_type_id']);

            $obCart = CartProcessor::instance()->getCartObject();

            if (empty($obShipping->method) || !class_exists($obShipping->method)) {

                throw new Exception(Lang::get("lovata.ordersshopaholic::lang.shipping_type.handler.empty_or_not_exists"));
            }

            return (new $obShipping->method)->run($obShipping, $obCart);
        });

        $obEvent->listen(ShippingType::EVENT_GET_SHIPPING_PRICE, function ($obShipping) {

            $obCart = CartProcessor::instance()->getCartObject();

            if (empty($obShipping->method) || !class_exists($obShipping->method)) {

                throw new Exception(Lang::get("lovata.ordersshopaholic::lang.shipping_type.handler.empty_or_not_exists"));
            }

            return (new $obShipping->method)->run($obShipping, $obCart);
        });

        $obEvent->listen(ShippingType::EVENT_GET_SHIPPING_TYPE_LIST, function () {
            return [
                Standard::class => "lovata.ordersshopaholic::lang.shipping_type.handler.standard"
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

        if (!$obWidget->model instanceof ShippingType || is_null($obWidget->model->id)) {
            return;
        }

        if (empty($obWidget->model->method) || !class_exists($obWidget->model->method)) {
            return;
        }

        $obWidget->addTabFields( (new $obWidget->model->method)->getFields() );
    }
}
