<?php namespace Lovata\OrdersShopaholic\Classes\Event\ShippingRestriction;

use Lovata\OrdersShopaholic\Models\ShippingRestriction;
use Lovata\OrdersShopaholic\Classes\Item\ShippingTypeItem;

/**
 * Class ShippingRestrictionModelHandler
 * @package Lovata\OrdersShopaholic\Classes\Event\ShippingRestriction
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class ShippingRestrictionModelHandler
{
    protected $iPriority = 1000;
    /** @var ShippingRestriction */
    protected $obElement;

    /**
     * Add listeners
     */
    public function subscribe()
    {
        $sModelClass = $this->getModelClass();
        $sModelClass::extend(function ($obElement) {

            /** @var \Model $obElement */
            $obElement->bindEvent('model.afterSave', function () use ($obElement) {
                $this->obElement = $obElement;
                $this->afterSave();
            }, $this->iPriority);

            /** @var \Model $obElement */
            $obElement->bindEvent('model.afterDelete', function () use ($obElement) {
                $this->obElement = $obElement;
                $this->afterDelete();
            }, $this->iPriority);
        });
    }

    /**
     * Get model class name
     * @return string
     */
    protected function getModelClass()
    {
        return ShippingRestriction::class;
    }

    /**
     * After save event handler
     */
    protected function afterSave()
    {
        //Get shipping type list
        $obShippingTypeList = $this->obElement->shipping_type;
        if ($obShippingTypeList->isEmpty()) {
            return;
        }

        foreach ($obShippingTypeList as $obShippingType) {
            ShippingTypeItem::clearCache($obShippingType->id);
        }
    }

    /**
     * After delete event handler
     */
    protected function afterDelete()
    {
        $this->obElement->shipping_type()->detach();
    }
}
