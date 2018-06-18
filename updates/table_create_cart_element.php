<?php namespace Lovata\OrdersShopaholic\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

/**
 * Class TableCreateCartPosition
 * @package Lovata\OrdersShopaholic\Updates
 */
class TableCreateCartPosition extends Migration
{
    public function up()
    {
        if(Schema::hasTable('lovata_orders_shopaholic_cart_element')) {
            return;
        }
        
        Schema::create('lovata_orders_shopaholic_cart_element', function(Blueprint $obTable)
        {
            $obTable->engine = 'InnoDB';
            $obTable->increments('id')->unsigned();
            $obTable->integer('cart_id')->unsigned()->default(0);
            $obTable->integer('offer_id')->unsigned()->default(0);
            $obTable->integer('quantity')->unsigned()->default(0);
            $obTable->timestamps();

            $obTable->index('cart_id');
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('lovata_orders_shopaholic_cart_element');
    }
}