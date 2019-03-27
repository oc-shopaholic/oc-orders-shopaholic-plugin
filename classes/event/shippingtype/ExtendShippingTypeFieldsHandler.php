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
