<?php namespace Lovata\OrdersShopaholic\Classes\Event;

use Lovata\Toolbox\Classes\Event\ModelHandler;

use Lovata\Shopaholic\Models\Offer;
use Lovata\Shopaholic\Classes\Item\OfferItem;
use Lovata\OrdersShopaholic\Models\CartElement;

/**
 * Class OfferModelHandler
 * @package Lovata\OrdersShopaholic\Classes\Event
 * @author Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class OfferModelHandler extends ModelHandler
{
    /** @var  Offer */
    protected $obElement;

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

    /**
     * After save event handler
     */
    protected function afterSave()
    {
        $this->checkActiveField();
    }

    /**
     * After delete event handler
     */
    protected function afterDelete()
    {
        //check offer "active" field
        if(!$this->obElement->active) {
            return;
        }

        $obCartElementList = CartElement::getByOffer($this->obElement->id)->get();
        if($obCartElementList->isEmpty()) {
            return;
        }

        /** @var CartElement $obCartElement */
        foreach ($obCartElementList as $obCartElement) {
            $obCartElement->delete();
        }
    }

    /**
     * Check offer active field
     */
    protected function checkActiveField()
    {
        //check offer "active" field
        if($this->obElement->getOriginal('active') == $this->obElement->active || $this->obElement->active) {
            return;
        }

        $obCartElementList = CartElement::getByOffer($this->obElement->id)->get();
        if($obCartElementList->isEmpty()) {
            return;
        }

        /** @var CartElement $obCartElement */
        foreach ($obCartElementList as $obCartElement) {
            $obCartElement->delete();
        }
    }
}