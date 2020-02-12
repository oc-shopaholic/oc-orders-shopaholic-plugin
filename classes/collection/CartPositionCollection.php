<?php namespace Lovata\OrdersShopaholic\Classes\Collection;

use Lovata\Toolbox\Classes\Collection\ElementCollection;

use Lovata\Shopaholic\Classes\Helper\CurrencyHelper;

use Lovata\OrdersShopaholic\Classes\Processor\CartProcessor;
use Lovata\OrdersShopaholic\Classes\Item\CartPositionItem;

/**
 * Class CartPositionCollection
 * @package Lovata\OrdersShopaholic\Classes\Collection
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class CartPositionCollection extends ElementCollection
{
    const ITEM_CLASS = CartPositionItem::class;

    /**
     * Cart item has product
     * @param int $iProductID
     * @return bool
     */
    public function hasProduct($iProductID)
    {
        if ($this->isEmpty() || empty($iProductID)) {
            return false;
        }

        $arPositionList = $this->all();
        /** @var CartPositionItem $obPositionItem */
        foreach ($arPositionList as $obPositionItem) {

            $obOfferItem = $obPositionItem->offer;
            if ($obOfferItem->isEmpty()) {
                continue;
            }

            if ($obOfferItem->product_id == $iProductID) {
                return true;
            }
        }

        return false;
    }

    /**
     * Cart item has offer
     * @param int $iOfferID
     * @return bool
     */
    public function hasOffer($iOfferID)
    {
        if ($this->isEmpty() || empty($iOfferID)) {
            return false;
        }

        $arPositionList = $this->all();
        /** @var CartPositionItem $obPositionItem */
        foreach ($arPositionList as $obPositionItem) {
            if ($obPositionItem->offer->id == $iOfferID) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get total price string
     * @return string
     */
    public function getTotalPrice()
    {
        $obPriceData = $this->getTotalPriceData();

        return $obPriceData->price;
    }

    /**
     * Get total price value
     * @return float
     */
    public function getTotalPriceValue()
    {
        $obPriceData = $this->getTotalPriceData();

        return $obPriceData->price_value;
    }


    /**
     * Get old total price string
     * @return string
     */
    public function getOldTotalPrice()
    {
        $obPriceData = $this->getTotalPriceData();

        return $obPriceData->old_price;
    }

    /**
     * Get old total price value
     * @return float
     */
    public function getOldTotalPriceValue()
    {
        $obPriceData = $this->getTotalPriceData();

        return $obPriceData->old_price_value;
    }

    /**
     * Get discount total price string
     * @return string
     */
    public function getDiscountTotalPrice()
    {
        $obPriceData = $this->getTotalPriceData();

        return $obPriceData->discount_price;
    }

    /**
     * Get discount total price value
     * @return float
     */
    public function getDiscountTotalPriceValue()
    {
        $obPriceData = $this->getTotalPriceData();

        return $obPriceData->discount_price_value;
    }

    /**
     * Get total position price data
     * @return \Lovata\OrdersShopaholic\Classes\PromoMechanism\TotalPriceContainer
     */
    public function getTotalPriceData()
    {
        $obPriceData = CartProcessor::instance()->getCartPositionTotalPriceData();

        return $obPriceData;
    }

    /**
     * Get currency symbol
     * @return null|string
     */
    public function getCurrency()
    {
        return CurrencyHelper::instance()->getActiveCurrencySymbol();
    }

    /**
     * Get currency code
     * @return null|string
     */
    public function getCurrencyCode()
    {
        return CurrencyHelper::instance()->getActiveCurrencyCode();
    }

    /**
     * Get the total count of all order positions
     * @return int
     */
    public function getTotalQuantity()
    {
        $iQuantityCount = 0;

        $arCartPositionList = $this->all();

        /** @var \Lovata\OrdersShopaholic\Classes\Item\CartPositionItem $obCartPositionItem */
        foreach ($arCartPositionList as $obCartPositionItem) {
            $iQuantityCount += $obCartPositionItem->quantity;
        }

        return $iQuantityCount;
    }

    /**
     * Get total weight
     * @return float
     */
    public function getTotalWeight()
    {
        $fWeight = 0;

        $arCartPositionList = $this->all();

        /** @var \Lovata\OrdersShopaholic\Classes\Item\CartPositionItem $obCartPositionItem */
        foreach ($arCartPositionList as $obCartPositionItem) {
            $fWeight += $obCartPositionItem->weight;
        }

        return $fWeight;
    }
}
