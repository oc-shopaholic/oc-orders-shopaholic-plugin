<?php namespace Lovata\OrdersShopaholic\Updates;

use Lang;
use Seeder;
use Lovata\OrdersShopaholic\Models\Status;

/**
 * Class SeederDefaultStatus
 * @package Lovata\OrdersShopaholic\Updates
 */
class SeederDefaultStatus extends Seeder
{
    public function run()
    {
        $arStatusList = [
            [
                'code' => Status::STATUS_NEW,
                'name' => Lang::get('lovata.ordersshopaholic::lang.field.' . Status::STATUS_NEW),
                'sort_order' => 1
            ], [
                'code' => Status::STATUS_IN_PROGRESS,
                'name' => Lang::get('lovata.ordersshopaholic::lang.field.' . Status::STATUS_IN_PROGRESS),
                'sort_order' => 2
            ], [
                'code' => Status::STATUS_COMPETE,
                'name' => Lang::get('lovata.ordersshopaholic::lang.field.' . Status::STATUS_COMPETE),
                'sort_order' => 3
            ], [
                'code' => Status::STATUS_CANCELED,
                'name' => Lang::get('lovata.ordersshopaholic::lang.field.' . Status::STATUS_CANCELED),
                'sort_order' => 4
            ],
        ];

        foreach ($arStatusList as $arStatusData) {
            Status::create($arStatusData);
        }
    }
}