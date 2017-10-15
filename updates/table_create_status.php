<?php namespace Lovata\OrdersShopaholic\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

/**
 * Class TableCreateStatus
 * @package Lovata\OrdersShopaholic\Updates
 */
class TableCreateStatus extends Migration
{
    public function up()
    {
        if(Schema::hasTable('lovata_orders_shopaholic_statuses')) {
            return;
        }
        
        Schema::create('lovata_orders_shopaholic_statuses', function(Blueprint $obTable)
        {
            $obTable->engine = 'InnoDB';
            $obTable->increments('id')->unsigned();
            $obTable->string('name');
            $obTable->string('code');
            $obTable->integer('sort_order')->nullable();
            $obTable->timestamps();

            $obTable->index('code');
            $obTable->index('sort_order');
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('lovata_orders_shopaholic_statuses');
    }
}