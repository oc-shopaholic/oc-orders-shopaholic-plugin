<?php namespace Lovata\OrdersShopaholic\Controllers;

use Backend\Classes\Controller;
use BackendMenu;
use Lovata\OrdersShopaholic\Models\Order;
use Lovata\Shopaholic\Classes\CPrice;

class Users extends Controller
{
    public $implement = [
        'Backend\Behaviors\ListController',
        'Backend\Behaviors\FormController',
        'Backend.Behaviors.RelationController',
    ];
    
    public $listConfig = 'config_list.yaml';
    public $formConfig = 'config_form.yaml';
    public $relationConfig = 'config_relation.yaml';
    
    public function __construct()
    {
        parent::__construct();
        BackendMenu::setContext('Lovata.OrdersShopaholic', 'ordersshopaholic-menu', 'users-menu-item');
    }

    public function update($recordId, $context = null)
    {
        $obOrders = Order::getByUser($recordId)->get();
        $fSum= 0;
        /** @var Order $obOrder */
        foreach ($obOrders as $obOrder) {
            $fSum += $obOrder->getTotalPriceValue();
        }
        $this->vars['iTotalCount'] = $obOrders->count();
        $this->vars['fTotalSum'] = CPrice::getPriceInFormat($fSum);

        // Call the FormController behavior update() method
        return $this->asExtension('FormController')->update($recordId, $context);
    }
}