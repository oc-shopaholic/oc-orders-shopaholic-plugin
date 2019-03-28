<?php namespace Lovata\OrdersShopaholic\Classes\Event\Settings;

use System\Models\MailTemplate;

use Lovata\Toolbox\Classes\Event\AbstractBackendFieldHandler;
use Lovata\Toolbox\Classes\Helper\UserHelper;

use Lovata\Shopaholic\Models\Settings;

use Lovata\OrdersShopaholic\Classes\Processor\CartProcessor;

/**
 * Class ExtendSettingsFieldHandler
 * @package Lovata\OrdersShopaholic\Classes\Event\Settings
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class ExtendSettingsFieldHandler extends AbstractBackendFieldHandler
{
    /**
     * Extend form fields
     * @param \Backend\Widgets\Form $obWidget
     */
    protected function extendFields($obWidget)
    {
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
     * Get model class name
     * @return string
     */
    protected function getModelClass() : string
    {
        return Settings::class;
    }

    /**
     * Get controller class name
     * @return string
     */
    protected function getControllerClass() : string
    {
        return \System\Controllers\Settings::class;
    }
}
