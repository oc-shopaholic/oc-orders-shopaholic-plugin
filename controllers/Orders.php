<?php namespace Lovata\OrdersShopaholic\Controllers;

use October\Rain\Argon\Argon;
use BackendMenu;
use Backend\Classes\Controller;

use Lovata\OrdersShopaholic\Models\Order;
use Lovata\OrdersShopaholic\Models\Status;
use Lovata\Shopaholic\Models\Currency;

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
        $this->addJs('/plugins/lovata/ordersshopaholic/assets/js/analytics.js');
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

    /**
     * Render analytics block for orders
     * @return array
     * @throws \SystemException
     */
    public function onAnalytics()
    {
        $obDate = Argon::now()->subDays(30);

        $obStatusCompleted = Status::getByCode(Status::STATUS_COMPETE)->first();
        $obCompletedOrderList = null;

        $iCountCompletedOrders = 0;
        $iCountAllOrders = Order::whereDate('created_at', '>=', $obDate)->count();

        if (!empty($obStatusCompleted)) {
            $obCompletedOrderList = Order::getByStatus($obStatusCompleted->id)
                ->whereDate('created_at', '>=', $obDate)
                ->get();

            $iCountCompletedOrders = $obCompletedOrderList->count();
        }

        $this->vars['iCountAllOrders'] = $iCountAllOrders;
        $this->vars['iCountCompletedOrders'] = $iCountCompletedOrders;
        $this->vars['arCompletedTotalPrice'] = $this->getCompletedTotalPriceList($obCompletedOrderList);

        return [
            '^.analytics-ajax' => $this->makePartial('analytics'),
        ];
    }

    /**
     * Get total price for the last period
     * @param $obOrderList
     * @return array
     */
    protected function getCompletedTotalPriceList($obOrderList)
    {
        $arResult = [];
        if (empty($obOrderList) || !$obOrderList instanceof \October\Rain\Database\Collection) {
            return $arResult;
        }

        $obCurrencyList = Currency::all();
        if ($obOrderList->isEmpty()) {
            return $arResult;
        }

        foreach ($obCurrencyList as $obCurrency) {
            $fPrice = $this->getTotalPriceByCurrency($obOrderList, $obCurrency->id);
            if ($fPrice == 0) {
                continue;
            }

            $arResult[] = [
                'price'    => $fPrice,
                'currency' => $obCurrency->symbol,
            ];
        }

        return $arResult;
    }

    /**
     * Get total price by currency
     * @param \October\Rain\Database\Collection $obOrderList
     * @param int                               $iCurrencyID
     * @return float|int
     */
    protected function getTotalPriceByCurrency($obOrderList, $iCurrencyID)
    {
        $fResult = 0;
        if (empty($obOrderList) || !$obOrderList instanceof \October\Rain\Database\Collection || empty($iCurrencyID)) {
            return $fResult;
        }

        /** @var \Lovata\OrdersShopaholic\Models\Order $obOrder */
        foreach ($obOrderList as $obOrder) {
            if ($obOrder->currency_id != $iCurrencyID) {
                continue;
            }

            $fResult += $obOrder->total_price_value;
        }

        return $fResult;
    }
}
