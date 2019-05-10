<?php namespace Lovata\OrdersShopaholic\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

/**
 * Class TableCreatePaymentRestrictionsTable
 * @package Lovata\OrdersShopaholic\Updates
 */
class TableCreatePaymentRestrictionsTable extends Migration
{
    const TABLE_NAME = 'lovata_ordersshopaholic_payment_restrictions';

    /**
     * Apply migration
     */
    public function up()
    {
        if(Schema::hasTable(self::TABLE_NAME)) {
            return;
        }
        
        Schema::create(self::TABLE_NAME, function(Blueprint $obTable) {
            $obTable->engine = 'InnoDB';
            $obTable->increments('id');
            $obTable->boolean('active')->default(0);
            $obTable->string('name');
            $obTable->string('code');
            $obTable->string('restriction')->nullable();
            $obTable->text('description')->nullable();
            $obTable->text('property')->nullable();
            $obTable->timestamps();

            $obTable->index('code');
            $obTable->index('restriction');
        });
    }

    /**
     * Rollback migration
     */
    public function down()
    {
        Schema::dropIfExists(self::TABLE_NAME);
    }
}
