<?php namespace Lovata\OrdersShopaholic\Classes\Item;

use Lovata\OrdersShopaholic\Classes\Processor\CartProcessor;

use Lovata\Shopaholic\Models\Offer;
use Lovata\Shopaholic\Classes\Item\OfferItem;
use Lovata\OrdersShopaholic\Models\CartPosition;

/**
 * Class CartPositionItem
 * @package Lovata\OrdersShopaholic\Classes\Item
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 *
 * @property int                                                            $item_id
 * @property string                                                         $item_type
 * @property array                                                          $property
 * @property integer                                                        $quantity
 * @property string                                                         $price
 * @property float                                                          $price_value
 * @property string                                                         $old_price
 * @property float                                                          $old_price_value
 * @property string                                                         $discount_price
 * @property float                                                          $discount_price_value
 * @property \Lovata\OrdersShopaholic\Classes\PromoMechanism\PriceContainer $price_data
 *
 * @property \Lovata\Shopaholic\Classes\Item\OfferItem                      $item
 * @property \Lovata\Shopaholic\Classes\Item\OfferItem                      $offer
 */
class CartPositionItem extends AbstractPositionItem
{
    const MODEL_CLASS = CartPosition::class;

    public $arPriceField = ['price', 'old_price', 'discount_price'];

    /** @var CartPosition */
    protected $obElement = null;

    /** @var \Lovata\OrdersShopaholic\Classes\Processor\AbstractOrderPositionProcessor|null */
    protected $obOrderPositionProcessor;

    /**
     * Get item field value
     * @return \Lovata\OrdersShopaholic\Classes\Processor\AbstractOrderPositionProcessor|null
     */
    public function getOrderPositionProcessor()
    {
        if ($this->obOrderPositionProcessor !== null) {
            return $this->obOrderPositionProcessor;
        }

        $sModelClass = $this->item_type;
        if (!isset($this->arItemTypeList[$sModelClass])) {
            return null;
        }

        $sClassName = $this->arItemTypeList[$sModelClass]['order_processor'];

        $obPositionProcessor = app($sClassName, [$this]);
        $this->obOrderPositionProcessor = $obPositionProcessor;

        return $obPositionProcessor;
    }

    /**
     * Get price value
     * @return float
     */
    protected function getPriceValueAttribute()
    {
        $obPriceData = $this->price_data;

        return $obPriceData->price_value;
    }

    /**
     * Get old price value
     * @return float
     */
    protected function getOldPriceValueAttribute()
    {
        $obPriceData = $this->price_data;

        return $obPriceData->old_price_value;
    }

    /**
     * Get discount price value
     * @return float
     */
    protected function getDiscountPriceValueAttribute()
    {
        $obPriceData = $this->price_data;

        return $obPriceData->discount_price_value;
    }

    /**
     * Get price data for position
     * @return \Lovata\OrdersShopaholic\Classes\PromoMechanism\PriceContainer
     */
    protected function getPriceDataAttribute()
    {
        $obPriceData = CartProcessor::instance()->getCartPositionPriceData($this->id);

        return $obPriceData;
    }

    /**
     * Get offer field value
     * @return OfferItem
     */
    protected function getOfferAttribute()
    {
        if ($this->item_type != Offer::class) {
            return OfferItem::make(null);
        }

        return OfferItem::make($this->item_id);
    }
}
