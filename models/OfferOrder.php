<?php namespace Lovata\OrdersShopaholic\Models;

use DB;
use Lovata\Shopaholic\Classes\CPrice;
use Lovata\Shopaholic\Models\Offer;
use Lovata\Shopaholic\Models\Settings;
use October\Rain\Database\Pivot;

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
 * 
 * //TODO: Требует рефакторинга
 */
class OfferOrder extends Pivot
{
    public $table = 'lovata_ordersshopaholic_offer_order';

    protected $fillable = [
        'order_id',
        'offer_id',
        'price',
        'old_price',
        'quantity',
        'code',
    ];
    
    public function beforeUpdate()
    {
        return $this->updateOrderOfferQuantity();
    }
    
    public function afterSave()
    {
        //Get order object
        /** @var Order $obOrder */
        $obOrder = Order::find($this->order_id);
        if(empty($obOrder)){
            return false;
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
     * @param  string  $iPrice
     * @return string
     */
    public function getPriceAttribute($iPrice)
    {
        if(!empty($iPrice)){
            return CPrice::getPriceInFormat((float)$iPrice);
        }
        return $iPrice;
    }


    /**
     * Accessor for old_price
     *
     * @param  string  $iPrice
     * @return string
     */
    public function getOldPriceAttribute($iPrice)
    {
        if(!empty($iPrice)){
            return CPrice::getPriceInFormat((float)$iPrice);
        }
        return $iPrice;
    }

    /**
     * Format price to decimal format
     *
     * @param  string  $sPrice
     */
    public function setPriceAttribute($sPrice)
    {
        $sPrice = str_replace(',', '.', $sPrice);
        $sPrice = preg_replace("/[^0-9\.]/", "",$sPrice);
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
        $sPrice = preg_replace("/[^0-9\.]/", "",$sPrice);
        if($sPrice <= $this->getPriceValue()) {
            $sPrice = 0;
        }

        $this->attributes['old_price'] = (float)$sPrice;
    }

    public function updateOrderOfferQuantity()
    {
        // Get order behavior flags from settings
        $bDecrementQuantityAfterOrder = Settings::getValue('decrement_quantity_after_order');
        if(empty($bDecrementQuantityAfterOrder)) {
            return true;
        }
        
        $newQuantity = $this->attributes['quantity'];
        $currQuantity = $this->original['quantity'];
        if($currQuantity != $newQuantity){
            if($newQuantity > $currQuantity){
                $bInc = true;
                $sQuantity = $newQuantity - $currQuantity;
            }else{
                $bInc = false;
                $sQuantity = $currQuantity - $newQuantity;
            }

            DB::beginTransaction();
            try{
                if($bInc){
                    DB::table('lovata_shopaholic_offers')->where('id', $this->offer_id)->decrement('quantity', $sQuantity);
                }else{
                    DB::table('lovata_shopaholic_offers')->where('id', $this->offer_id)->increment('quantity', $sQuantity);
                }
            }catch (\Exception $e){
                DB::rollBack();
                return false;
            }
            
            DB::commit();
        }
        
        return true;
    }

    /**
     * Add offer quantity before delete
     * @param Offer $obOffer
     * @return bool
     */
    public static function addOrderOfferQuantityBeforeDelete($obOffer) {

        // Get order behavior flags from settings
        $bDecrementQuantityAfterOrder = Settings::getValue('decrement_quantity_after_order');
        if(empty($bDecrementQuantityAfterOrder)) {
            return true;
        }

        $iQuantity = $obOffer->pivot->quantity;

        DB::beginTransaction();
        try{
            DB::table('lovata_shopaholic_offers')->where('id', $obOffer->id)->increment('quantity', $iQuantity);
        }catch (\Exception $e){
            DB::rollBack();
            return false;
        }

        DB::commit();

        return true;
    }
}