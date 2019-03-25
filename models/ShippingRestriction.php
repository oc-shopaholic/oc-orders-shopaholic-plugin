<?php namespace Lovata\OrdersShopaholic\Models;

use Event;
use Model;
use Kharanenka\Scope\ActiveField;
use Kharanenka\Scope\CodeField;
use October\Rain\Database\Traits\Validation;
use Lovata\Toolbox\Traits\Helpers\TraitCached;
use Lovata\Toolbox\Traits\Models\SetPropertyAttributeTrait;

/**
 * ShippingRestriction Model
 */
class ShippingRestriction extends Model
{
    use ActiveField;
    use Validation;
    use CodeField;
    use TraitCached;
    use SetPropertyAttributeTrait;

    const EVENT_GET_SHIPPING_RESTRICTION_LIST = 'shopaholic.shippingtype.get.restriction.list';

    /**
     * @var string The database table used by the model.
     */
    public $table = 'lovata_ordersshopaholic_shipping_restrictions';

    /** Validation */
    public $rules = [
        'name' => 'required',
        'code' => 'required|unique:lovata_ordersshopaholic_shipping_restrictions',
    ];

    /**
     * @var array Guarded fields
     */
    protected $guarded = ['*'];

    /**
     * @var array Fillable fields
     */
    protected $fillable = [
        'active',
        'code',
        'name',
        'sort_order',
        'description',
        'property',
        'restriction',
        'shipping_type',
    ];

    public $cached = [
        'id',
        'active',
        'code',
        'name',
        'sort_order',
        'description',
        'property',
        'restriction',
        'shipping_type',
    ];

    public $jsonable = ['property'];
    public $dates = ['created_at', 'updated_at'];

    /**
     * @var array Relations
     */
    public $hasOne = [];
    public $hasMany = [];
    public $belongsTo = [
        'shipping_type' => [
            ShippingType::class,
            'key' => 'shipping_type_id',
        ],
    ];
    public $belongsToMany = [];
    public $morphTo = [];
    public $morphOne = [];
    public $morphMany = [];
    public $attachOne = [];
    public $attachMany = [];

    public function restrictionOptions() {

        $eventResult = Event::fire(self::EVENT_GET_SHIPPING_RESTRICTION_LIST);

        $options = [];

        if (is_array($eventResult)) {
        
            foreach ($eventResult as $shipping_restriction) {
                
                if (is_array($shipping_restriction) && count($shipping_restriction) > 0) {

                    $options = array_merge($shipping_restriction, $options);
                }
            }
        }

        return $options;
    }
}
