<?php namespace Lovata\OrdersShopaholic\Classes\Event;

use Lovata\Shopaholic\Models\Settings;
use Lovata\OrdersShopaholic\Classes\CartData;

/**
 * Class ExtendCategoryModel
 * @package Lovata\OrdersShopaholic\Classes\Event
 * @author Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class ExtendFieldHandler
{
    /**
     * Add listeners
     * @param \Illuminate\Events\Dispatcher $obEvent
     */
    public function subscribe($obEvent)
    {
        $obEvent->listen('backend.form.extendFields', function($obWidget) {
            $this->extendSettingsFields($obWidget);
        });
    }

    /**
     * Extend settings fields
     * @param \Backend\Widgets\Form $obWidget
     */
    protected function extendSettingsFields($obWidget)
    {
        // Only for the Settings controller
        if (!$obWidget->getController() instanceof \System\Controllers\Settings) {
            return;
        }

        // Only for the Settings model
        if (!$obWidget->model instanceof Settings) {
            return;
        }

        // Add an extra birthday field
        $obWidget->addTabFields([
            'cart_cookie_lifetime' => [
                'tab'           => 'lovata.ordersshopaholic::lang.tab.order_settings',
                'label'         => 'lovata.ordersshopaholic::lang.settings.cart_cookie_lifetime',
                'span'          => 'left',
                'type'          => 'number',
                'default'       => CartData::$iCookieLifeTime,
            ],
            'check_quantity_on_order' => [
                'tab'           => 'lovata.ordersshopaholic::lang.tab.order_settings',
                'label'         => 'lovata.ordersshopaholic::lang.settings.check_quantity_on_order',
                'span'          => 'left',
                'type'          => 'checkbox',
            ],
            'decrement_quantity_after_order' => [
                'tab'           => 'lovata.ordersshopaholic::lang.tab.order_settings',
                'label'         => 'lovata.ordersshopaholic::lang.settings.decrement_quantity_after_order',
                'span'          => 'left',
                'type'          => 'checkbox',
            ],
            'create_new_user' => [
                'tab'           => 'lovata.ordersshopaholic::lang.tab.order_settings',
                'label'         => 'lovata.ordersshopaholic::lang.settings.create_new_user',
                'span'          => 'left',
                'type'          => 'checkbox',
            ],
        ]);
    }
}