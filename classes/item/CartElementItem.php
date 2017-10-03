<?php namespace Lovata\OrdersShopaholic\Classes\Item;

use Lovata\Toolbox\Classes\Item\ElementItem;

use Lovata\Shopaholic\Plugin;
use Lovata\Shopaholic\Models\Settings;
use Lovata\Shopaholic\Classes\Helper\PriceHelper;
use Lovata\Shopaholic\Classes\Item\OfferItem;
use Lovata\OrdersShopaholic\Models\CartItem;

/**
 * Class CartElementItem
 * @package Lovata\OrdersShopaholic\Classes\Item
 * @author Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 *
 * @property           $id
 * @property string    $offer_id
 * @property string    $quantity
 * @property string    $currency
 * @property string    $price
 * @property float     $price_value
 *
 * @property OfferItem $offer
 */
class CartElementItem extends ElementItem
{
    const CACHE_TAG_ELEMENT = 'orders-shopaholic-cart-item-element';

    /** @var CartItem */
    protected $obElement = null;

    public $arRelationList = [
        'offer' => [
            'class' => OfferItem::class,
            'field' => 'offer_id',
        ],
    ];

    /**
     * Set element object
     */
    protected function setElementObject()
    {
        if(!empty($this->obElement) && ! $this->obElement instanceof CartItem) {
            $this->obElement = null;
        }

        if(!empty($this->obElement) || empty($this->iElementID)) {
            return;
        }

        $this->obElement = CartItem::find($this->iElementID);
    }

    /**
     * Get cache tag array for model
     * @return array
     */
    protected static function getCacheTag()
    {
        return [Plugin::CACHE_TAG, self::CACHE_TAG_ELEMENT];
    }

    /**
     * Set element data from model object
     *
     * @return array
     */
    protected function getElementData()
    {
        if(empty($this->obElement)) {
            return null;
        }

        $arResult = [
            'id'       => $this->obElement->id,
            'offer_id' => $this->obElement->offer_id,
            'quantity' => $this->obElement->quantity,
        ];

        return $arResult;
    }

    /**
     * Get price string
     * @return string
     */
    public function getPriceAttribute()
    {
        $fPrice = $this->price_value;

        /** @var PriceHelper $obPriceHelper */
        $obPriceHelper = app()->make(PriceHelper::class);
        return $obPriceHelper->get($fPrice);
    }
    
    /**
     * Get price value
     * @return float
     */
    public function getPriceValueAttribute()
    {
        $obOfferItem = $this->offer;
        if($obOfferItem->isEmpty()) {
            return 0;
        }
        
        return $obOfferItem->price_value * $this->quantity; 
    }
    
    /**
     * Get currency value
     * @return null|string
     */
    protected function getCurrencyAttribute()
    {
        return Settings::getValue('currency');
    }
}