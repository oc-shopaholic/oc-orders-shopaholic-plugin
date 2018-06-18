<?php namespace Lovata\OrdersShopaholic\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

/**
 * Class TableUpdateShippingTypeAddPriceField
 * @package Lovata\OrdersShopaholic\Updates
 */
class TableUpdateShippingTypeAddPriceField extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('lovata_orders_shopaholic_shipping_types') || Schema::hasColumn('lovata_orders_shopaholic_shipping_types', 'price')) {
            return;
        }
        
        Schema::table('lovata_orders_shopaholic_shipping_types', function(Blueprint $obTable)
        {
            $obTable->decimal('price', 15, 2)->nullable();
        });
    }
    
    public function down()
    {
        if (!Schema::hasTable('lovata_orders_shopaholic_shipping_types') || !Schema::hasColumn('lovata_orders_shopaholic_shipping_types', 'price')) {
            return;
        }

        Schema::table('lovata_orders_shopaholic_shipping_types', function (Blueprint $obTable)
        {
            $obTable->dropColumn(['price']);
        });
    }
}