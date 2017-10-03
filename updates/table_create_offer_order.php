<?php namespace Lovata\OrdersShopaholic\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

/**
 * Class TableCreateOfferOrder
 * @package Lovata\OrdersShopaholic\Updates
 */
class TableCreateOfferOrder extends Migration
{
    public function up()
    {
        if(Schema::hasTable('lovata_orders_shopaholic_offer_order')) {
            return;
        }
        
        Schema::create('lovata_orders_shopaholic_offer_order', function(Blueprint $obTable)
        {
            $obTable->engine = 'InnoDB';
            $obTable->integer('offer_id')->unsigned();
            $obTable->integer('order_id')->unsigned();
            $obTable->decimal('price', 15, 2)->nullable();
            $obTable->decimal('old_price', 15, 2)->nullable();
            $obTable->integer('quantity')->unsigned()->nullable();
            $obTable->string('code')->nullable();
            $obTable->primary(['offer_id','order_id'], 'offer_id_order_id');
            $obTable->timestamps();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('lovata_orders_shopaholic_offer_order');
    }
}