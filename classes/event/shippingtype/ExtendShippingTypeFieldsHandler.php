<?php namespace Lovata\OrdersShopaholic\Classes\Event\ShippingType;

use Lovata\Toolbox\Classes\Event\AbstractBackendFieldHandler;

use Lovata\OrdersShopaholic\Models\ShippingType;
use Lovata\OrdersShopaholic\Controllers\ShippingTypes;

/**
 * Class ExtendShippingTypeFieldsHandler
 * @package Lovata\OrdersShopaholic\Classes\Event\ShippingType
 */
class ExtendShippingTypeFieldsHandler extends AbstractBackendFieldHandler
{
//        $obEvent->listen(OrderProcessor::EVENT_GET_SHIPPING_PRICE, function ($arOrderData) {
//
//            $obShipping = ShippingType::find($arOrderData['shipping_type_id']);
//
//            $obCart = CartProcessor::instance()->getCartObject();
//
//            if (empty($obShipping->method) || !class_exists($obShipping->method)) {
//
//                throw new Exception(Lang::get("lovata.ordersshopaholic::lang.shipping_type.handler.empty_or_not_exists"));
//            }
//
//            return (new $obShipping->method)->run($obShipping, $obCart);
//        });
//
//        $obEvent->listen(ShippingType::EVENT_GET_SHIPPING_PRICE, function ($obShipping) {
//
//            $obCart = CartProcessor::instance()->getCartObject();
//
//            if (empty($obShipping->method) || !class_exists($obShipping->method)) {
//
//                throw new Exception(Lang::get("lovata.ordersshopaholic::lang.shipping_type.handler.empty_or_not_exists"));
//            }
//
//            return (new $obShipping->method)->run($obShipping, $obCart);
//        });
    /**
     * Extend form fields
     * @param \Backend\Widgets\Form $obWidget
     */
    protected function extendFields($obWidget)
    {
        $sApiClass = $obWidget->model->api_class;
        if (empty($sApiClass) || !class_exists($sApiClass)) {
            return;
        }

        $arFieldList = $sApiClass::getFields();
        if (empty($arFieldList)) {
            return;
        }

        $obWidget->addTabFields($arFieldList);
    }

    /**
     * Get model class name
     * @return string
     */
    protected function getModelClass() : string
    {
        return ShippingType::class;
    }

    /**
     * Get controller class name
     * @return string
     */
    protected function getControllerClass() : string
    {
        return ShippingTypes::class;
    }
}
