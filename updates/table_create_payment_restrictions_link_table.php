<?php namespace Lovata\OrdersShopaholic\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

/**
 * Class TableCreatePaymentRestrictionsLinkTable
 * @package Lovata\OrdersShopaholic\Updates
 */
class TableCreatePaymentRestrictionsLinkTable extends Migration
{
    const TABLE_NAME = 'lovata_ordersshopaholic_payment_restrictions_link';

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
            $obTable->integer('payment_method_id')->unsigned();
            $obTable->integer('payment_restriction_id')->unsigned();
            $obTable->primary(['payment_method_id', 'payment_restriction_id'], 'payment_method_restriction');
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
