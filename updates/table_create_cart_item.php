<?php namespace Lovata\OrdersShopaholic\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

/**
 * Class TableCreateCartItem
 * @package Lovata\OrdersShopaholic\Updates
 */
class TableCreateCartItem extends Migration
{
    public function up()
    {
        if(Schema::hasTable('lovata_orders_shopaholic_cart_item')) {
            return;
        }
        
        Schema::create('lovata_orders_shopaholic_cart_item', function(Blueprint $obTable)
        {
            $obTable->engine = 'InnoDB';
            $obTable->increments('id')->unsigned();
            $obTable->integer('cart_id')->unsigned();
            $obTable->integer('offer_id')->unsigned();
            $obTable->integer('quantity')->unsigned();
            $obTable->timestamps();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('lovata_orders_shopaholic_cart_item');
    }
}