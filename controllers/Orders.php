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

        $this->addCss('/plugins/lovata/ordersshopaholic/assets/css/jjsonviewer.css');
        $this->addCss('/plugins/lovata/ordersshopaholic/assets/css/backend.css');
        $this->addJs('/plugins/lovata/ordersshopaholic/assets/js/jjsonviewer.js');
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
        /** @var \Lovata\OrdersShopaholic\Models\Order $obOrder */
        $obOrder = $this->relationObject->getParent();

        //Update price block partial
        $this->initForm($obOrder, 'update');
        $arResult['#Form-field-Order-price_block-group'] = $this->formGetWidget()->renderField('price_block', ['useContainer' => false]);
        $obMechanismList = $obOrder->order_promo_mechanism;
        if ($obMechanismList->isNotEmpty()) {
            $arResult['#Form-field-Order-discount_log_position_total_price-group'] = $this->formGetWidget()->renderField('discount_log_position_total_price', ['useContainer' => false]);
            $arResult['#Form-field-Order-discount_log_shipping_price-group'] = $this->formGetWidget()->renderField('discount_log_shipping_price', ['useContainer' => false]);
            $arResult['#Form-field-Order-discount_log_total_price-group'] = $this->formGetWidget()->renderField('discount_log_total_price', ['useContainer' => false]);
        }

        return $arResult;
    }

    /**
     * Update relation object handler
     * @return mixed
     */
    public function onRelationManageUpdate()
    {
        $arResult = parent::onRelationManageUpdate();
        if (empty($arResult) || !is_array($arResult)) {
            $arResult = [];
        }

        //Get order object
        /** @var \Lovata\OrdersShopaholic\Models\Order $obOrder */
        $obOrder = $this->relationObject->getParent();
        $sRelationName = $this->relationName;

        //Update price block partial
        $this->initForm($obOrder, 'update');
        if (in_array($sRelationName, ['active_task', 'completed_task'])) {
            $arResult = array_merge($arResult, $this->relationRefresh('active_task'), $this->relationRefresh('completed_task'));
        }

        return $arResult;
    }

    /**
     * Remove relation object handler
     * @return mixed
     */
    public function onRelationButtonDelete()
    {
        $arResult = parent::onRelationButtonDelete();
        if (empty($arResult) || !is_array($arResult)) {
            $arResult = [];
        }

        //Get order object
        /** @var \Lovata\OrdersShopaholic\Models\Order $obOrder */
        $obOrder = $this->relationObject->getParent();

        //Update price block partial
        $this->initForm($obOrder, 'update');
        $arResult['#Form-field-Order-price_block-group'] = $this->formGetWidget()->renderField('price_block', ['useContainer' => false]);
        $obMechanismList = $obOrder->order_promo_mechanism;
        if ($obMechanismList->isNotEmpty()) {
            $arResult['#Form-field-Order-discount_log_position_total_price-group'] = $this->formGetWidget()->renderField('discount_log_position_total_price', ['useContainer' => false]);
            $arResult['#Form-field-Order-discount_log_shipping_price-group'] = $this->formGetWidget()->renderField('discount_log_shipping_price', ['useContainer' => false]);
            $arResult['#Form-field-Order-discount_log_total_price-group'] = $this->formGetWidget()->renderField('discount_log_total_price', ['useContainer' => false]);
        }

        return $arResult;
    }
}
