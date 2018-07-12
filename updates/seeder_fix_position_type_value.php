<?php namespace Lovata\OrdersShopaholic\Updates;

use Seeder;

use Lovata\Shopaholic\Models\Offer;
use Lovata\OrdersShopaholic\Models\OrderPosition;
use Lovata\OrdersShopaholic\Models\CartPosition;

/**
 * Class SeederFixPositionTypeValue
 * @package Lovata\OrdersShopaholic\Updates
 */
class SeederFixPositionTypeValue extends Seeder
{
    public function run()
    {
        $this->updateCartPositionValue();
        $this->updateOrderPositionValue();
    }

    /**
     * Fix item_type value from \Lovata\Shopaholic\Models\Offer to Lovata\Shopaholic\Models\Offer
     */
    protected function updateCartPositionValue()
    {
        //Get cart position value list
        $obElementList = CartPosition::getByItemType('\\Lovata\\Shopaholic\\Models\\Offer')->get();
        if ($obElementList->isEmpty()) {
            return;
        }

        foreach ($obElementList as $obElement) {
            $obElement->item_type = Offer::class;
            $obElement->save();
        }
    }

    /**
     * Fix item_type value from \Lovata\Shopaholic\Models\Offer to Lovata\Shopaholic\Models\Offer
     */
    protected function updateOrderPositionValue()
    {
        //Get cart position value list
        $obElementList = OrderPosition::getByItemType('\\Lovata\\Shopaholic\\Models\\Offer')->get();
        if ($obElementList->isEmpty()) {
            return;
        }

        foreach ($obElementList as $obElement) {
            $obElement->item_type = Offer::class;
            $obElement->save();
        }
    }
}