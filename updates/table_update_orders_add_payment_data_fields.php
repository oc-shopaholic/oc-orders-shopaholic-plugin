<?php namespace Lovata\OrdersShopaholic\Updates;

use DB;
use Schema;
use Crypt;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

/**
 * Class TableUpdateOrdersAddPaymentDataFields
 * @package Lovata\OrdersShopaholic\Updates
 */
class TableUpdateOrdersAddPaymentDataFields extends Migration
{
    const TABLE_NAME = 'lovata_orders_shopaholic_orders';

    /**
     * Apply migration
     */
    public function up()
    {
        if (!Schema::hasTable(self::TABLE_NAME)) {
            return;
        }

        if (!Schema::hasColumn(self::TABLE_NAME, 'transaction_id')) {
            Schema::table(self::TABLE_NAME, function (Blueprint $obTable) {
                $obTable->string('transaction_id')->nullable();
            });
        }

        if (!Schema::hasColumn(self::TABLE_NAME, 'payment_data')) {
            Schema::table(self::TABLE_NAME, function (Blueprint $obTable) {
                $obTable->text('payment_data')->nullable();
                $obTable->text('payment_response')->nullable();
            });
        } else {
            $this->encryptPaymentData();
        }
    }

    /**
     * Rollback migration
     */
    public function down()
    {
        if (!Schema::hasTable(self::TABLE_NAME) || !Schema::hasColumn(self::TABLE_NAME, 'payment_data')) {
            return;
        }

        Schema::table(self::TABLE_NAME, function (Blueprint $obTable) {
            $obTable->dropColumn(['transaction_id', 'payment_data', 'payment_response']);
        });
    }

    /**
     * Encrypt payment data in current orders
     */
    protected function encryptPaymentData()
    {
        //Get all orders
        $obOrderList = DB::table(self::TABLE_NAME)->get();
        if ($obOrderList->isEmpty()) {
            return;
        }

        /** @var \Lovata\OrdersShopaholic\Models\Order $obOrder */
        foreach ($obOrderList as $obOrder) {
            if (empty($obOrder->payment_data) && empty($obOrder->payment_response)) {
                continue;
            }

            $arUpdateData = [];
            if (!empty($obOrder->payment_data)) {
                $arUpdateData['payment_data'] = $this->encryptValue($obOrder->payment_data);
            }

            if (!empty($obOrder->payment_response)) {
                $arUpdateData['payment_response'] = $this->encryptValue($obOrder->payment_response);
            }

            DB::table(self::TABLE_NAME)->where('id', $obOrder->id)->update($arUpdateData);
        }
    }

    /**
     * Try decrypt value and encrypt it
     * @param mixed $sValue
     * @return string
     */
    protected function encryptValue($sValue)
    {
        try {
            $sDecryptValue = Crypt::decrypt($sValue);
        } catch (\Exception $obException) {
            $sDecryptValue = $sValue;
        }

        try {
            $arValue = json_decode($sDecryptValue, true);
        } catch (\Exception $obException) {
            $arValue = [];
        }

        if (empty($arValue) && !empty($sDecryptValue)) {
            $arValue = $sDecryptValue;
        }

        return Crypt::encrypt($arValue);
    }
}
