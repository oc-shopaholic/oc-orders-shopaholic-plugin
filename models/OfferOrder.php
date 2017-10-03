<?php namespace Lovata\OrdersShopaholic\Models;

use October\Rain\Database\Pivot;
use Lovata\Shopaholic\Classes\Helper\PriceHelper;

/**
 * Class OfferOrder
 * @package Lovata\PropertiesShopaholic\Models
 * @author Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 *
 * @property int $order_id
 * @property int $offer_id
 * @property float $price
 * @property float $old_price
 * @property int $quantity
 * @property string $code
 */
class OfferOrder extends Pivot
{
    public $table = 'lovata_orders_shopaholic_offer_order';

    protected $fillable = [
        'order_id',
        'offer_id',
        'price',
        'old_price',
        'quantity',
        'code',
    ];

    /**
     * After save model method
     */
    public function afterSave()
    {
        //Get order object
        /** @var Order $obOrder */
        $obOrder = Order::find($this->order_id);
        if(empty($obOrder)){
            return;
        }

        $obOrder->save();
    }
    
    /**
     * Get price value
     * @return double
     */
    public function getPriceValue()
    {
        return $this->attributes['price'];
    }

    /**
     * Get price value
     * @return double
     */
    public function getOldPriceValue()
    {
        return $this->attributes['old_price'];
    }

    /**
     * Accessor for price
     *
     * @param  float  $dPrice
     * @return string
     */
    public function getPriceAttribute($dPrice)
    {
        /** @var PriceHelper $obPriceHelper */
        $obPriceHelper = app()->make(PriceHelper::class);
        return $obPriceHelper->get($dPrice);
    }

    /**
     * Accessor for old_price
     *
     * @param  float  $dPrice
     * @return string
     */
    public function getOldPriceAttribute($dPrice)
    {
        /** @var PriceHelper $obPriceHelper */
        $obPriceHelper = app()->make(PriceHelper::class);
        return $obPriceHelper->get($dPrice);
    }

    /**
     * Format price to decimal format
     *
     * @param  string  $sPrice
     */
    public function setPriceAttribute($sPrice)
    {
        $sPrice = str_replace(',', '.', $sPrice);
        $sPrice = (float) preg_replace("/[^0-9\.]/", "",$sPrice);
        $this->attributes['price'] = (float)$sPrice;
    }

    /**
     * Format discount price to decimal format
     *
     * @param  string  $sPrice
     */
    public function setOldPriceAttribute($sPrice)
    {
        $sPrice = str_replace(',', '.', $sPrice);
        $sPrice = (float) preg_replace("/[^0-9\.]/", "",$sPrice);
        if($sPrice <= $this->getPriceValue()) {
            $sPrice = 0;
        }

        $this->attributes['old_price'] = $sPrice;
    }

    /**
     * Get total price value
     * @return float
     */
    public function getTotalPriceValue()
    {
        return $this->quantity * $this->getPriceValue();
    }

    /**
     * Get total price string
     * @return string
     */
    public function getTotalPrice()
    {
        /** @var PriceHelper $obPriceHelper */
        $obPriceHelper = app()->make(PriceHelper::class);
        return $obPriceHelper->get($this->getTotalPriceValue());
    }
}