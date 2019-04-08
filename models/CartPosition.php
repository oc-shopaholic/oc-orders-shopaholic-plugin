<?php namespace Lovata\OrdersShopaholic\Models;

use Model;
use October\Rain\Database\Traits\SoftDelete;
use October\Rain\Database\Traits\Validation;

use Lovata\Toolbox\Traits\Helpers\TraitCached;
use Lovata\Toolbox\Traits\Models\SetPropertyAttributeTrait;

/**
 * Class CartPosition
 * @package Lovata\OrdersShopaholic\Models
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 *
 * @mixin \October\Rain\Database\Builder
 * @mixin \Eloquent
 *
 * @property                                 $id
 * @property int                             $cart_id
 * @property int                             $item_id
 * @property string                          $item_type
 * @property array                           $property
 * @property integer                         $quantity
 * @property \October\Rain\Argon\Argon       $created_at
 * @property \October\Rain\Argon\Argon       $updated_at
 * @property \October\Rain\Argon\Argon       $deleted_at
 *
 * @property Cart                            $cart
 * @method static Cart|\October\Rain\Database\Relations\BelongsTo cart()
 *
 * @property \Lovata\Shopaholic\Models\Offer $item
 * @method static \Lovata\Shopaholic\Models\Offer|\October\Rain\Database\Relations\MorphTo item()
 *
 * @method static $this getByCart(int $iCartID)
 * @method static $this getByItemID(int $iItemID)
 * @method static $this getByItemType(string $sItemType)
 */
class CartPosition extends Model
{
    use SoftDelete;
    use TraitCached;
    use Validation;
    use SetPropertyAttributeTrait;

    public $table = 'lovata_orders_shopaholic_cart_positions';

    public $rules = [
        'cart_id'   => 'required',
        'item_id'   => 'required',
        'item_type' => 'required',
    ];

    public $customMessages = [
        'cart_id.required' => 'lovata.ordersshopaholic::lang.message.cart_id_required',
        'item_id.required' => 'lovata.ordersshopaholic::lang.message.item_required',
        'item_type.required' => 'lovata.ordersshopaholic::lang.message.item_required',
    ];

    public $fillable = [
        'cart_id',
        'item_id',
        'item_type',
        'cart_id',
        'quantity',
        'property',
    ];

    public $cached = [
        'id',
        'item_id',
        'item_type',
        'quantity',
        'property',
    ];

    public $belongsTo = [
        'cart' => [Cart::class],
    ];

    public $morphTo = [
        'item' => [],
    ];

    public $jsonable = ['property'];
    public $dates = ['created_at', 'updated_at'];

    /**
     * Get element by cart ID
     * @param CartPosition $obQuery
     * @param string       $sData
     * @return CartPosition
     */
    public function scopeGetByCart($obQuery, $sData)
    {
        if (!empty($sData)) {
            $obQuery->where('cart_id', $sData);
        }

        return $obQuery;
    }

    /**
     * Get element by item ID
     * @param CartPosition $obQuery
     * @param string       $sData
     * @return CartPosition
     */
    public function scopeGetByItemID($obQuery, $sData)
    {
        if (!empty($sData)) {
            $obQuery->where('item_id', $sData);
        }

        return $obQuery;
    }

    /**
     * Get element by item type
     * @param CartPosition $obQuery
     * @param string       $sData
     * @return CartPosition
     */
    public function scopeGetByItemType($obQuery, $sData)
    {
        if (!empty($sData)) {
            $obQuery->where('item_type', $sData);
        }

        return $obQuery;
    }
}
