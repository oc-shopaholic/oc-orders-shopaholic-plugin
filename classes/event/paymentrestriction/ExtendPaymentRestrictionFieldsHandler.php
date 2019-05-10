<?php namespace Lovata\OrdersShopaholic\Classes\Event\PaymentRestriction;

use Lovata\Toolbox\Classes\Event\AbstractBackendFieldHandler;

use Lovata\OrdersShopaholic\Controllers\PaymentMethods;
use Lovata\OrdersShopaholic\Models\PaymentRestriction;

/**
 * Class ExtendPaymentRestrictionFieldsHandler
 * @package Lovata\OrdersShopaholic\Classes\Event\PaymentRestriction
 * @author  Tsagan Noniev, deploy@rubium.ru, Rubium Web
 */
class ExtendPaymentRestrictionFieldsHandler extends AbstractBackendFieldHandler
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
        return PaymentRestriction::class;
    }

    /**
     * Get controller class name
     * @return string
     */
    protected function getControllerClass() : string
    {
        return PaymentMethods::class;
    }
}
