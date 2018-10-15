<?php namespace Lovata\OrdersShopaholic\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

use Lovata\Shopaholic\Models\Settings;
use Lovata\OrdersShopaholic\Models\Order;

/**
 * Class TableUpdateOrdersAddCurrencyField
 * @package Lovata\OrdersShopaholic\Updates
 */
class TableUpdateOrdersAddCurrencyField extends Migration
{
    const TABLE_NAME = 'lovata_orders_shopaholic_orders';

    /**
     * Apply migration
     */
    public function up()
    {
        if (!Schema::hasTable(self::TABLE_NAME) || Schema::hasColumn(self::TABLE_NAME, 'currency')) {
            return;
        }

        Schema::table(self::TABLE_NAME, function (Blueprint $obTable) {
            $obTable->string('currency')->nullable();
        });

        $this->seedDefaultCurrencyValue();
    }

    /**
     * Rollback migration
     */
    public function down()
    {
        if (!Schema::hasTable(self::TABLE_NAME) || !Schema::hasColumn(self::TABLE_NAME, 'currency')) {
            return;
        }

        Schema::table(self::TABLE_NAME, function (Blueprint $obTable) {
            $obTable->dropColumn(['currency']);
        });
    }

    /**
     * Get currency value from settings and save it to orders
     */
    protected function seedDefaultCurrencyValue()
    {
        $sCurrency = Settings::getValue('currency');

        $obOrderList = Order::all();
        if (empty($sCurrency) || empty($obOrderList)) {
            return;
        }

        /** @var Order $obOrder */
        foreach ($obOrderList as $obOrder) {
            $obOrder->currency = $sCurrency;
            $obOrder->save();
        }
    }
}
