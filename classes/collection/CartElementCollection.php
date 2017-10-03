<?php namespace Lovata\OrdersShopaholic\Classes\Collection;

use Lovata\Toolbox\Classes\Collection\ElementCollection;

use Lovata\Shopaholic\Models\Settings;
use Lovata\Shopaholic\Classes\Helper\PriceHelper;
use Lovata\OrdersShopaholic\Classes\Item\CartElementItem;

/**
 * Class CartElementCollection
 * @package Lovata\OrdersShopaholic\Classes\Collection
 * @author Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class CartElementCollection extends ElementCollection
{
    /** @var array CartElementItem[] */
    protected $arItemList = [];
    
    /**
     * Make element item
     * @param int   $iElementID
     * @param \Lovata\Shopaholic\Models\Brand  $obElement
     *
     * @return CartElementItem
     */
    protected function makeItem($iElementID, $obElement = null)
    {
        if(!empty($this->arItemList) && isset($this->arItemList[$iElementID]) && $this->arItemList[$iElementID] instanceof CartElementItem) {
            return $this->arItemList[$iElementID];
        }
        
        $obItem = CartElementItem::make($iElementID, $obElement);
        $this->arItemList[$iElementID] = $obItem;
        
        return $obItem;
    }

    /**
     * Cart item has product
     * @param int $iProductID
     * @return bool
     */
    public function hasProduct($iProductID)
    {
        if($this->isEmpty() || empty($iProductID)) {
            return false;
        }
        
        $arItemList = $this->all();
        /** @var CartElementItem $obItem */
        foreach($arItemList as $obItem) {
            
            $obOfferItem = $obItem->offer;
            if($obOfferItem->isEmpty()) {
                continue;
            }
            
            if($obOfferItem->product_id == $iProductID) {
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
        if($this->isEmpty() || empty($iOfferID)) {
            return false;
        }
        
        $arItemList = $this->all();
        /** @var CartElementItem $obItem */
        foreach($arItemList as $obItem) {
            if($obItem->offer_id == $iOfferID) {
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
        
        /** @var PriceHelper $obPriceHelper */
        $obPriceHelper = app()->make(PriceHelper::class);
        return $obPriceHelper->get($fPrice);
    }

    /**
     * Get total price value
     * @return float
     */
    public function getTotalPriceValue()
    {
        if($this->isEmpty()) {
            return 0;
        }

        $fPrice = 0;
        $arItemList = $this->all();
        /** @var CartElementItem $obItem */
        foreach($arItemList as $obItem) {
            $fPrice += $obItem->price_value;
        }

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