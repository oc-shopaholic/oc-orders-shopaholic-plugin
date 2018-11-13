<?php namespace Lovata\OrdersShopaholic\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

/**
 * Class TableCreateTasks
 * @package Lovata\OrdersShopaholic\Updates
 */
class TableCreateTasks extends Migration
{
    const TABLE_NAME = 'lovata_orders_shopaholic_tasks';

    public function up()
    {
        if(Schema::hasTable(self::TABLE_NAME)) {
            return;
        }
        
        Schema::create(self::TABLE_NAME, function(Blueprint $obTable)
        {
            $obTable->engine = 'InnoDB';
            $obTable->increments('id')->unsigned();
            $obTable->boolean('sent')->default(0);
            $obTable->dateTime('date')->nullable();
            $obTable->string('status');
            $obTable->string('title');
            $obTable->string('description')->nullable();
            $obTable->string('mail_template')->nullable();
            $obTable->integer('order_id')->unsigned()->nullable();
            $obTable->integer('user_id')->unsigned()->nullable();
            $obTable->integer('manager_id')->unsigned()->nullable();
            $obTable->integer('author_id')->unsigned()->nullable();
            $obTable->timestamps();

            $obTable->index('status');
            $obTable->index('order_id');
            $obTable->index('user_id');
            $obTable->index('manager_id');
            $obTable->index('author_id');
        });
    }
    
    public function down()
    {
        Schema::dropIfExists(self::TABLE_NAME);
    }
}
