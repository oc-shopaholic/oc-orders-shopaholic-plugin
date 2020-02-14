<?php namespace Lovata\OrdersShopaholic\Models;

use Event;
use Model;
use October\Rain\Database\Traits\Validation;

use Kharanenka\Scope\ActiveField;
use Kharanenka\Scope\CodeField;

use Lovata\Toolbox\Traits\Models\SetPropertyAttributeTrait;
use Lovata\OrdersShopaholic\Classes\Restriction\PaymentRestrictionByShippingType;
use Lovata\OrdersShopaholic\Classes\Restriction\RestrictionByTotalPrice;

/**
 * Class PaymentRestriction
 * @package Lovata\OrdersShopaholic\Models
 * @author  Tsagan Noniev, deploy@rubium.ru, Rubium Web
 *
 * @mixin \October\Rain\Database\Builder
 * @mixin \Eloquent
 *
 * @property int                                               $id
 * @property bool                                              $active
 * @property string                                            $name
 * @property string                                            $code
 * @property string                                            $restriction
 * @property string                                            $description
 * @property array                                             $property
 * @property int                                               $sort_order
 * @property \October\Rain\Argon\Argon                         $created_at
 * @property \October\Rain\Argon\Argon                         $updated_at
 *
 * @property \October\Rain\Database\Collection|PaymentMethod[] $payment_method
 * @method static PaymentMethod|\October\Rain\Database\Relations\BelongsToMany payment_method()
 */
class PaymentRestriction extends Model
{
    use Validation;
    use ActiveField;
    use CodeField;
    use SetPropertyAttributeTrait;

    const EVENT_GET_PAYMENT_RESTRICTION_LIST = 'shopaholic.paymentmethod.get.restriction.list';

    /**
     * @var string The database table used by the model.
     */
    public $table = 'lovata_ordersshopaholic_payment_restrictions';

    /** Validation */
    public $rules = [
        'name' => 'required',
        'code' => 'required|unique:lovata_ordersshopaholic_payment_restrictions',
    ];

    protected $guarded = ['*'];
    protected $fillable = [
        'active',
        'code',
        'name',
        'description',
        'property',
        'restriction',
    ];

    public $jsonable = ['property'];
    public $dates = ['created_at', 'updated_at'];

    /**
     * @var array Relations
     */
    public $hasOne = [];
    public $hasMany = [];
    public $belongsTo = [];
    public $belongsToMany = [
        'payment_method' => [
            PaymentMethod::class,
            'table' => 'lovata_ordersshopaholic_payment_restrictions_link',
        ],
    ];
    public $morphTo = [];
    public $morphOne = [];
    public $morphMany = [];
    public $attachOne = [];
    public $attachMany = [];

    /**
     * Get restriction options
     * @return array
     */
    public function getRestrictionOptions()
    {
        $arResult = [
            RestrictionByTotalPrice::class          => 'lovata.ordersshopaholic::lang.restriction.handler.by_total_price',
            PaymentRestrictionByShippingType::class => 'lovata.ordersshopaholic::lang.restriction.handler.by_shipping_type',
        ];

        $arEventResult = Event::fire(self::EVENT_GET_PAYMENT_RESTRICTION_LIST);
        if (empty($arEventResult)) {
            return $arResult;
        }

        foreach ($arEventResult as $arClassList) {
            if (empty($arClassList) || !is_array($arClassList)) {
                continue;
            }

            $arResult = array_merge($arResult, $arClassList);
        }

        asort($arResult);

        return $arResult;
    }
}
