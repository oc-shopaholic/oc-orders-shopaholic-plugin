<?php namespace Lovata\OrdersShopaholic\Updates;

use Schema;

use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

/**
 * Class TableUpdateStatusAddIsUserShowField
 * @package Lovata\OrdersShopaholic\Updates
 */
class TableUpdateStatusAddIsUserShowField extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('lovata_orders_shopaholic_statuses') || Schema::hasColumn('lovata_orders_shopaholic_statuses', 'is_user_show')) {
            return;
        }

        Schema::table('lovata_orders_shopaholic_statuses', function(Blueprint $obTable)
        {
            $obTable->boolean('is_user_show')->default(0);
            $obTable->integer('user_status_id')->nullable();
            $obTable->text('preview_text')->nullable();
        });
    }
    
    public function down()
    {
        if (!Schema::hasTable('lovata_orders_shopaholic_statuses') || !Schema::hasColumn('lovata_orders_shopaholic_statuses', 'is_user_show')) {
            return;
        }

        Schema::table('lovata_orders_shopaholic_statuses', function(Blueprint $obTable)
        {
            $obTable->dropColumn(['is_user_show', 'user_status_id', 'preview_text']);
        });
    }
}
