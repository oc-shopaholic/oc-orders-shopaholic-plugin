<?php namespace Lovata\OrdersShopaholic\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

/**
 * Class TableCreatePromoMechanism
 * @package Lovata\OrdersShopaholic\Updates
 */
class TableCreatePromoMechanism extends Migration
{
    const TABLE_NAME = 'lovata_orders_shopaholic_promo_mechanism';

    public function up()
    {
        if(Schema::hasTable(self::TABLE_NAME)) {
            return;
        }
        
        Schema::create(self::TABLE_NAME, function(Blueprint $obTable)
        {
            $obTable->engine = 'InnoDB';
            $obTable->increments('id')->unsigned();
            $obTable->string('name');
            $obTable->string('type');
            $obTable->integer('priority')->unsigned();
            $obTable->float('discount_value')->unsigned();
            $obTable->string('discount_type');
            $obTable->boolean('final_discount')->default(0);
            $obTable->text('property')->nullable();
            $obTable->timestamps();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists(self::TABLE_NAME);
    }
}