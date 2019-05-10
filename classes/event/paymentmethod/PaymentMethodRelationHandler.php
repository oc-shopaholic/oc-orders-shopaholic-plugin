<?php namespace Lovata\OrdersShopaholic\Classes\Event\PaymentMethod;

use Lovata\Toolbox\Classes\Event\AbstractModelRelationHandler;

use Lovata\OrdersShopaholic\Models\PaymentMethod;
use Lovata\OrdersShopaholic\Classes\Item\PaymentMethodItem;

/**
 * Class PaymentMethodRelationHandler
 * @package Lovata\OrdersShopaholic\Classes\Event\PaymentMethod
 * @author  Tsagan Noniev, deploy@rubium.ru, Rubium Web
 */
class PaymentMethodRelationHandler extends AbstractModelRelationHandler
{
    protected $iPriority = 900;

    /**
     * After attach event handler
     * @param PaymentMethod $obModel
     * @param array        $arAttachedIDList
     * @param array        $arInsertData
     */
    protected function afterAttach($obModel, $arAttachedIDList, $arInsertData)
    {
        $this->clearPaymentMethodItem($obModel);
    }

    /**
     * After detach event handler
     * @param PaymentMethod $obModel
     * @param array        $arAttachedIDList
     */
    protected function afterDetach($obModel, $arAttachedIDList)
    {
        $this->clearPaymentMethodItem($obModel);
    }

    /**
     * Clear cached PaymentMethod Item
     * @param PaymentMethod $obModel
     */
    protected function clearPaymentMethodItem($obModel)
    {
        if (empty($obModel)) {
            return;
        }

        PaymentMethodItem::clearCache($obModel->id);
    }

    /**
     * Get model class name
     * @return string
     */
    protected function getModelClass() : string
    {
        return PaymentMethod::class;
    }

    /**
     * Get relation name
     * @return string
     */
    protected function getRelationName()
    {
        return 'payment_restriction';
    }
}
