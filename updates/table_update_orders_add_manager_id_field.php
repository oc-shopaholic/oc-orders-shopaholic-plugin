<?php namespace Lovata\OrdersShopaholic\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

/**
 * Class TableUpdateOrdersAddManagerIdField
 * @package Lovata\OrdersShopaholic\Updates
 */
class TableUpdateOrdersAddManagerIdField extends Migration
{
    const TABLE_NAME = 'lovata_orders_shopaholic_orders';
    /**
     * Apply migration
     */
    public function up()
    {
        if (!Schema::hasTable(self::TABLE_NAME) || Schema::hasColumn(self::TABLE_NAME, 'manager_id')) {
            return;
        }

        Schema::table(self::TABLE_NAME, function(Blueprint $obTable) {
            $obTable->integer('manager_id')->nullable();
        });
    }

    /**
     * Rollback migration
     */
    public function down()
    {
        if (!Schema::hasTable(self::TABLE_NAME) || !Schema::hasColumn(self::TABLE_NAME, 'manager_id')) {
            return;
        }

        Schema::table(self::TABLE_NAME, function(Blueprint $obTable) {
            $obTable->dropColumn(['manager_id']);
        });
    }
}
