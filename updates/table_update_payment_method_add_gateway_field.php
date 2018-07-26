<?php namespace Lovata\OrdersShopaholic\Updates;

use DB;
use Schema;
use Crypt;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

/**
 * Class TableCreatePaymentMethod
 * @package Lovata\OrdersShopaholic\Updates
 */
class TableUpdatePaymentMethodAddTypeField extends Migration
{
    const TABLE_NAME = 'lovata_orders_shopaholic_payment_methods';

    /**
     * Apply migration
     */
    public function up()
    {
        if (!Schema::hasTable(self::TABLE_NAME)) {
            return;
        }

        if (!Schema::hasColumn(self::TABLE_NAME, 'cancel_status_id')) {
            Schema::table(self::TABLE_NAME, function(Blueprint $obTable)
            {
                $obTable->integer('cancel_status_id')->nullable()->default(0);
                $obTable->integer('fail_status_id')->nullable()->default(0);
                $obTable->boolean('send_purchase_request')->default(0);
            });
        }

        if (!Schema::hasColumn(self::TABLE_NAME, 'gateway_id')) {
            Schema::table(self::TABLE_NAME, function(Blueprint $obTable)
            {
                $obTable->string('gateway_id')->nullable();
                $obTable->string('gateway_currency')->nullable();
                $obTable->text('gateway_property')->nullable();
                $obTable->integer('before_status_id')->nullable()->default(0);
                $obTable->integer('after_status_id')->nullable()->default(0);
            });
        } else {
            $this->encryptGatewayData();
        }
    }

    /**
     * Rollback migration
     */
    public function down()
    {
        if(!Schema::hasTable(self::TABLE_NAME) || !Schema::hasColumn(self::TABLE_NAME, 'gateway_id')) {
            return;
        }

        Schema::table(self::TABLE_NAME, function(Blueprint $obTable) {
            $obTable->dropColumn(['gateway_id', 'gateway_currency', 'gateway_property', 'before_status_id', 'after_status_id', 'cancel_status_id', 'fail_status_id']);
        });
    }

    /**
     * Encrypt gateway data in current payment methods
     */
    protected function encryptGatewayData()
    {
        //Get all orders
        $obPaymentMethodList = DB::table(self::TABLE_NAME)->get();
        if ($obPaymentMethodList->isEmpty()) {
            return;
        }

        /** @var \Lovata\OrdersShopaholic\Models\PaymentMethod $obPaymentMethod */
        foreach ($obPaymentMethodList as $obPaymentMethod) {
            if (empty($obPaymentMethod->gateway_property)) {
                continue;
            }

            DB::table(self::TABLE_NAME)->where('id', $obPaymentMethod->id)->update([
                'gateway_property' => $this->encryptValue($obPaymentMethod->gateway_property),
            ]);
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
