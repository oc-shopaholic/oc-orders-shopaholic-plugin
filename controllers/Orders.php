<?php namespace Lovata\OrdersShopaholic\Controllers;

use BackendMenu;
use Backend\Classes\Controller;

/**
 * Class Orders
 * @package Lovata\OrdersShopaholic\Controllers
 * @author Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class Orders extends Controller
{
    public $implement = [
        'Backend\Behaviors\ListController',
        'Backend\Behaviors\FormController',
        'Backend\Behaviors\RelationController',
    ];
    
    public $listConfig = 'config_list.yaml';
    public $formConfig = 'config_form.yaml';
    public $relationConfig = 'config_relation.yaml';

    /**
     * Orders constructor.
     */
    public function __construct()
    {
        parent::__construct();
        BackendMenu::setContext('Lovata.OrdersShopaholic', 'orders-shopaholic-menu', 'orders-shopaholic-menu-orders');
    }
}