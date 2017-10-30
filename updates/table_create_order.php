<?php namespace Lovata\OrdersShopaholic\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

/**
 * Class TableCreateOrder
 * @package Lovata\OrdersShopaholic\Updates
 */
class TableCreateOrder extends Migration
{
    public function up()
    {
        if(Schema::hasTable('lovata_orders_shopaholic_orders')) {
            return;
        }
        
        Schema::create('lovata_orders_shopaholic_orders', function(Blueprint $obTable)
        {
            $obTable->engine = 'InnoDB';
            $obTable->increments('id')->unsigned();
            $obTable->integer('user_id')->nullable()->unsigned();
            $obTable->integer('status_id')->nullable();
            $obTable->string('order_number')->nullable();
            $obTable->string('secret_key')->nullable();
            $obTable->decimal('total_price', 15, 2)->nullable();
            $obTable->decimal('shipping_price', 15, 2)->nullable();
            $obTable->integer('shipping_type_id')->nullable();
            $obTable->integer('payment_method_id')->nullable();
            $obTable->mediumText('property')->nullable();
            $obTable->timestamps();

            $obTable->index('user_id');
            $obTable->index('status_id');
            $obTable->index('order_number');
            $obTable->index('shipping_type_id');
            $obTable->index('payment_method_id');
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('lovata_orders_shopaholic_orders');
    }
}