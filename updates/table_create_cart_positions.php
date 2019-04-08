<?php namespace Lovata\OrdersShopaholic\Updates;

use DB;
use Schema;

use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

/**
 * Class TableCreateCartPositions
 * @package Lovata\OrdersShopaholic\Updates
 */
class TableCreateCartPositions extends Migration
{
    public function up()
    {
        $this->createNewTable();
        $this->replaceData();
        $this->removeOldTable();
    }

    public function down()
    {
        $this->createOldTable();
        $this->restoreData();
        $this->removeNewTable();
    }

    protected function createNewTable()
    {
        if (Schema::hasTable('lovata_orders_shopaholic_cart_positions')) {
            return;
        }

        Schema::create('lovata_orders_shopaholic_cart_positions', function (Blueprint $obTable) {
            $obTable->engine = 'InnoDB';
            $obTable->increments('id')->unsigned();
            $obTable->integer('cart_id')->unsigned()->default(0);
            $obTable->integer('item_id')->unsigned()->default(0);
            $obTable->string('item_type')->default('Lovata\\Shopaholic\\Models\\Offer');
            $obTable->integer('quantity')->unsigned()->default(0);
            $obTable->text('property')->nullable();
            $obTable->timestamps();
            $obTable->softDeletes();

            $obTable->index('cart_id');
        });
    }

    protected function removeNewTable()
    {
        Schema::dropIfExists('lovata_orders_shopaholic_cart_positions');
    }

    protected function createOldTable()
    {
        if (Schema::hasTable('lovata_orders_shopaholic_cart_element')) {
            return;
        }

        Schema::create('lovata_orders_shopaholic_cart_element', function (Blueprint $obTable) {
            $obTable->engine = 'InnoDB';
            $obTable->increments('id')->unsigned();
            $obTable->integer('cart_id')->unsigned()->default(0);
            $obTable->integer('offer_id')->unsigned()->default(0);
            $obTable->integer('quantity')->unsigned()->default(0);
            $obTable->timestamps();

            $obTable->index('cart_id');
        });
    }

    protected function removeOldTable()
    {
        Schema::dropIfExists('lovata_orders_shopaholic_cart_element');
    }


    protected function replaceData()
    {
        if (!Schema::hasTable('lovata_orders_shopaholic_cart_positions') || !Schema::hasTable('lovata_orders_shopaholic_cart_element')) {
            return;
        }

        //Get offer order list
        $obElementList = DB::table('lovata_orders_shopaholic_cart_element')->get();
        if ($obElementList->isEmpty()) {
            return;
        }

        $arInsetRowList = [];
        foreach ($obElementList as $iKey => $obElement) {

            $arInsetRowList[] = [
                'id'        => $iKey + 1,
                'cart_id'   => $obElement->cart_id,
                'item_id'   => $obElement->offer_id,
                'quantity'  => $obElement->quantity,
                'item_type' => 'Lovata\\Shopaholic\\Models\\Offer',
            ];
        }

        DB::table('lovata_orders_shopaholic_cart_positions')->insert($arInsetRowList);
    }

    protected function restoreData()
    {
        if (!Schema::hasTable('lovata_orders_shopaholic_cart_positions') || !Schema::hasTable('lovata_orders_shopaholic_cart_element')) {
            return;
        }

        //Get offer order list
        $obElementList = DB::table('lovata_orders_shopaholic_cart_positions')->get();
        if ($obElementList->isEmpty()) {
            return;
        }

        $arInsetRowList = [];
        foreach ($obElementList as $iKey => $obElement) {

            $arInsetRowList[] = [
                'id'       => $iKey + 1,
                'cart_id'  => $obElement->cart_id,
                'offer_id' => $obElement->item_id,
                'quantity' => $obElement->quantity,
            ];

        }

        DB::table('lovata_orders_shopaholic_cart_element')->insert($arInsetRowList);
    }
}
