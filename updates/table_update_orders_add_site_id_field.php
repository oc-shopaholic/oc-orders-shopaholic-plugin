<?php namespace Lovata\OrdersShopaholic\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

/**
 * @package Lovata\OrdersShopaholic\Updates
 */
class TableUpdateOrdersAddSiteIDField extends Migration
{
    const TABLE_NAME = 'lovata_orders_shopaholic_orders';
    const COLUMN_LIST = [
        'site_id',
    ];

    /**
     * Apply migration
     */
    public function up()
    {
        if (!Schema::hasTable(self::TABLE_NAME)) {
            return;
        }

        if (!Schema::hasTable(self::TABLE_NAME) || Schema::hasColumns(self::TABLE_NAME, self::COLUMN_LIST)) {
            return;
        }

        Schema::table(self::TABLE_NAME, function (Blueprint $obTable) {
            $obTable->integer('site_id')->nullable();
        });
    }

    /**
     * Rollback migration
     */
    public function down()
    {
        if (!Schema::hasTable(self::TABLE_NAME) || !Schema::hasColumns(self::TABLE_NAME, self::COLUMN_LIST)) {
            return;
        }

        Schema::table(self::TABLE_NAME, function (Blueprint $obTable) {
            $obTable->dropColumn(self::COLUMN_LIST);
        });
    }
}
