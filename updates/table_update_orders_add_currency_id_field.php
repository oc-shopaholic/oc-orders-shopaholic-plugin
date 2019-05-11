<?php namespace Lovata\OrdersShopaholic\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

use Lovata\Shopaholic\Models\Currency;
use Lovata\OrdersShopaholic\Models\Order;

/**
 * Class TableUpdateOrdersAddCurrencyIdField
 * @package Lovata\OrdersShopaholic\Updates
 */
class TableUpdateOrdersAddCurrencyIdField extends Migration
{
    const TABLE_NAME = 'lovata_orders_shopaholic_orders';

    /**
     * Apply migration
     */
    public function up()
    {
        if (!Schema::hasTable(self::TABLE_NAME) || Schema::hasColumn(self::TABLE_NAME, 'currency_id')) {
            return;
        }

        Schema::table(self::TABLE_NAME, function (Blueprint $obTable) {
            $obTable->integer('currency_id')->insigned()->nullable();
            $obTable->index('currency_id');
        });

        $this->seedDefaultCurrencyValue();

        Schema::table(self::TABLE_NAME, function (Blueprint $obTable) {
            $obTable->dropColumn(['currency']);
        });
    }

    /**
     * Rollback migration
     */
    public function down()
    {
        if (!Schema::hasTable(self::TABLE_NAME) || !Schema::hasColumn(self::TABLE_NAME, 'currency_id')) {
            return;
        }

        Schema::table(self::TABLE_NAME, function (Blueprint $obTable) {
            $obTable->dropColumn(['currency_id']);
        });

        Schema::table(self::TABLE_NAME, function (Blueprint $obTable) {
            $obTable->string('currency')->nullable();
        });
    }

    /**
     * Get currency_id value from settings and save it to orders
     */
    protected function seedDefaultCurrencyValue()
    {
        if (!Schema::hasTable('lovata_shopaholic_currency')) {
            return;
        }

        $obCurrencyList = Currency::all();
        $obOrderList = Order::all();
        if ($obCurrencyList->isEmpty() || $obOrderList->isEmpty()) {
            return;
        }

        /** @var Order $obOrder */
        foreach ($obOrderList as $obOrder) {
            $obCurrency = $obCurrencyList->where('code', $obOrder->currency)->first();
            if (empty($obCurrency)) {
                continue;
            }

            $obOrder->currency_id = $obCurrency->id;
            $obOrder->save();
        }
    }
}
