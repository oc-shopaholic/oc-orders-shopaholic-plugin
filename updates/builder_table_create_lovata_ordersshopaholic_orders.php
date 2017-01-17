<?php namespace Lovata\OrdersShopaholic\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateLovataOrdersshopaholicOrders extends Migration
{
    public function up()
    {
        Schema::create('lovata_ordersshopaholic_orders', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            $table->integer('user_id')->nullable()->unsigned();
            $table->integer('status_id')->nullable()->unsigned();
            $table->string('name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('email')->nullable();
            $table->string('order_number')->nullable();
            $table->decimal('total_price', 15, 2)->nullable();
            $table->decimal('shipping_price', 15, 2)->nullable();
            $table->string('billing_country')->nullable();
            $table->string('billing_state')->nullable();
            $table->string('billing_city')->nullable();
            $table->string('billing_street')->nullable();
            $table->string('billing_zip')->nullable();
            $table->string('billing_text_address')->nullable();
            $table->string('shipping_country')->nullable();
            $table->string('shipping_state')->nullable();
            $table->string('shipping_city')->nullable();
            $table->string('shipping_street')->nullable();
            $table->string('shipping_zip')->nullable();
            $table->string('shipping_text_address')->nullable();
            $table->text('user_comment')->nullable();
            $table->text('order_comment')->nullable();
            $table->integer('payment_method_id')->nullable();
            $table->integer('shipping_type_id')->nullable();
            $table->integer('manager_id')->nullable();
            $table->timestamps();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('lovata_ordersshopaholic_orders');
    }
}