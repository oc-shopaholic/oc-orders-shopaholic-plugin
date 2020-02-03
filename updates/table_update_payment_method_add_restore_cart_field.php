<?php namespace Lovata\OrdersShopaholic\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

/**
 * Class TableUpdatePaymentMethodAddRestoreCartField
 * @package Lovata\OrdersShopaholic\Updates
 */
class TableUpdatePaymentMethodAddRestoreCartField extends Migration
{
    const TABLE_NAME = 'lovata_orders_shopaholic_payment_methods';

    /**
     * Apply migration
     */
    public function up()
    {
        if (!Schema::hasTable(self::TABLE_NAME) || Schema::hasColumn(self::TABLE_NAME, 'restore_cart')) {
            return;
        }

        Schema::table(self::TABLE_NAME, function(Blueprint $obTable)
        {
            $obTable->boolean('restore_cart')->default(0);
        });
    }

    /**
     * Rollback migration
     */
    public function down()
    {
        if(!Schema::hasTable(self::TABLE_NAME) || !Schema::hasColumn(self::TABLE_NAME, 'restore_cart')) {
            return;
        }

        Schema::table(self::TABLE_NAME, function(Blueprint $obTable) {
            $obTable->dropColumn(['restore_cart']);
        });
    }
}
