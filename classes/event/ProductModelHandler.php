<?php namespace Lovata\OrdersShopaholic\Classes\Event;

use Lovata\Toolbox\Classes\Event\ModelHandler;

use Lovata\Shopaholic\Models\Product;
use Lovata\Shopaholic\Classes\Item\ProductItem;
use Lovata\OrdersShopaholic\Models\CartElement;

/**
 * Class ProductModelHandler
 * @package Lovata\OrdersShopaholic\Classes\Event
 * @author Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class ProductModelHandler extends ModelHandler
{
    /** @var  Product */
    protected $obElement;

    /**
     * Get model class name
     * @return string
     */
    protected function getModelClass()
    {
        return Product::class;
    }

    /**
     * Get item class name
     * @return string
     */
    protected function getItemClass()
    {
        return ProductItem::class;
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

        //Get offer ID list
        $arOfferIDList = $this->obElement->offer()->active()->lists('id');
        if(empty($arOfferIDList)) {
            return;
        }

        foreach ($arOfferIDList as $iOfferID) {
            $obCartElementList = CartElement::getByOffer($iOfferID)->get();
            if($obCartElementList->isEmpty()) {
                return;
            }

            /** @var CartElement $obCartElement */
            foreach ($obCartElementList as $obCartElement) {
                $obCartElement->delete();
            }
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

        //Get offer ID list
        $arOfferIDList = $this->obElement->offer()->active()->lists('id');
        if(empty($arOfferIDList)) {
            return;
        }

        foreach ($arOfferIDList as $iOfferID) {
            $obCartElementList = CartElement::getByOffer($iOfferID)->get();
            if($obCartElementList->isEmpty()) {
                return;
            }

            /** @var CartElement $obCartElement */
            foreach ($obCartElementList as $obCartElement) {
                $obCartElement->delete();
            }
        }
    }
}