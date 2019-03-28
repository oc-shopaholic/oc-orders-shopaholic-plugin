<?php namespace Lovata\OrdersShopaholic\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

/**
 * Class TableUpdateCartsAddFields
 * @package Lovata\OrdersShopaholic\Updates
 */
class TableUpdateCartsAddFields extends Migration
{
    const TABLE_NAME = 'lovata_orders_shopaholic_carts';

    /**
     * Apply migration
     */
    public function up()
    {
        if (!Schema::hasTable(self::TABLE_NAME) || Schema::hasColumn(self::TABLE_NAME, 'property')) {
            return;
        }

        Schema::table(self::TABLE_NAME, function (Blueprint $obTable) {
            $obTable->integer('shipping_type_id')->nullable();
            $obTable->integer('payment_method_id')->nullable();
            $obTable->string('email')->nullable();
            $obTable->text('user_data')->nullable();
            $obTable->text('property')->nullable();
            $obTable->text('shipping_address')->nullable();
            $obTable->text('billing_address')->nullable();
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
            $obTable->dropColumn(['shipping_type_id', 'payment_method_id', 'email', 'user_data', 'property', 'shipping_address', 'billing_address']);
        });
    }
}
