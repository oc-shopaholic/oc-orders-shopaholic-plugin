<?php namespace Lovata\OrdersShopaholic\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

/**
 * Class TableUpdateTaxesAddAppliedToShippingPrice
 * @package Lovata\OrdersShopaholic\Updates
 */
class TableUpdateTaxesAddAppliedToShippingPrice extends Migration
{
    const TABLE_NAME = 'lovata_shopaholic_taxes';

    /**
     * Apply migration
     */
    public function up()
    {
        if (!Schema::hasTable(self::TABLE_NAME) || Schema::hasColumn(self::TABLE_NAME, 'applied_to_shipping_price')) {
            return;
        }

        Schema::table(self::TABLE_NAME, function (Blueprint $obTable) {
            $obTable->boolean('applied_to_shipping_price')->default(0);
        });
    }

    /**
     * Rollback migration
     */
    public function down()
    {
        if (!Schema::hasTable(self::TABLE_NAME) || !Schema::hasColumn(self::TABLE_NAME, 'applied_to_shipping_price')) {
            return;
        }

        Schema::table(self::TABLE_NAME, function (Blueprint $obTable) {
            $obTable->dropColumn(['applied_to_shipping_price']);
        });
    }
}
