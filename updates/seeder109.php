<?php namespace Lovata\OrdersShopaholic\Updates;

use Lang;
use Lovata\OrdersShopaholic\Models\AddressType;
use Lovata\OrdersShopaholic\Models\Status;
use Seeder;

class Seeder109 extends Seeder
{
    public function run()
    {
        Status::insert([
            ['code' => Status::STATUS_NEW, 'name' => Lang::get('lovata.ordersshopaholic::lang.fields.'.Status::STATUS_NEW),],
            ['code' => Status::STATUS_IN_PROGRESS, 'name' => Lang::get('lovata.ordersshopaholic::lang.fields.'.Status::STATUS_IN_PROGRESS)],
            ['code' => Status::STATUS_COMPETE, 'name' => Lang::get('lovata.ordersshopaholic::lang.fields.'.Status::STATUS_COMPETE)],
            ['code' => Status::STATUS_CANCELED, 'name' => Lang::get('lovata.ordersshopaholic::lang.fields.'.Status::STATUS_CANCELED)],
        ]);

        $obStatuses = Status::all();
        if(!$obStatuses->isEmpty()){
            foreach ($obStatuses as $obStatus) {
                $obStatus->sort_order = $obStatus->id;
                $obStatus->save();
            }
        }
    }
}