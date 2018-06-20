<?php namespace Lovata\OrdersShopaholic\Updates;

use Schema;

use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

/**
 * Class TableUpdateStatusAddIsUserShowField
 * @package Lovata\OrdersShopaholic\Updates
 */
class TableUpdateOrdersRemoveField extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('lovata_orders_shopaholic_orders') || !Schema::hasColumn('lovata_orders_shopaholic_orders', 'total_price')) {
            return;
        }

        Schema::table('lovata_orders_shopaholic_orders', function(Blueprint $obTable)
        {
            $obTable->dropColumn(['total_price']);
        });
    }
    
    public function down()
    {
        if (!Schema::hasTable('lovata_orders_shopaholic_orders') || Schema::hasColumn('lovata_orders_shopaholic_orders', 'total_price')) {
            return;
        }

        Schema::table('lovata_orders_shopaholic_orders', function(Blueprint $obTable)
        {
            $obTable->decimal('total_price', 15, 2)->nullable();
        });
    }
}
