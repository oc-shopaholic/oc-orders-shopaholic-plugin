<?php namespace Lovata\OrdersShopaholic\Updates;

use Seeder;
use Lovata\OrdersShopaholic\Models\Status;

/**
 * Class SeederDefaultStatusColors
 * @package Lovata\OrdersShopaholic\Updates
 */
class SeederDefaultStatusColors extends Seeder
{
    public function run()
    {
        $obStatusList = Status::all();
        if ($obStatusList->isEmpty()) {
            return;
        }

        /** @var Status $obStatus */
        foreach ($obStatusList as $obStatus) {
            if (!empty($obStatus->color)) {
                continue;
            }

            switch ($obStatus->code) {
                case Status::STATUS_NEW:
                    $obStatus->color = Status::STATUS_COLOR_NEW;
                    break;
                case Status::STATUS_IN_PROGRESS:
                    $obStatus->color = Status::STATUS_COLOR_IN_PROGRESS;
                    break;
                case Status::STATUS_COMPETE:
                    $obStatus->color = Status::STATUS_COLOR_COMPLETE;
                    break;
                case Status::STATUS_CANCELED:
                    $obStatus->color = Status::STATUS_COLOR_CANCELED;
                    break;
            }

            $obStatus->save();
        }
    }
}