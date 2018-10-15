<?php namespace Lovata\OrdersShopaholic\Classes\Event;

use Lovata\OrdersShopaholic\Controllers\OrderPositions;
use Lovata\OrdersShopaholic\Models\OrderPosition;
use Lovata\OrdersShopaholic\Models\OrderPositionProperty;
use System\Models\MailTemplate;

use Lovata\Toolbox\Classes\Helper\UserHelper;

use Lovata\Shopaholic\Models\Settings;
use Lovata\OrdersShopaholic\Models\Order;
use Lovata\OrdersShopaholic\Models\OrderProperty;
use Lovata\OrdersShopaholic\Controllers\Orders;
use Lovata\OrdersShopaholic\Classes\Processor\CartProcessor;

/**
 * Class ExtendCategoryModel
 * @package Lovata\OrdersShopaholic\Classes\Event
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class ExtendFieldHandler
{
    /**
     * Add listeners
     * @param \Illuminate\Events\Dispatcher $obEvent
     */
    public function subscribe($obEvent)
    {
        $obEvent->listen('backend.form.extendFields', function ($obWidget) {
            $this->extendSettingsFields($obWidget);
            $this->extendOrderFields($obWidget);
            $this->extendOrderPositionFields($obWidget);
        });
    }

    /**
     * Extend settings fields
     * @param \Backend\Widgets\Form $obWidget
     */
    protected function extendSettingsFields($obWidget)
    {
        // Only for the Settings controller
        if (!$obWidget->getController() instanceof \System\Controllers\Settings || $obWidget->isNested) {
            return;
        }

        // Only for the Settings model
        if (!$obWidget->model instanceof Settings) {
            return;
        }

        $arAdditionFieldList = [
            'cart_cookie_lifetime'                 => [
                'tab'     => 'lovata.ordersshopaholic::lang.tab.order_settings',
                'label'   => 'lovata.ordersshopaholic::lang.settings.cart_cookie_lifetime',
                'span'    => 'left',
                'type'    => 'number',
                'default' => CartProcessor::$iCookieLifeTime,
            ],
            'check_offer_quantity'                 => [
                'tab'   => 'lovata.ordersshopaholic::lang.tab.order_settings',
                'label' => 'lovata.ordersshopaholic::lang.settings.check_offer_quantity',
                'span'  => 'left',
                'type'  => 'checkbox',
            ],
            'decrement_offer_quantity'             => [
                'tab'   => 'lovata.ordersshopaholic::lang.tab.order_settings',
                'label' => 'lovata.ordersshopaholic::lang.settings.decrement_offer_quantity',
                'span'  => 'left',
                'type'  => 'checkbox',
            ],
            'send_email_after_creating_order'      => [
                'tab'   => 'lovata.ordersshopaholic::lang.tab.order_settings',
                'label' => 'lovata.ordersshopaholic::lang.settings.send_email_after_creating_order',
                'span'  => 'left',
                'type'  => 'checkbox',
            ],
            'creating_order_mail_template'         => [
                'tab'         => 'lovata.ordersshopaholic::lang.tab.order_settings',
                'label'       => 'lovata.ordersshopaholic::lang.settings.creating_order_mail_template',
                'span'        => 'left',
                'type'        => 'dropdown',
                'emptyOption' => 'lovata.toolbox::lang.field.empty',
                'options'     => MailTemplate::listAllTemplates(),
                'trigger'     => [
                    'action'    => 'show',
                    'field'     => 'send_email_after_creating_order',
                    'condition' => 'checked',
                ],
            ],
            'creating_order_manager_mail_template' => [
                'tab'         => 'lovata.ordersshopaholic::lang.tab.order_settings',
                'label'       => 'lovata.ordersshopaholic::lang.settings.creating_order_manager_mail_template',
                'span'        => 'left',
                'type'        => 'dropdown',
                'emptyOption' => 'lovata.toolbox::lang.field.empty',
                'options'     => MailTemplate::listAllTemplates(),
                'trigger'     => [
                    'action'    => 'show',
                    'field'     => 'send_email_after_creating_order',
                    'condition' => 'checked',
                ],
            ],
            'creating_order_manager_email_list'    => [
                'tab'     => 'lovata.ordersshopaholic::lang.tab.order_settings',
                'label'   => 'lovata.ordersshopaholic::lang.settings.creating_order_manager_email_list',
                'comment' => 'lovata.toolbox::lang.field.email_list_description',
                'span'    => 'left',
                'type'    => 'text',
                'trigger' => [
                    'action'    => 'show',
                    'field'     => 'send_email_after_creating_order',
                    'condition' => 'checked',
                ],
            ],
        ];

        $obWidget->addTabFields($arAdditionFieldList);
        $sUserPluginName = UserHelper::instance()->getPluginName();
        if (empty($sUserPluginName)) {
            return;
        }

        // Add an extra birthday field
        $obWidget->addTabFields([
            'create_new_user'     => [
                'tab'   => 'lovata.ordersshopaholic::lang.tab.order_settings',
                'label' => 'lovata.ordersshopaholic::lang.settings.create_new_user',
                'span'  => 'left',
                'type'  => 'checkbox',
            ],
            'generate_fake_email' => [
                'tab'   => 'lovata.ordersshopaholic::lang.tab.order_settings',
                'label' => 'lovata.ordersshopaholic::lang.settings.generate_fake_email',
                'span'  => 'left',
                'type'  => 'checkbox',
            ],
        ]);
    }

    /**
     * Extend fields for Order model
     * @param \Backend\Widgets\Form $obWidget
     */
    protected function extendOrderFields($obWidget)
    {
        if (!$obWidget->getController() instanceof Orders || $obWidget->isNested) {
            return;
        }

        // Only for the Order model
        if (!$obWidget->model instanceof Order) {
            return;
        }

        $obPropertyList = OrderProperty::active()->orderBy('sort_order', 'asc')->get();

        $this->addOrderPropertyField($obWidget, $obPropertyList);
        $this->removeOrderUserRelationField($obWidget);
        $this->removePaymentDataField($obWidget);
        $this->removeDiscountFields($obWidget);
    }

    /**
     * Add additional order properties
     * @param \Backend\Widgets\Form $obWidget
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
     * Extend fields for OrderPosition model
     * @param \Backend\Widgets\Form $obWidget
     */
    protected function extendOrderPositionFields($obWidget)
    {
        if (!$obWidget->getController() instanceof OrderPositions || $obWidget->isNested) {
            return;
        }

        // Only for the OrderPosition model
        if (!$obWidget->model instanceof OrderPosition) {
            return;
        }

        $obPropertyList = OrderPositionProperty::active()->orderBy('sort_order', 'asc')->get();

        $this->addOrderPropertyField($obWidget, $obPropertyList);
    }
}
