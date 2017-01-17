<?php namespace Lovata\OrdersShopaholic\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateLovataOrdersshopaholicOfferOrder extends Migration
{
    public function up()
    {
        Schema::create('lovata_ordersshopaholic_offer_order', function($table)
        {
            $table->engine = 'InnoDB';
            $table->integer('offer_id')->unsigned();
            $table->integer('order_id')->unsigned();
            $table->decimal('price', 15, 2)->nullable();
            $table->decimal('old_price', 15, 2)->nullable();
            $table->integer('quantity')->unsigned()->nullable();
            $table->string('code')->nullable();
            $table->primary(['offer_id','order_id']);
            $table->timestamps();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('lovata_ordersshopaholic_offer_order');
    }
}