<?php namespace Lovata\OrdersShopaholic\Classes\Event\PaymentMethod;

use Event;
use Lovata\Toolbox\Classes\Event\AbstractBackendFieldHandler;

use Lovata\OrdersShopaholic\Models\PaymentMethod;
use Lovata\OrdersShopaholic\Controllers\PaymentMethods;

/**
 * Class ExtendPaymentMethodFieldsHandler
 * @package Lovata\OrdersShopaholic\Classes\Event\PaymentMethod
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class ExtendPaymentMethodFieldsHandler extends AbstractBackendFieldHandler
{
    /**
     * Extend form fields
     * @param \Backend\Widgets\Form $obWidget
     */
    protected function extendFields($obWidget)
    {
        //Get gateway list
        $arGatewayList = $this->getPaymentGatewayList();
        if (empty($arGatewayList)) {
            return;
        }

        $arFieldList = [
            'gateway_id' => [
                'label'       => 'lovata.ordersshopaholic::lang.field.gateway_id',
                'tab'         => 'lovata.ordersshopaholic::lang.tab.gateway',
                'emptyOption' => 'lovata.toolbox::lang.field.empty',
                'span'        => 'left',
                'type'        => 'dropdown',
                'options'     => $arGatewayList,
            ],
        ];

        $obWidget->addTabFields($arFieldList);
        if (empty($obWidget->model->gateway_id)) {
            return;
        }

        $this->addPaymentGatewayField($obWidget);
    }

    /**
     * Add payment gateway fields
     * @param \Backend\Widgets\Form $obWidget
     */
    protected function addPaymentGatewayField($obWidget)
    {
        $arFieldList = [
            'gateway_currency' => [
                'tab'      => 'lovata.ordersshopaholic::lang.tab.gateway',
                'label'    => 'lovata.ordersshopaholic::lang.field.gateway_currency',
                'span'     => 'right',
                'type'     => 'text',
                'required' => true,
            ],
            'before_status'    => [
                'tab'         => 'lovata.ordersshopaholic::lang.tab.gateway',
                'label'       => 'lovata.ordersshopaholic::lang.field.before_status_id',
                'emptyOption' => 'lovata.toolbox::lang.field.empty',
                'span'        => 'left',
                'type'        => 'relation',
            ],
            'after_status'     => [
                'tab'         => 'lovata.ordersshopaholic::lang.tab.gateway',
                'label'       => 'lovata.ordersshopaholic::lang.field.after_status_id',
                'emptyOption' => 'lovata.toolbox::lang.field.empty',
                'span'        => 'right',
                'type'        => 'relation',
            ],
            'cancel_status'    => [
                'tab'         => 'lovata.ordersshopaholic::lang.tab.gateway',
                'label'       => 'lovata.ordersshopaholic::lang.field.cancel_status_id',
                'emptyOption' => 'lovata.toolbox::lang.field.empty',
                'span'        => 'left',
                'type'        => 'relation',
            ],
            'fail_status'    => [
                'tab'         => 'lovata.ordersshopaholic::lang.tab.gateway',
                'label'       => 'lovata.ordersshopaholic::lang.field.fail_status_id',
                'emptyOption' => 'lovata.toolbox::lang.field.empty',
                'span'        => 'right',
                'type'        => 'relation',
            ],
            'send_purchase_request'    => [
                'tab'         => 'lovata.ordersshopaholic::lang.tab.gateway',
                'label'       => 'lovata.ordersshopaholic::lang.field.send_purchase_request',
                'span'        => 'left',
                'type'        => 'switch',
            ],
            'restore_cart'    => [
                'tab'         => 'lovata.ordersshopaholic::lang.tab.gateway',
                'label'       => 'lovata.ordersshopaholic::lang.field.restore_cart',
                'span'        => 'left',
                'type'        => 'switch',
            ],
        ];

        $obWidget->addTabFields($arFieldList);
    }

    /**
     * Get payment gateway list
     * @return array
     */
    protected function getPaymentGatewayList()
    {
        $arResult = [];

        $arEventResult = Event::fire(PaymentMethod::EVENT_GET_GATEWAY_LIST);
        if (empty($arEventResult)) {
            return $arEventResult;
        }

        foreach ($arEventResult as $arGatewayList) {
            if (empty($arGatewayList) || !is_array($arGatewayList)) {
                continue;
            }

            $arResult = array_merge($arResult, $arGatewayList);
        }

        asort($arResult);

        return $arResult;
    }

    /**
     * Get model class name
     * @return string
     */
    protected function getModelClass() : string
    {
        return PaymentMethod::class;
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
