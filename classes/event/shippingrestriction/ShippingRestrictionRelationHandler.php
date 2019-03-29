<?php namespace Lovata\OrdersShopaholic\Classes\Event\ShippingRestriction;

use Lovata\Toolbox\Classes\Event\AbstractModelRelationHandler;

use Lovata\OrdersShopaholic\Models\ShippingRestriction;
use Lovata\OrdersShopaholic\Classes\Item\ShippingTypeItem;

/**
 * Class ShippingRestrictionRelationHandler
 * @package Lovata\OrdersShopaholic\Classes\Event\ShippingRestriction
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class ShippingRestrictionRelationHandler extends AbstractModelRelationHandler
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
        $this->clearShippingTypeItem($arAttachedIDList);
    }

    /**
     * After detach event handler
     * @param \Model $obModel
     * @param array  $arAttachedIDList
     */
    protected function afterDetach($obModel, $arAttachedIDList)
    {
        $this->clearShippingTypeItem($arAttachedIDList);
    }

    /**
     * Clear cached ShippingType Item
     * @param array $arAttachedIDList
     */
    protected function clearShippingTypeItem($arAttachedIDList)
    {
        if (empty($arAttachedIDList)) {
            return;
        }

        foreach ($arAttachedIDList as $iShippingTypeID) {
            ShippingTypeItem::clearCache($iShippingTypeID);
        }
    }

    /**
     * Get model class name
     * @return string
     */
    protected function getModelClass() : string
    {
        return ShippingRestriction::class;
    }

    /**
     * Get relation name
     * @return string
     */
    protected function getRelationName()
    {
        return 'shipping_type';
    }
}
