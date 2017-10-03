<?php namespace Lovata\OrdersShopaholic\Models;

use Model;
use Lovata\Shopaholic\Models\Offer;

/**
 * Class CartItem
 * @package Lovata\Shopaholic\Models
 * @author Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 *
 * @mixin \October\Rain\Database\Builder
 * @mixin \Eloquent
 * 
 * @property $id
 * @property integer $cart_id
 * @property integer $offer_id
 * @property integer $quantity
 * @property \October\Rain\Argon\Argon $created_at
 * @property \October\Rain\Argon\Argon $updated_at
 *
 * @property Cart $cart
 * @method static Cart|\October\Rain\Database\Relations\BelongsTo cart()
 *
 * @property Offer $offer
 * @method static Offer|\October\Rain\Database\Relations\BelongsTo offer()
 * 
 * @method static $this getByCart(int $iCartID)
 * @method static $this getByOffer(int $iOfferID)
 */
class CartItem extends Model
{
    public $table = 'lovata_orders_shopaholic_cart_item';
    
    public $fillable = [
        'offer_id',
        'cart_id',
        'quantity',
    ];
    
    public $belongsTo = [
        'cart' => [Cart::class],
        'offer' => [Offer::class, 'key' => 'offer_id'],
    ];

    public $dates = ['created_at', 'updated_at'];
    
    /**
     * Get element by cart ID
     * @param CartItem $obQuery
     * @param string $sData
     * @return CartItem
     */
    public function scopeGetByCart($obQuery, $sData)
    {
        if(!empty($sData)) {
            $obQuery->where('cart_id', $sData);
        }

        return $obQuery;
    }

    /**
     * Get element by offer ID
     * @param CartItem $obQuery
     * @param string $sData
     * @return CartItem
     */
    public function scopeGetByOffer($obQuery, $sData)
    {
        if(!empty($sData)) {
            $obQuery->where('offer_id', $sData);
        }

        return $obQuery;
    }
}