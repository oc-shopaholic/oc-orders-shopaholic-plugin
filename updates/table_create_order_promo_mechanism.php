<?php namespace Lovata\OrdersShopaholic\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

/**
 * Class TableCreateOrderPromoMechanism
 * @package Lovata\OrdersShopaholic\Updates
 */
class TableCreateOrderPromoMechanism extends Migration
{
    const TABLE_NAME = 'lovata_orders_shopaholic_order_promo_mechanism';

    public function up()
    {
        if(Schema::hasTable(self::TABLE_NAME)) {
            return;
        }
        
        Schema::create(self::TABLE_NAME, function(Blueprint $obTable)
        {
            $obTable->engine = 'InnoDB';
            $obTable->increments('id')->unsigned();
            $obTable->integer('order_id')->unsigned();
            $obTable->integer('mechanism_id')->unsigned();
            $obTable->string('name');
            $obTable->string('type');
            $obTable->integer('priority')->unsigned();
            $obTable->float('discount_value')->unsigned();
            $obTable->string('discount_type');
            $obTable->boolean('final_discount')->default(0);
            $obTable->text('property')->nullable();
            $obTable->integer('element_id')->unsigned()->nullable();
            $obTable->string('element_type')->nullable();
            $obTable->mediumText('element_data')->nullable();
            $obTable->timestamps();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists(self::TABLE_NAME);
    }
}
