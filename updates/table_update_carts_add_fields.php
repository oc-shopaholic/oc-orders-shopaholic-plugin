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
        if (Schema::hasTable(self::TABLE_NAME) && !Schema::hasColumn(self::TABLE_NAME, 'email')) {

            Schema::table(self::TABLE_NAME, function(Blueprint $obTable) {
                $obTable->string('email')->nullable();
            });
        }

        if (Schema::hasTable(self::TABLE_NAME) && !Schema::hasColumn(self::TABLE_NAME, 'user_data')) {

            Schema::table(self::TABLE_NAME, function(Blueprint $obTable) {
                $obTable->mediumText('user_data')->nullable();
            });
        }

        if (Schema::hasTable(self::TABLE_NAME) && !Schema::hasColumn(self::TABLE_NAME, 'shipping_address')) {

            Schema::table(self::TABLE_NAME, function(Blueprint $obTable) {
                $obTable->mediumText('shipping_address')->nullable();
            });
        }

        if (Schema::hasTable(self::TABLE_NAME) && !Schema::hasColumn(self::TABLE_NAME, 'billing_address')) {

            Schema::table(self::TABLE_NAME, function(Blueprint $obTable) {
                $obTable->mediumText('billing_address')->nullable();
            });
        }

        if (Schema::hasTable(self::TABLE_NAME) && !Schema::hasColumn(self::TABLE_NAME, 'shipping_type_id')) {
            
            Schema::table(self::TABLE_NAME, function(Blueprint $obTable) {
                $obTable->integer('shipping_type_id')->nullable();
            });
        }

        if (Schema::hasTable(self::TABLE_NAME) && !Schema::hasColumn(self::TABLE_NAME, 'payment_method_id')) {
            
            Schema::table(self::TABLE_NAME, function(Blueprint $obTable) {
                $obTable->integer('payment_method_id')->nullable();
            });
        }

        if (Schema::hasTable(self::TABLE_NAME) && !Schema::hasColumn(self::TABLE_NAME, 'property')) {
            
            Schema::table(self::TABLE_NAME, function(Blueprint $obTable) {
                $obTable->mediumText('property')->nullable();
            });
        }
    }

    /**
     * Rollback migration
     */
    public function down()
    {
        if (Schema::hasTable(self::TABLE_NAME) && Schema::hasColumn(self::TABLE_NAME, 'email')) {
            
            Schema::table(self::TABLE_NAME, function(Blueprint $obTable) {
                $obTable->dropColumn(['email']);
            });
        }

        if (Schema::hasTable(self::TABLE_NAME) && Schema::hasColumn(self::TABLE_NAME, 'user_data')) {
            
            Schema::table(self::TABLE_NAME, function(Blueprint $obTable) {
                $obTable->dropColumn(['user_data']);
            });
        }

        if (Schema::hasTable(self::TABLE_NAME) && Schema::hasColumn(self::TABLE_NAME, 'shipping_address')) {
            
            Schema::table(self::TABLE_NAME, function(Blueprint $obTable) {
                $obTable->dropColumn(['shipping_address']);
            });
        }

        if (Schema::hasTable(self::TABLE_NAME) && Schema::hasColumn(self::TABLE_NAME, 'billing_address')) {
            
            Schema::table(self::TABLE_NAME, function(Blueprint $obTable) {
                $obTable->dropColumn(['billing_address']);
            });
        }

        if (Schema::hasTable(self::TABLE_NAME) && Schema::hasColumn(self::TABLE_NAME, 'shipping_type_id')) {
            
            Schema::table(self::TABLE_NAME, function(Blueprint $obTable) {
                $obTable->dropColumn(['shipping_type_id']);
            });
        }

        if (Schema::hasTable(self::TABLE_NAME) && Schema::hasColumn(self::TABLE_NAME, 'payment_method_id')) {
            
            Schema::table(self::TABLE_NAME, function(Blueprint $obTable) {
                $obTable->dropColumn(['payment_method_id']);
            });
        }

        if (Schema::hasTable(self::TABLE_NAME) && Schema::hasColumn(self::TABLE_NAME, 'property')) {
            
            Schema::table(self::TABLE_NAME, function(Blueprint $obTable) {
                $obTable->dropColumn(['property']);
            });
        }
    }
}