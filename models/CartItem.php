<?php namespace Lovata\OrdersShopaholic\Models;

use Carbon\Carbon;
use Lovata\Shopaholic\Classes\CPrice;
use Lovata\Shopaholic\Models\Offer;
use Model;
use October\Rain\Database\Builder;
use System\Classes\PluginManager;

/**
 * Class CartItem
 * @package Lovata\Shopaholic\Models
 * @author Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 *
 * @mixin Builder
 * @mixin \Eloquent
 * @mixin \Lovata\CustomShopaholic\Classes\CartItemExtend
 * 
 * @property $id
 * @property integer $cart_id
 * @property integer $offer_id
 * @property integer $quantity
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property \Lovata\OrdersShopaholic\Models\Cart $cart
 * 
 * @method static $this getByCart(int $iCartID)
 * @method static $this getByOffer(int $iOfferID)
 */
class CartItem extends Model
{
    public $table = 'lovata_ordersshopaholic_cart_item';
    
    public $fillable = [
        'offer_id',
        'cart_id',
        'quantity',
    ];
    
    public $belongsTo = [
        'cart' => ['Lovata\OrdersShopaholic\Models\Cart'],
        'offer' => ['Lovata\Shopaholic\Models\Offer', 'key' => 'product_id'],
    ];

    public $dates = ['created_at', 'updated_at'];

    /**
     * CartItem constructor.
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        if(PluginManager::instance()->hasPlugin('Lovata.CustomShopaholic')) {
            \Lovata\CustomShopaholic\Classes\CartItemExtend::constructExtend($this);
        }

        parent::__construct($attributes);
    }
    
    /**
     * Get element by cart ID
     * @param \Illuminate\Database\Eloquent\Builder $obQuery
     * @param string $sData
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeGetByCart($obQuery, $sData)
    {
        if(!empty($sData)) {
            $obQuery->where('cart_id', $sData);
        }

        return $obQuery;
    }

    /**
     * Get element by cart ID
     * @param \Illuminate\Database\Eloquent\Builder $obQuery
     * @param string $sData
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeGetByOffer($obQuery, $sData)
    {
        if(!empty($sData)) {
            $obQuery->where('offer_id', $sData);
        }

        return $obQuery;
    }

    /**
     * Get cart item data
     * @return array
     */
    public function getData() {

        $arResult = Offer::getCacheData($this->offer_id);
        if(empty($arResult)) {
            return [];
        }

        $arResult['cart_quantity'] = $this->quantity;
        $arResult['cart_price_value'] = $arResult['price_value'] * $this->quantity;
        $arResult['cart_old_price_value'] = $arResult['old_price_value'] * $this->quantity;
        $arResult['cart_price'] = CPrice::getPriceInFormat($arResult['cart_price_value']);
        $arResult['cart_old_price'] = CPrice::getPriceInFormat($arResult['cart_old_price_value']);

        if(PluginManager::instance()->hasPlugin('Lovata.CustomShopaholic')) {
            \Lovata\CustomShopaholic\Classes\CartItemExtend::getData($arResult, $this);
        }
        
        return $arResult;
    }
}