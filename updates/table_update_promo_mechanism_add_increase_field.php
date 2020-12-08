<?php namespace Lovata\OrdersShopaholic\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

/**
 * Class TableUpdatePromoMechanismAddIncreaseField
 * @package Lovata\OrdersShopaholic\Updates
 */
class TableUpdatePromoMechanismAddIncreaseField extends Migration
{
    const TABLE_NAME = 'lovata_orders_shopaholic_promo_mechanism';

    public function up()
    {
        if(!Schema::hasTable(self::TABLE_NAME) || Schema::hasColumn(self::TABLE_NAME, 'increase')) {
            return;
        }
        
        Schema::table(self::TABLE_NAME, function(Blueprint $obTable)
        {
            $obTable->boolean('auto_add')->after('type')->default(0);
            $obTable->boolean('increase')->after('type')->default(0);
        });
    }
    
    public function down()
    {
        if(!Schema::hasTable(self::TABLE_NAME) || !Schema::hasColumn(self::TABLE_NAME, 'increase')) {
            return;
        }

        Schema::table(self::TABLE_NAME, function(Blueprint $obTable)
        {
            $obTable->dropColumn(['increase', 'auto_add']);
        });
    }
}
