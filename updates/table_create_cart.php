<?php namespace Lovata\OrdersShopaholic\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

/**
 * Class TableCreateCart
 * @package Lovata\OrdersShopaholic\Updates
 */
class TableCreateCart extends Migration
{
    /**
     * Apply migration
     */
    public function up()
    {
        if(Schema::hasTable('lovata_orders_shopaholic_carts')) {
            return;
        }
        
        Schema::create('lovata_orders_shopaholic_carts', function(Blueprint $obTable)
        {
            $obTable->engine = 'InnoDB';
            $obTable->increments('id')->unsigned();
            $obTable->integer('user_id')->nullable();
            $obTable->timestamps();

            $obTable->index('user_id');
        });
    }

    /**
     * Rollback migration
     */
    public function down()
    {
        Schema::dropIfExists('lovata_orders_shopaholic_carts');
    }
}