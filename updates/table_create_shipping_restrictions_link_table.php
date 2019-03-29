<?php namespace Lovata\OrdersShopaholic\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

/**
 * Class TableCreateShippingRestrictionsLinkTable
 * @package Lovata\OrdersShopaholic\Updates
 */
class TableCreateShippingRestrictionsLinkTable extends Migration
{
    const TABLE_NAME = 'lovata_ordersshopaholic_shipping_restrictions_link';

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
            $obTable->integer('shipping_type_id')->unsigned();
            $obTable->integer('shipping_restriction_id')->unsigned();
            $obTable->primary(['shipping_type_id', 'shipping_restriction_id'], 'shipping_type_restriction');
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
