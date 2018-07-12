<?php namespace Lovata\OrdersShopaholic\Updates;

use DB;
use Schema;

use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

/**
 * Class TableCreateOrderPositions
 * @package Lovata\OrdersShopaholic\Updates
 */
class TableCreateOrderPositions extends Migration
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
        if (Schema::hasTable('lovata_orders_shopaholic_order_positions')) {
            return;
        }

        Schema::create('lovata_orders_shopaholic_order_positions', function (Blueprint $obTable) {
            $obTable->engine = 'InnoDB';
            $obTable->increments('id')->unsigned();
            $obTable->integer('order_id')->unsigned();
            $obTable->integer('item_id')->unsigned();
            $obTable->string('item_type');
            $obTable->decimal('price', 15, 2)->nullable();
            $obTable->decimal('old_price', 15, 2)->nullable();
            $obTable->integer('quantity')->unsigned()->nullable();
            $obTable->string('code')->nullable();
            $obTable->text('property')->nullable();
            $obTable->timestamps();

            $obTable->index('item_id');
            $obTable->index('item_type');
        });
    }

    protected function createOldTable()
    {
        if (Schema::hasTable('lovata_orders_shopaholic_offer_order')) {
            return;
        }

        Schema::create('lovata_orders_shopaholic_offer_order', function (Blueprint $obTable) {
            $obTable->engine = 'InnoDB';
            $obTable->integer('offer_id')->unsigned();
            $obTable->integer('order_id')->unsigned();
            $obTable->decimal('price', 15, 2)->nullable();
            $obTable->decimal('old_price', 15, 2)->nullable();
            $obTable->integer('quantity')->unsigned()->nullable();
            $obTable->string('code')->nullable();
            $obTable->primary(['offer_id', 'order_id'], 'offer_id_order_id');
            $obTable->timestamps();
        });
    }

    protected function replaceData()
    {
        if (!Schema::hasTable('lovata_orders_shopaholic_offer_order') || !Schema::hasTable('lovata_orders_shopaholic_order_positions')) {
            return;
        }

        //Get offer order list
        $obOfferOrderList = DB::table('lovata_orders_shopaholic_offer_order')->get();
        if ($obOfferOrderList->isEmpty()) {
            return;
        }

        $arInsetRowList = [];
        foreach ($obOfferOrderList as $iKey => $obOfferOrder) {

            $arInsetRowList[] = [
                'id'        => $iKey + 1,
                'order_id'  => $obOfferOrder->order_id,
                'item_id'   => $obOfferOrder->offer_id,
                'price'     => $obOfferOrder->price,
                'old_price' => $obOfferOrder->old_price,
                'quantity'  => $obOfferOrder->quantity,
                'code'      => $obOfferOrder->code,
                'item_type' => 'Lovata\\Shopaholic\\Models\\Offer',
            ];
        }

        DB::table('lovata_orders_shopaholic_order_positions')->insert($arInsetRowList);
    }

    protected function restoreData()
    {
        if (!Schema::hasTable('lovata_orders_shopaholic_offer_order') || !Schema::hasTable('lovata_orders_shopaholic_order_positions')) {
            return;
        }

        //Get offer order list
        $obOfferOrderList = DB::table('lovata_orders_shopaholic_order_positions')->get();
        if ($obOfferOrderList->isEmpty()) {
            return;
        }

        $arInsetRowList = [];
        foreach ($obOfferOrderList as $obOfferOrder) {

            $arInsetRowList[] = [
                'order_id'  => $obOfferOrder->order_id,
                'offer_id'  => $obOfferOrder->item_id,
                'price'     => $obOfferOrder->price,
                'old_price' => $obOfferOrder->old_price,
                'quantity'  => $obOfferOrder->quantity,
                'code'      => $obOfferOrder->code,
            ];

        }

        DB::table('lovata_orders_shopaholic_offer_order')->insert($arInsetRowList);
    }

    protected function removeOldTable()
    {
        Schema::dropIfExists('lovata_orders_shopaholic_offer_order');
    }

    protected function removeNewTable()
    {
        Schema::dropIfExists('lovata_orders_shopaholic_order_positions');
    }
}
