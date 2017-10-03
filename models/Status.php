<?php namespace Lovata\OrdersShopaholic\Models;

use Model;
use October\Rain\Database\Traits\Sortable;
use October\Rain\Database\Traits\Validation;

use Kharanenka\Scope\CodeField;

/**
 * Class Status
 * @package Lovata\OrdersShopaholic\Models
 * @author Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 *
 * @mixin \October\Rain\Database\Builder
 * @mixin \Eloquent
 * 
 * @property $id
 * @property string $code
 * @property string $name
 * @property int $sort_order
 * @property \October\Rain\Argon\Argon $created_at
 * @property \October\Rain\Argon\Argon $updated_at
 *
 * @property \October\Rain\Database\Collection|Order[] $order
 * @method static Order|\October\Rain\Database\Relations\HasMany order()
 */
class Status extends Model
{
    use CodeField;
    use Validation;
    use Sortable;
    
    const STATUS_NEW = 'new';
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_COMPETE = 'complete';
    const STATUS_CANCELED = 'canceled';
    
    public $table = 'lovata_orders_shopaholic_statuses';

    /** Validation */
    public $rules = [
        'name' => 'required',
        'code' => 'required|unique:lovata_orders_shopaholic_statuses',
    ];

    public $attributeNames = [
        'lovata.toolbox::lang.field.name',
        'lovata.toolbox::lang.field.code',
    ];

    protected $fillable = [
        'code',
        'name',
        'sort_order',
    ];

    protected $dates = ['created_at', 'updated_at'];
    
    public $hasMany = ['order' => Order::class];
}