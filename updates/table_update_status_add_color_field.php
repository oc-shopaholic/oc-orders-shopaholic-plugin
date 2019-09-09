<?php namespace Lovata\OrdersShopaholic\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

use Lovata\OrdersShopaholic\Models\Status;

/**
 * Class TableUpdateStatusAddColorField
 * @package Lovata\OrdersShopaholic\Updates
 */
class TableUpdateStatusAddColorField extends Migration
{
    const TABLE_NAME = 'lovata_orders_shopaholic_statuses';

    /**
     * Apply migration
     */
    public function up()
    {
        if (!Schema::hasTable(self::TABLE_NAME) || Schema::hasColumn(self::TABLE_NAME, 'color')) {
            return;
        }

        Schema::table(self::TABLE_NAME, function (Blueprint $obTable) {
            $obTable->string('color')->nullable();
        });

        $this->seedDefaultColorValue();
    }

    /**
     * Rollback migration
     */
    public function down()
    {
        if (!Schema::hasTable(self::TABLE_NAME) || !Schema::hasColumn(self::TABLE_NAME, 'color')) {
            return;
        }

        Schema::table(self::TABLE_NAME, function (Blueprint $obTable) {
            $obTable->dropColumn(['color']);
        });
    }

    /**
     * Todo update Get color value from settings and save it to orders
     */
    protected function seedDefaultColorValue()
    {
        $obStatusList = Status::get();
        if (empty($obStatusList)) {
            return;
        }

        /** @var Status $obStatus */
        foreach ($obStatusList as $obStatus) {
            if ($obStatus->code == Status::STATUS_NEW) {
                $obStatus->color = Status::STATUS_COLOR_NEW;
                $obStatus->save();
            }
            elseif ($obStatus->code == Status::STATUS_IN_PROGRESS) {
                $obStatus->color = Status::STATUS_COLOR_IN_PROGRESS;
                $obStatus->save();
            }
            elseif ($obStatus->code == Status::STATUS_COMPETE) {
                $obStatus->color = Status::STATUS_COLOR_COMPLETE;
                $obStatus->save();
            }
            elseif ($obStatus->code == Status::STATUS_CANCELED) {
                $obStatus->color = Status::STATUS_COLOR_CANCELED;
                $obStatus->save();
            }
        }
    }
}
