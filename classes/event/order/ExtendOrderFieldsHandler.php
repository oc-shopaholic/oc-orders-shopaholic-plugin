<?php namespace Lovata\OrdersShopaholic\Classes\Event\Order;

use Lovata\Toolbox\Classes\Event\AbstractBackendFieldHandler;
use Lovata\Toolbox\Classes\Helper\UserHelper;

use Lovata\OrdersShopaholic\Models\Order;
use Lovata\OrdersShopaholic\Models\OrderPositionProperty;
use Lovata\OrdersShopaholic\Models\OrderProperty;
use Lovata\OrdersShopaholic\Controllers\Orders;

/**
 * Class ExtendOrderFieldsHandler
 * @package Lovata\OrdersShopaholic\Classes\Event\Order
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class ExtendOrderFieldsHandler extends AbstractBackendFieldHandler
{
    /**
     * Extend form fields
     * @param \Backend\Widgets\Form $obWidget
     */
    protected function extendFields($obWidget)
    {
        $obPropertyList = OrderProperty::active()->orderBy('sort_order', 'asc')->get();

        $this->addOrderPropertyField($obWidget, $obPropertyList);
        $this->removeOrderUserRelationField($obWidget);
        $this->removePaymentDataField($obWidget);
        $this->removeDiscountFields($obWidget);
    }

    /**
     * Add additional order properties
     * @param \Backend\Widgets\Form                                                     $obWidget
     * @param \October\Rain\Database\Collection|OrderProperty[]|OrderPositionProperty[] $obPropertyList
     */
    protected function addOrderPropertyField($obWidget, $obPropertyList)
    {
        if ($obPropertyList->isEmpty()) {
            return;
        }

        //Get widget data for properties
        $arAdditionPropertyData = [];
        /** @var OrderProperty $obProperty */
        foreach ($obPropertyList as $obProperty) {
            $arPropertyData = $obProperty->getWidgetData();
            if (!empty($arPropertyData)) {
                $arAdditionPropertyData[OrderProperty::NAME.'['.$obProperty->code.']'] = $arPropertyData;
            }
        }

        // Add fields
        if (!empty($arAdditionPropertyData)) {
            $obWidget->addTabFields($arAdditionPropertyData);
        }
    }

    /**
     * Add user relation field
     * @param \Backend\Widgets\Form $obWidget
     */
    protected function removeOrderUserRelationField($obWidget)
    {
        $sUserModelClass = UserHelper::instance()->getUserModel();
        if (empty($sUserModelClass)) {
            $obWidget->removeField('user');
        }
    }

    /**
     * Add payment data fields
     * @param \Backend\Widgets\Form $obWidget
     */
    protected function removePaymentDataField($obWidget)
    {
        //Get payment gateway
        /** @var \Lovata\OrdersShopaholic\Models\PaymentMethod $obPaymentMethod */
        $obPaymentMethod = $obWidget->model->payment_method;
        if (!empty($obPaymentMethod) && !empty($obPaymentMethod->gateway)) {
            return;
        }

        $obWidget->removeField('transaction_id');
        $obWidget->removeField('payment_token');
        $obWidget->removeField('payment_data_json');
        $obWidget->removeField('payment_response_json');
    }

    /**
     * Add discount fields
     * @param \Backend\Widgets\Form $obWidget
     */
    protected function removeDiscountFields($obWidget)
    {
        //Get promo mechanism list
        $obMechanismList = $obWidget->model->order_promo_mechanism;
        if ($obMechanismList->isNotEmpty()) {
            return;
        }

        $obWidget->removeField('order_promo_mechanism');
        $obWidget->removeField('discount_log_position_total_price');
        $obWidget->removeField('discount_log_shipping_price');
        $obWidget->removeField('discount_log_total_price');
    }

    /**
     * Get model class name
     * @return string
     */
    protected function getModelClass() : string
    {
        return Order::class;
    }

    /**
     * Get controller class name
     * @return string
     */
    protected function getControllerClass() : string
    {
        return Orders::class;
    }
}
