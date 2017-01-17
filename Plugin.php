<?php namespace Lovata\OrdersShopaholic;

use Backend\Widgets\Form;
use Event;
use Lang;
use Lovata\OrdersShopaholic\Classes\CartStore;
use Lovata\OrdersShopaholic\Models\Address;
use System\Classes\PluginBase;
use Lovata\Shopaholic\Models\Settings;

/**
 * Class Plugin
 * @package Lovata\OrdersShopaholic
 * @author Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class Plugin extends PluginBase
{
    const NAME = 'ordersshopaholic';
    const FIND_USER_BY_PHONE = 'phone';
    const FIND_USER_BY_EMAIL = 'email';
    
    
    public function registerComponents()
    {
        return [
            'Lovata\OrdersShopaholic\Components\Cart'               => 'Cart',
            'Lovata\OrdersShopaholic\Components\Ordering'           => 'Ordering',
        ];
    }

    public function register()
    {
        $this->registerConsoleCommand('ordersshopaholic.removeOldCarts', 'Lovata\OrdersShopaholic\Console\RemoveOldCarts');
    }
    
    public function boot()
    {
        Event::listen('shopaholic.order.created', function($obOrder) {
            //TODO: Отрефакторить
            Address::createUserAddress($obOrder);
        });

        Event::listen('shopaholic.order.updated', function($obOrder) {
            //TODO: Отрефакторить
            Address::createUserAddress($obOrder);
        });
        
        // Extend "Shopaholic" settings form, add cart settings
        Event::listen('backend.form.extendFields', function($widget) {

            /**@var Form $widget */
            // Only for the Settings controller
            if (!$widget->getController() instanceof \System\Controllers\Settings) {
                return;
            }

            // Only for the Settings model
            if (!$widget->model instanceof Settings) {
                return;
            }

            // Add an extra birthday field
            $widget->addTabFields([
                'cart_cookie_lifetime' => [
                    'tab'           => 'lovata.ordersshopaholic::lang.tab.order_settings',
                    'label'         => 'lovata.ordersshopaholic::lang.settings.cart_cookie_lifetime',
                    'span'          => 'left',
                    'type'          => 'number',
                    'default'       => CartStore::$iCookieLifeTime,
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
                'user_key_field' => [
                    'tab'           => 'lovata.ordersshopaholic::lang.tab.order_settings',
                    'label'         => 'lovata.ordersshopaholic::lang.settings.user_key_field',
                    'span'          => 'left',
                    'type'          => 'dropdown',
                    'options'       => [
                        self::FIND_USER_BY_PHONE => Lang::get('lovata.toolbox::lang.field.phone'),
                        self::FIND_USER_BY_EMAIL => Lang::get('lovata.toolbox::lang.field.email'),
                    ],
                ],
            ]);
        });
    }
}
