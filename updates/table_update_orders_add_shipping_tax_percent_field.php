<?php namespace Lovata\OrdersShopaholic\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

/**
 * Class TableUpdateOrdersAddShippingTaxPercentField
 * @package Lovata\OrdersShopaholic\Updates
 */
class TableUpdateOrdersAddShippingTaxPercentField extends Migration
{
    const TABLE_NAME = 'lovata_orders_shopaholic_orders';
    /**
     * Apply migration
     */
    public function up()
    {
        if (!Schema::hasTable(self::TABLE_NAME) || Schema::hasColumn(self::TABLE_NAME, 'shipping_tax_percent')) {
            return;
        }

        Schema::table(self::TABLE_NAME, function(Blueprint $obTable) {
            $obTable->decimal('shipping_tax_percent')->nullable();
        });
    }

    /**
     * Rollback migration
     */
    public function down()
    {
        if (!Schema::hasTable(self::TABLE_NAME) || !Schema::hasColumn(self::TABLE_NAME, 'shipping_tax_percent')) {
            return;
        }

        Schema::table(self::TABLE_NAME, function(Blueprint $obTable) {
            $obTable->dropColumn(['shipping_tax_percent']);
        });
    }
}
