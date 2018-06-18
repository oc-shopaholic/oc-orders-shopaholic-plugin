<?php namespace Lovata\OrdersShopaholic\Classes\Item;

use Lovata\Toolbox\Classes\Helper\PriceHelper;

use Lovata\Shopaholic\Models\Offer;
use Lovata\Shopaholic\Classes\Item\OfferItem;
use Lovata\OrdersShopaholic\Models\CartPosition;

/**
 * Class CartPositionItem
 * @package Lovata\OrdersShopaholic\Classes\Item
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 *
 * @property string                                    $price
 * @property float                                     $price_value
 *
 * @property \Lovata\Shopaholic\Classes\Item\OfferItem $item
 * @property \Lovata\Shopaholic\Classes\Item\OfferItem $offer
 */
class CartPositionItem extends AbstractPositionItem
{
    const MODEL_CLASS = CartPosition::class;

    public $arPriceField = ['price'];

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
        $obItem = $this->item;
        if ($obItem->isEmpty()) {
            return 0;
        }

        $fPrice = $obItem->price_value * $this->quantity;
        $fPrice = PriceHelper::round($fPrice);

        return $fPrice;
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
