<?php namespace Lovata\OrdersShopaholic\Widgets;

use Lang;
use October\Rain\Argon\Argon;
use Backend\Classes\ReportWidgetBase;

use Lovata\OrdersShopaholic\Models\Status;
use Lovata\OrdersShopaholic\Models\Order;

/**
 * Class OrdersByStatus
 * @package Lovata\OrdersShopaholic\Widgets
 * @author  Sergey Zakharevich, s.zakharevich@lovata.com, LOVATA Group
 */
class OrdersByStatus extends ReportWidgetBase
{
    /** @var Argon */
    protected $obDate;

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
        ];
    }

    /**
     * Init data
     */
    protected function initData()
    {
        $iDays = $this->property('days');

        if (empty($iDays)) {
            $iDays = 14;
        }

        $this->obDate = Argon::now()->subDays($iDays);

        $this->vars['arOrderListByStatus'] = [];
        $this->vars['iCountOrders']        = 0;

        $obStatusList = Status::all();

        if (empty($obStatusList)) {
            return;
        }

        foreach ($obStatusList as $obStatus) {
            $iCount = $this->countOrdersByStatus($obStatus);

            $this->vars['iCountOrders'] += $iCount;
            $this->vars['arOrderListByStatus'][] = [
                'name' => $obStatus->name,
                'count' => $iCount,
            ];
        }
    }

    /**
     * Count orders by status
     * @param Status $obStatus
     * @return int
     */
    protected function countOrdersByStatus($obStatus)
    {
        if (empty($obStatus) || !$obStatus instanceof Status || empty($this->obDate)) {
            return 0;
        }

        return Order::getByStatus($obStatus->id)
            ->whereDate('created_at', '>=', $this->obDate)
            ->get()
            ->count();
    }
}
