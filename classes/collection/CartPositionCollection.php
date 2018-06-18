<?php namespace Lovata\OrdersShopaholic\Classes\Collection;

use Lovata\Toolbox\Classes\Helper\PriceHelper;
use Lovata\Toolbox\Classes\Collection\ElementCollection;

use Lovata\Shopaholic\Models\Settings;
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
        $fPrice = $this->getTotalPriceValue();

        return PriceHelper::format($fPrice);
    }

    /**
     * Get total price value
     * @return float
     */
    public function getTotalPriceValue()
    {
        if ($this->isEmpty()) {
            return 0;
        }

        $fPrice = 0;
        $arPositionList = $this->all();
        /** @var CartPositionItem $obPositionItem */
        foreach ($arPositionList as $obPositionItem) {
            $fPrice += $obPositionItem->price_value;
        }

        $fPrice = PriceHelper::round($fPrice);

        return $fPrice;
    }

    /**
     * Get currency value
     * @return null|string
     */
    public function getCurrency()
    {
        return Settings::getValue('currency');
    }
}
