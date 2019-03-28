<?php namespace Lovata\OrdersShopaholic\Classes\Event\ShippingType;

use Lovata\Toolbox\Classes\Event\AbstractModelRelationHandler;

use Lovata\OrdersShopaholic\Models\ShippingType;
use Lovata\OrdersShopaholic\Classes\Item\ShippingTypeItem;

/**
 * Class ShippingTypeRelationHandler
 * @package Lovata\OrdersShopaholic\Classes\Event\ShippingType
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class ShippingTypeRelationHandler extends AbstractModelRelationHandler
{
    protected $iPriority = 900;

    /**
     * After attach event handler
     * @param ShippingType $obModel
     * @param array        $arAttachedIDList
     * @param array        $arInsertData
     */
    protected function afterAttach($obModel, $arAttachedIDList, $arInsertData)
    {
        $this->clearShippingTypeItem($obModel);
    }

    /**
     * After detach event handler
     * @param ShippingType $obModel
     * @param array        $arAttachedIDList
     */
    protected function afterDetach($obModel, $arAttachedIDList)
    {
        $this->clearShippingTypeItem($obModel);
    }

    /**
     * Clear cached ShippingType Item
     * @param ShippingType $obModel
     */
    protected function clearShippingTypeItem($obModel)
    {
        if (empty($obModel)) {
            return;
        }

        ShippingTypeItem::clearCache($obModel->id);
    }

    /**
     * Get model class name
     * @return string
     */
    protected function getModelClass() : string
    {
        return ShippingType::class;
    }

    /**
     * Get relation name
     * @return string
     */
    protected function getRelationName()
    {
        return 'shipping_restriction';
    }
}
