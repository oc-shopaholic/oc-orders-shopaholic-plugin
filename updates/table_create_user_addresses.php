<?php namespace Lovata\OrdersShopaholic\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

/**
 * Class TableCreateOrder
 * @package Lovata\OrdersShopaholic\Updates
 */
class TableCreateUserAddresses extends Migration
{
    const TABLE_NAME = 'lovata_orders_shopaholic_user_addresses';

    public function up()
    {
        if(Schema::hasTable(self::TABLE_NAME)) {
            return;
        }

        Schema::create(self::TABLE_NAME, function(Blueprint $obTable)
        {
            $obTable->engine = 'InnoDB';
            $obTable->increments('id')->unsigned();
            $obTable->integer('user_id')->unsigned();
            $obTable->string('type');
            $obTable->string('country')->nullable();
            $obTable->string('state')->nullable();
            $obTable->string('city')->nullable();
            $obTable->string('street')->nullable();
            $obTable->string('house')->nullable();
            $obTable->string('building')->nullable();
            $obTable->string('flat')->nullable();
            $obTable->integer('floor')->nullable();
            $obTable->string('address1')->nullable();
            $obTable->string('address2')->nullable();
            $obTable->integer('postcode')->nullable();
            $obTable->timestamps();

            $obTable->index('user_id');
            $obTable->index('type');
        });
    }

    public function down()
    {
        Schema::dropIfExists(self::TABLE_NAME);
    }
}
