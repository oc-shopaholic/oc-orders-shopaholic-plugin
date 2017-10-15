<?php namespace Lovata\OrdersShopaholic\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

/**
 * Class TableCreatePaymentMethod
 * @package Lovata\OrdersShopaholic\Updates
 */
class TableCreatePaymentMethod extends Migration
{
    public function up()
    {
        if(Schema::hasTable('lovata_orders_shopaholic_payment_methods')) {
            return;
        }
        
        Schema::create('lovata_orders_shopaholic_payment_methods', function(Blueprint $obTable)
        {
            $obTable->engine = 'InnoDB';
            $obTable->increments('id')->unsigned();
            $obTable->boolean('active')->default(0);
            $obTable->string('name');
            $obTable->string('code');
            $obTable->integer('sort_order')->nullable();
            $obTable->text('preview_text')->nullable();
            $obTable->timestamps();

            $obTable->index('code');
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('lovata_orders_shopaholic_payment_methods');
    }
}