<?php namespace Lovata\OrdersShopaholic\Updates;

use Schema;

use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

/**
 * Class TableUpdateCartPositionsAddDeletedField
 * @package Lovata\OrdersShopaholic\Updates
 */
class TableUpdateCartPositionsAddDeletedField extends Migration
{
    const TABLE_NAME = 'lovata_orders_shopaholic_cart_positions';

    public function up()
    {
        if (!Schema::hasTable(self::TABLE_NAME) || Schema::hasColumn(self::TABLE_NAME, 'deleted_at')) {
            return;
        }

        Schema::table(self::TABLE_NAME, function(Blueprint $obTable) {
            $obTable->softDeletes();
        });
    }

    public function down()
    {
        if (!Schema::hasTable(self::TABLE_NAME) || !Schema::hasColumn(self::TABLE_NAME, 'deleted_at')) {
            return;
        }

        Schema::table(self::TABLE_NAME, function(Blueprint $obTable) {
            $obTable->dropColumn(['deleted_at']);
        });
    }
}
