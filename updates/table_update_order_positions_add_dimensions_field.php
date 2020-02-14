<?php namespace Lovata\OrdersShopaholic\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

/**
 * Class TableUpdateOrderPositionsAddDimensionsField
 * @package Lovata\OrdersShopaholic\Updates
 */
class TableUpdateOrderPositionsAddDimensionsField extends Migration
{
    const TABLE_NAME = 'lovata_orders_shopaholic_order_positions';

    /**
     * Apply migration
     */
    public function up()
    {
        if (!Schema::hasTable(self::TABLE_NAME)) {
            return;
        }

        $arNewFieldList = [
            'weight',
            'height',
            'length',
            'width',
        ];

        foreach ($arNewFieldList as $iKey => $sFieldName) {
            if (Schema::hasColumn(self::TABLE_NAME, $sFieldName)) {
                unset($arNewFieldList[$iKey]);
            }
        }

        if (empty($arNewFieldList)) {
            return;
        }

        Schema::table(self::TABLE_NAME, function (Blueprint $obTable) use ($arNewFieldList) {
            if (in_array('width', $arNewFieldList)) {
                $obTable->double('width')->nullable()->after('quantity');
            }
            if (in_array('length', $arNewFieldList)) {
                $obTable->double('length')->nullable()->after('quantity');
            }
            if (in_array('height', $arNewFieldList)) {
                $obTable->double('height')->nullable()->after('quantity');
            }
            if (in_array('weight', $arNewFieldList)) {
                $obTable->double('weight')->nullable()->after('quantity');
            }
        });
    }

    /**
     * Rollback migration
     */
    public function down()
    {
        if (!Schema::hasTable(self::TABLE_NAME)) {
            return;
        }

        $arNewFieldList = [
            'weight',
            'height',
            'length',
            'width',
        ];

        foreach ($arNewFieldList as $iKey => $sFieldName) {
            if (!Schema::hasColumn(self::TABLE_NAME, $sFieldName)) {
                unset($arNewFieldList[$iKey]);
            }
        }

        if (empty($arNewFieldList)) {
            return;
        }

        Schema::table(self::TABLE_NAME, function (Blueprint $obTable) use ($arNewFieldList) {
            $obTable->dropColumn($arNewFieldList);
        });
    }
}
