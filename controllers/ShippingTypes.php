<?php namespace Lovata\OrdersShopaholic\Controllers;

use Event;
use BackendMenu;
use Backend\Classes\Controller;
use System\Classes\SettingsManager;

/**
 * Class ShippingTypes
 * @package Lovata\OrdersShopaholic\Controllers
 * @author Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class ShippingTypes extends Controller
{
    public $implement = [
        'Backend.Behaviors.ListController',
        'Backend.Behaviors.FormController',
        'Backend.Behaviors.ReorderController',
        'Backend.Behaviors.RelationController',
    ];

    public $listConfig = 'config_list.yaml';
    public $formConfig = 'config_form.yaml';
    public $reorderConfig = 'config_reorder.yaml';
    public $relationConfig = 'config_relation.yaml';

    /**
     * ShippingTypes constructor.
     */
    public function __construct()
    {
        parent::__construct();
        BackendMenu::setContext('October.System', 'system', 'settings');
        SettingsManager::setContext('Lovata.OrdersShopaholic', 'orders-shopaholic-menu-shipping-types');
    }

    /**
     * Ajax handler onReorder event
     */
    public function onReorder()
    {
        $obResult =  parent::onReorder();
        Event::fire('shopaholic.shipping_type.update.sorting');

        return $obResult;
    }
}