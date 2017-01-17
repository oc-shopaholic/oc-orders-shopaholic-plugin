<?php namespace Lovata\OrdersShopaholic\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateLovataOrdersshopaholicShippingTypes extends Migration
{
    public function up()
    {
        Schema::create('lovata_ordersshopaholic_shipping_types', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            $table->string('code');
            $table->string('name');
            $table->integer('sort_order')->nullable();
            $table->string('description')->nullable();
            $table->timestamps();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('lovata_ordersshopaholic_shipping_types');
    }
}