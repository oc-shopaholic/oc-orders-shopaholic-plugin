<?php namespace Lovata\OrdersShopaholic\Models;

use Kharanenka\Scope\UserBelongsTo;
use Model;
use Carbon\Carbon;
use October\Rain\Database\Builder;
use October\Rain\Database\Collection;
use Lovata\Buddies\Models\User;
use October\Rain\Database\Relations\HasMany;

/**
 * Class Cart
 * @package Lovata\Shopaholic\Models
 * @author Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 *
 * @mixin Builder
 * @mixin \Eloquent
 * 
 * @property $id
 * @property integer $user_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * 
 * @property User $user
 * @property Collection|CartItem[] $item
 * @method static $this|HasMany item()
 */
class Cart extends Model
{
    use UserBelongsTo;
    
    public $table = 'lovata_ordersshopaholic_carts';
    
    protected $fillable = [
        'user_id',
    ];

    protected $dates = ['created_at', 'updated_at'];
    
    public $belongsTo = [
        'user' => 'Lovata\Buddies\Models\User'
    ];
    
    public $hasMany = [
        'item' => 'Lovata\OrdersShopaholic\Models\CartItem'
    ];
}