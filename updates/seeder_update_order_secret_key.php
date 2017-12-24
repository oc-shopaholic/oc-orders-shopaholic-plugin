<?php namespace Lovata\OrdersShopaholic\Updates;

use Lovata\OrdersShopaholic\Models\Order;
use Seeder;

/**
 * Class SeederUpdateOrderSecretKey
 * @package Lovata\OrdersShopaholic\Updates
 */
class SeederUpdateOrderSecretKey extends Seeder
{
    public function run()
    {
        //Get order list
        $obOrderList = Order::get();
        if($obOrderList->isEmpty()) {
            return;
        }

        //Process order list
        /** @var Order $obOrder */
        foreach ($obOrderList as $obOrder) {

            $obOrder->secret_key = $obOrder->generateSecretKey();
            $obOrder->save();
        }
    }
}