<?php namespace Lovata\OrdersShopaholic\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateLovataOrdersshopaholicCarts extends Migration
{
    public function up()
    {
        Schema::create('lovata_ordersshopaholic_carts', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            $table->integer('user_id')->nullable();
            $table->timestamps();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('lovata_ordersshopaholic_carts');
    }
}