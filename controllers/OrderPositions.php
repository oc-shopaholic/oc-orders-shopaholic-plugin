<?php namespace Lovata\OrdersShopaholic\Controllers;

use BackendMenu;
use Backend\Classes\Controller;

/**
 * Class OrderPositions
 * @package Lovata\OrdersShopaholic\Controllers
 * @author Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class OrderPositions extends Controller
{
    public $implement = [
        'Backend.Behaviors.FormController',
    ];
    
    public $formConfig = 'config_form.yaml';

    /**
     * OrderPositions constructor.
     */
    public function __construct()
    {
        parent::__construct();
        BackendMenu::setContext('Lovata.OrdersShopaholic', 'orders-shopaholic-menu', 'orders-shopaholic-menu-orders');
    }
}