<?php namespace Lovata\OrdersShopaholic\Classes\Event\ShippingRestriction;

use Lovata\Toolbox\Classes\Event\AbstractBackendFieldHandler;

use Lovata\OrdersShopaholic\Controllers\ShippingTypes;
use Lovata\OrdersShopaholic\Models\ShippingRestriction;

/**
 * Class ExtendShippingRestrictionFieldsHandler
 * @package Lovata\OrdersShopaholic\Classes\Event\ShippingRestriction
 */
class ExtendShippingRestrictionFieldsHandler extends AbstractBackendFieldHandler
{
    /**
     * Extend form fields
     * @param \Backend\Widgets\Form $obWidget
     */
    protected function extendFields($obWidget)
    {
        $sRestrictionClass = $obWidget->model->restriction;
        if (empty($sRestrictionClass) || !class_exists($sRestrictionClass)) {
            return;
        }

        $arFieldList = $sRestrictionClass::getFields();
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
        return ShippingRestriction::class;
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
