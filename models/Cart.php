<?php namespace Lovata\OrdersShopaholic\Models;

use Model;
use Lovata\Buddies\Models\User;
use Kharanenka\Scope\UserBelongsTo;

/**
 * Class Cart
 * @package Lovata\OrdersShopaholic\Models
 * @author Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 *
 * @mixin \October\Rain\Database\Builder
 * @mixin \Eloquent
 * 
 * @property $id
 * @property integer $user_id
 * @property \October\Rain\Argon\Argon $created_at
 * @property \October\Rain\Argon\Argon $updated_at
 * 
 * @property User $user
 * @method static User|\October\Rain\Database\Relations\BelongsTo user()
 * @property \October\Rain\Database\Collection|CartItem[] $item
 * @method static CartItem|\October\Rain\Database\Relations\HasMany item()
 */
class Cart extends Model
{
    use UserBelongsTo;
    
    public $table = 'lovata_orders_shopaholic_carts';
    
    protected $fillable = [
        'user_id',
    ];

    protected $dates = ['created_at', 'updated_at'];
    
    public $belongsTo = ['user' => User::class];
    public $hasMany = ['item' => CartItem::class];
}