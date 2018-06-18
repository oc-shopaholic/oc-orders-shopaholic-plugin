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
        'Backend.Behaviors.ListController',
        'Backend.Behaviors.FormController',
        'Backend.Behaviors.RelationController',
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

    /**
     * Create relation object handler
     * @return mixed
     */
    public function onRelationManageCreate()
    {
        $arResult = parent::onRelationManageCreate();
        if (empty($arResult) || !is_array($arResult)) {
            $arResult = [];
        }

        //Get order object
        $obOrder = $this->relationObject->getParent();

        //Update price block partial
        $this->initForm($obOrder, 'update');
        $arResult['#Form-field-Order-price_block-group'] = $this->formGetWidget()->renderField('price_block', ['useContainer' => false]);

        return $arResult;
    }
}
