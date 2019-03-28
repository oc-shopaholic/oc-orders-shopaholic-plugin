<?php namespace Lovata\OrdersShopaholic\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

/**
 * Class TableUpdateShippingTypesAddMethodAndPropertyFields
 * @package Lovata\OrdersShopaholic\Updates
 */
class TableUpdateShippingTypesAddMethodAndPropertyFields extends Migration
{
    const TABLE_NAME = 'lovata_orders_shopaholic_shipping_types';

    /**
     * Apply migration
     */
    public function up()
    {
        if (!Schema::hasTable(self::TABLE_NAME) || Schema::hasColumn(self::TABLE_NAME, 'property')) {
            return;
        }

        Schema::table(self::TABLE_NAME, function (Blueprint $obTable) {
            $obTable->text('property')->nullable();
            $obTable->string('api_class')->nullable();
        });
    }

    /**
     * Rollback migration
     */
    public function down()
    {
        if (!Schema::hasTable(self::TABLE_NAME) || !Schema::hasColumn(self::TABLE_NAME, 'property')) {
            return;
        }

        Schema::table(self::TABLE_NAME, function (Blueprint $obTable) {
            $obTable->dropColumn(['api_class', 'property']);
        });
    }
}
