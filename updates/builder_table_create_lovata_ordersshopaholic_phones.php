<?php namespace Lovata\OrdersShopaholic\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateLovataOrdersshopaholicPhones extends Migration
{
    public function up()
    {
        Schema::create('lovata_ordersshopaholic_phones', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            $table->integer('user_id')->unsigned();
            $table->string('formatted_phone');
            $table->string('phone');
            $table->timestamps();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('lovata_ordersshopaholic_phones');
    }
}