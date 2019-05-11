<?php namespace Lovata\OrdersShopaholic\Classes\Event\PaymentRestriction;

use Lovata\OrdersShopaholic\Models\PaymentRestriction;
use Lovata\OrdersShopaholic\Classes\Item\PaymentMethodItem;

/**
 * Class PaymentRestrictionModelHandler
 * @package Lovata\OrdersShopaholic\Classes\Event\PaymentRestriction
 * @author  Tsagan Noniev, deploy@rubium.ru, Rubium Web
 */
class PaymentRestrictionModelHandler
{
    protected $iPriority = 1000;
    /** @var PaymentRestriction */
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
        return PaymentRestriction::class;
    }

    /**
     * After save event handler
     */
    protected function afterSave()
    {
        //Get shipping type list
        $obPaymentMethodList = $this->obElement->payment_method;
        if ($obPaymentMethodList->isEmpty()) {
            return;
        }

        foreach ($obPaymentMethodList as $obPaymentMethod) {
            PaymentMethodItem::clearCache($obPaymentMethod->id);
        }
    }

    /**
     * After delete event handler
     */
    protected function afterDelete()
    {
        $this->obElement->payment_method()->detach();
    }
}
