<?php namespace Lovata\OrdersShopaholic\Classes\Event\Offer;

use Lovata\Toolbox\Classes\Event\ModelHandler;

use Lovata\Shopaholic\Models\Offer;
use Lovata\Shopaholic\Classes\Item\OfferItem;
use Lovata\OrdersShopaholic\Models\CartPosition;

/**
 * Class OfferModelHandler
 * @package Lovata\OrdersShopaholic\Classes\Event\Offer
 * @author Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class OfferModelHandler extends ModelHandler
{
    /** @var  Offer */
    protected $obElement;

    /**
     * After save event handler
     */
    protected function afterSave()
    {
        $this->checkActiveField();
    }

    /**
     * After delete event handler
     * @throws
     */
    protected function afterDelete()
    {
        //check offer "active" field
        if(!$this->obElement->active) {
            return;
        }

        $this->removeCartPositionList();
    }

    /**
     * Check offer active field
     * @throws
     */
    protected function checkActiveField()
    {
        //check offer "active" field
        if(!$this->isFieldChanged('active') || $this->obElement->active) {
            return;
        }

        $this->removeCartPositionList();
    }

    /**
     * Remove cart elements with offers
     * @throws \Exception
     */
    protected function removeCartPositionList()
    {
        $obCartPositionList = CartPosition::getByItemID($this->obElement->id)->getByItemType(Offer::class)->get();
        if($obCartPositionList->isEmpty()) {
            return;
        }

        /** @var CartPosition $obCartPosition */
        foreach ($obCartPositionList as $obCartPosition) {
            $obCartPosition->forceDelete();
        }
    }

    /**
     * Get model class name
     * @return string
     */
    protected function getModelClass()
    {
        return Offer::class;
    }

    /**
     * Get item class name
     * @return string
     */
    protected function getItemClass()
    {
        return OfferItem::class;
    }
}
