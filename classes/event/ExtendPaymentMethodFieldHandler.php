<?php namespace Lovata\OrdersShopaholic\Classes\Event;

use Event;

use Lovata\OrdersShopaholic\Models\PaymentMethod;
use Lovata\OrdersShopaholic\Controllers\PaymentMethods;

/**
 * Class ExtendPaymentMethodFieldHandler
 * @package Lovata\OrdersShopaholic\Classes\Event
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class ExtendPaymentMethodFieldHandler
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
    }

    /**
     * Extend Product fields
     * @param \Backend\Widgets\Form $obWidget
     */
    protected function extendBackendFields($obWidget)
    {
        if (!$obWidget->getController() instanceof PaymentMethods || $obWidget->isNested) {
            return;
        }

        if (!$obWidget->model instanceof PaymentMethod) {
            return;
        }

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
}
