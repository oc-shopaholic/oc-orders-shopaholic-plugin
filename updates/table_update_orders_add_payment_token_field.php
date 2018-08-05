<?php namespace Lovata\OrdersShopaholic\Updates;

use DB;
use Crypt;
use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

/**
 * Class TableUpdateOrdersAddPaymentTokenField
 * @package Lovata\OrdersShopaholic\Updates
 */
class TableUpdateOrdersAddPaymentTokenField extends Migration
{
    const TABLE_NAME = 'lovata_orders_shopaholic_orders';

    /**
     * Apply migration
     */
    public function up()
    {
        if (!Schema::hasTable(self::TABLE_NAME) || Schema::hasColumn(self::TABLE_NAME, 'payment_token')) {
            return;
        }

        Schema::table(self::TABLE_NAME, function (Blueprint $obTable) {
            $obTable->string('payment_token')->nullable();
        });
    }

    /**
     * Rollback migration
     */
    public function down()
    {
        if (!Schema::hasTable(self::TABLE_NAME) || !Schema::hasColumn(self::TABLE_NAME, 'payment_token')) {
            return;
        }

        Schema::table(self::TABLE_NAME, function (Blueprint $obTable) {
            $obTable->dropColumn(['payment_token']);
        });
    }
}
