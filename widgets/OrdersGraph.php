<?php namespace Lovata\OrdersShopaholic\Widgets;

use Lang;
use October\Rain\Argon\Argon;
use Backend\Classes\ReportWidgetBase;

use Lovata\OrdersShopaholic\Models\Status;
use Lovata\OrdersShopaholic\Models\Order;
use Lovata\Shopaholic\Classes\Helper\CurrencyHelper;

/**
 * Class OrdersGraph
 * @package Lovata\OrdersShopaholic\Widgets
 * @author  Sergey Zakharevich, s.zakharevich@lovata.com, LOVATA Group
 */
class OrdersGraph extends ReportWidgetBase
{
    const TYPE_COUNT_ORDERS = 'count_orders';
    const TYPE_TOTAL_PRICE  = 'total_price';

    /** @var Argon */
    protected $obDate;
    /** @var int */
    protected $iDays = 14;

    /**
     * Render method
     * @return mixed|string
     * @throws \SystemException
     */
    public function render()
    {
        $this->initData();


        return $this->makePartial('widget');
    }

    /**
     * Define properties
     * @return array
     */
    public function defineProperties()
    {
        return [
            'days' => [
                'title'   => 'lovata.ordersshopaholic::lang.field.widget_orders_by_statuses',
                'type'    => 'dropdown',
                'default' => 14,
                'options' => [
                    14 => '14'.' '.Lang::get('lovata.ordersshopaholic::lang.field.widget_days'),
                    30 => '30'.' '.Lang::get('lovata.ordersshopaholic::lang.field.widget_days'),
                    90 => '90'.' '.Lang::get('lovata.ordersshopaholic::lang.field.widget_days'),
                ],
            ],
            'completed' => [
                'title'   => 'lovata.ordersshopaholic::lang.field.widget_only_completed_orders',
                'type'    => 'checkbox',
                'default' => false,
            ],
            'type' => [
                'title'   => 'lovata.toolbox::lang.field.type',
                'type'    => 'dropdown',
                'default' => self::TYPE_COUNT_ORDERS,
                'options' => [
                    self::TYPE_COUNT_ORDERS => Lang::get('lovata.ordersshopaholic::lang.field.count_orders'),
                    self::TYPE_TOTAL_PRICE  => Lang::get('lovata.ordersshopaholic::lang.field.total_price'),
                ],
            ],
        ];
    }

    /**
     * Init data
     */
    protected function initData()
    {
        $iDays = $this->property('days');

        if (!empty($iDays)) {
            $this->iDays = $iDays;
        }

        $this->obDate = Argon::now()->subDays($iDays);

        $sGraph = $this->getGraph();

        if ($this->property('type') == self::TYPE_COUNT_ORDERS) {
            $sType = Lang::get('lovata.ordersshopaholic::lang.field.count_orders');
        } else {
            $sType = Lang::get('lovata.ordersshopaholic::lang.field.total_price');
        }

        $this->vars['sName']  = implode('_', $this->getProperties());
        $this->vars['sGraph'] = substr($sGraph, 0, -1);
        $this->vars['sTitle'] = Lang::get('lovata.toolbox::lang.field.type').': '.$sType;
    }

    /**
     * Count orders by status
     * @return int
     */
    protected function getGraph()
    {
        $sResult = '';

        if (empty($this->obDate) || $this->iDays === null) {
            return $sResult;
        }

        $iMaxCount = $this->iDays + 1;
        for ($i = 1; $i < $iMaxCount; $i++) {
            $obDate = $this->obDate->addDay();

            if ($this->property('type') == self::TYPE_COUNT_ORDERS) {
                $fCount = $this->getCountOrders($obDate);
            } else {
                $fCount = $this->getTotalPrice($obDate);
            }

            $sResult .= '['.$obDate->getTimestamp().'000,'.$fCount.'],';
        }

        return $sResult;
    }

    /**
     * Get count order
     * @param Argon $obDate
     * @return int
     */
    protected function getCountOrders($obDate)
    {
        if (empty($obDate) || !$obDate instanceof Argon) {
            return 0;
        }

        $obQuery = Order::whereDate('created_at', '>=', $obDate->toDateString())
            ->whereDate('created_at', '<=', $obDate->toDateString());

        if ($this->property('completed')) {
            $obQuery = $this->getQueryByStatus($obQuery);
        }

        return $obQuery->orderBy('created_at', 'asc')
            ->get()
            ->count();
    }

    /**
     * Get total price
     * @param Argon $obDate
     * @return int
     */
    protected function getTotalPrice($obDate)
    {
        $fResult = 0;

        if (empty($obDate) || !$obDate instanceof Argon) {
            return $fResult;
        }

        $obQuery = Order::whereDate('created_at', '>=', $obDate->toDateString())
            ->whereDate('created_at', '<=', $obDate->toDateString());

        if ($this->property('completed')) {
            $obQuery = $this->getQueryByStatus($obQuery);
        }

        $obOrderList = $obQuery->orderBy('created_at', 'asc')->get();

        if ($obOrderList->isEmpty()) {
            return $fResult;
        }

        foreach ($obOrderList as $obOrder) {
            $obCurrency = $obOrder->currency;
            if (empty($obCurrency)) {
                continue;
            }

            $fResult = $fResult + CurrencyHelper::instance()->convertTo($obOrder->total_price_value, $obCurrency->code);
        }

        return $fResult;
    }

    /**
     * Get orders by completed status
     * @param Order $obQuery
     * @return mixed
     */
    protected function getQueryByStatus($obQuery)
    {
        $obStatus = Status::getByCode(Status::STATUS_COMPETE)->first();

        if (empty($obStatus)) {
            return $obQuery;
        }

        return $obQuery->getByStatus($obStatus->id);
    }
}
