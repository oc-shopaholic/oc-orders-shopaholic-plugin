<?php namespace Lovata\OrdersShopaholic\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

class CreateShippingRestrictionsTable extends Migration
{
    public function up()
    {
        Schema::create('lovata_ordersshopaholic_shipping_restrictions', function(Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->boolean('active')->default(0);
            $table->string('name');
            $table->string('code');
            $table->string('restriction')->nullable()->index('restrictions_restriction');
            $table->integer('sort_order')->nullable();
            $table->integer('shipping_type_id')->index('restrictions_shipping_type_id');
            $table->text('description')->nullable();
            $table->text('property')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('lovata_ordersshopaholic_shipping_restrictions');
    }
}
