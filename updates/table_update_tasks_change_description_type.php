<?php namespace Lovata\OrdersShopaholic\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

/**
 * Class TableUpdateTasksChangeDescriptionType
 * @package Lovata\OrdersShopaholic\Updates
 */
class TableUpdateTasksChangeDescriptionType extends Migration
{
    const TABLE_NAME = 'lovata_orders_shopaholic_tasks';

    public function up()
    {
        if(!Schema::hasTable(self::TABLE_NAME)) {
            return;
        }
        
        Schema::table(self::TABLE_NAME, function(Blueprint $obTable)
        {
            $obTable->text('description')->nullable()->change();
        });
    }
    
    public function down()
    {
        if(!Schema::hasTable(self::TABLE_NAME)) {
            return;
        }

        Schema::table(self::TABLE_NAME, function(Blueprint $obTable)
        {
            $obTable->string('description')->nullable()->change();
        });
    }
}
