<?php namespace Lovata\OrdersShopaholic\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

/**
 * Class TableUpdateOrderPromoMechanismAddIncreaseField
 * @package Lovata\OrdersShopaholic\Updates
 */
class TableUpdateOrderPromoMechanismAddIncreaseField extends Migration
{
    const TABLE_NAME = 'lovata_orders_shopaholic_order_promo_mechanism';

    public function up()
    {
        if(!Schema::hasTable(self::TABLE_NAME) || Schema::hasColumn(self::TABLE_NAME, 'increase')) {
            return;
        }
        
        Schema::table(self::TABLE_NAME, function(Blueprint $obTable)
        {
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
            $obTable->dropColumn(['increase']);
        });
    }
}
