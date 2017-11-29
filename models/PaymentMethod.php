<?php namespace Lovata\OrdersShopaholic\Models;

use Model;
use October\Rain\Database\Traits\Sortable;
use October\Rain\Database\Traits\Validation;

use Kharanenka\Scope\ActiveField;
use Kharanenka\Scope\CodeField;

/**
 * Class PaymentMethod
 * @package Lovata\Shopaholic\Models
 * @author Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 * 
 * @mixin \October\Rain\Database\Builder
 * @mixin \Eloquent
 *
 * @property $id
 * @property bool $active
 * @property string $code
 * @property string $name
 * @property string $preview_text
 * @property int $sort_order
 * @property \October\Rain\Argon\Argon $created_at
 * @property \October\Rain\Argon\Argon $updated_at
 * 
 * Omnipay for Shopaholic plugin
 * @property string $gateway_id
 * @property string $gateway_currency
 * @property array $gateway_property
 * @property int $before_status_id
 * @property int $after_status_id
 *
 * @property \October\Rain\Database\Collection|Order[] $order
 * @method static Order|\October\Rain\Database\Relations\HasMany order()
 */
class PaymentMethod extends Model
{
    use ActiveField;
    use CodeField;
    use Validation;
    use Sortable;

    public $table = 'lovata_orders_shopaholic_payment_methods';

    public $implement = [
        '@RainLab.Translate.Behaviors.TranslatableModel',
    ];

    public $translatable = ['name', 'preview_text'];

    /** Validation */
    public $rules = [
        'name' => 'required',
        'code' => 'required|unique:lovata_orders_shopaholic_payment_methods',
    ];

    public $attributeNames = [
        'lovata.toolbox::lang.field.name',
        'lovata.toolbox::lang.field.code',
    ];

    protected $fillable = [
        'active',
        'code',
        'name',
        'sort_order',
        'preview_text',
    ];

    protected $dates = ['created_at', 'updated_at'];

    public $hasMany = ['order' => Order::class];
}