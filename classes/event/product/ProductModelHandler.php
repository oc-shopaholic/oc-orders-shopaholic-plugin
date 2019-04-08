<?php namespace Lovata\OrdersShopaholic\Classes\Event\Product;

use Lovata\Toolbox\Classes\Event\ModelHandler;

use Lovata\Shopaholic\Models\Offer;
use Lovata\Shopaholic\Models\Product;
use Lovata\Shopaholic\Classes\Item\ProductItem;
use Lovata\OrdersShopaholic\Models\CartPosition;

/**
 * Class ProductModelHandler
 * @package Lovata\OrdersShopaholic\Classes\Event\Product
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
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
     * @throws
     */
    protected function afterDelete()
    {
        //check offer "active" field
        if (!$this->obElement->active) {
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
        if (!$this->isFieldChanged('active') || $this->obElement->active) {
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
        //Get offer ID list
        $arOfferIDList = $this->obElement->offer()->active()->lists('id');
        if (empty($arOfferIDList)) {
            return;
        }

        foreach ($arOfferIDList as $iOfferID) {
            $obCartPositionList = CartPosition::getByItemID($iOfferID)->getByItemType(Offer::class)->get();
            if ($obCartPositionList->isEmpty()) {
                continue;
            }

            /** @var CartPosition $obCartPosition */
            foreach ($obCartPositionList as $obCartPosition) {
                $obCartPosition->forceDelete();
            }
        }
    }
}
