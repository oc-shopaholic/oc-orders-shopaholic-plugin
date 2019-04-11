<?php namespace Lovata\OrdersShopaholic\Classes\Event\PaymentRestriction;

use Lovata\Toolbox\Classes\Event\AbstractModelRelationHandler;

use Lovata\OrdersShopaholic\Models\PaymentRestriction;
use Lovata\OrdersShopaholic\Classes\Item\PaymentMethodItem;

/**
 * Class PaymentRestrictionRelationHandler
 * @package Lovata\OrdersShopaholic\Classes\Event\PaymentRestriction
 * @author  Tsagan Noniev, deploy@rubium.ru, Rubium Web
 */
class PaymentRestrictionRelationHandler extends AbstractModelRelationHandler
{
    protected $iPriority = 900;

    /**
     * After attach event handler
     * @param \Model $obModel
     * @param array  $arAttachedIDList
     * @param array  $arInsertData
     */
    protected function afterAttach($obModel, $arAttachedIDList, $arInsertData)
    {
        $this->clearPaymentMethodItem($arAttachedIDList);
    }

    /**
     * After detach event handler
     * @param \Model $obModel
     * @param array  $arAttachedIDList
     */
    protected function afterDetach($obModel, $arAttachedIDList)
    {
        $this->clearPaymentMethodItem($arAttachedIDList);
    }

    /**
     * Clear cached PaymentMethod Item
     * @param array $arAttachedIDList
     */
    protected function clearPaymentMethodItem($arAttachedIDList)
    {
        if (empty($arAttachedIDList)) {
            return;
        }

        foreach ($arAttachedIDList as $iPaymentMethodID) {
            PaymentMethodItem::clearCache($iPaymentMethodID);
        }
    }

    /**
     * Get model class name
     * @return string
     */
    protected function getModelClass() : string
    {
        return PaymentRestriction::class;
    }

    /**
     * Get relation name
     * @return string
     */
    protected function getRelationName()
    {
        return 'payment_method';
    }
}
