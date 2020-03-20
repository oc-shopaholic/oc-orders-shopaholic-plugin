<?php namespace Lovata\OrdersShopaholic\Models;

use Lang;
use Model;
use Event;
use October\Rain\Database\Traits\Validation;

use Kharanenka\Scope\NameField;
use Lovata\Toolbox\Traits\Helpers\TraitCached;
use Lovata\Toolbox\Classes\Helper\PriceHelper;

/**
 * Class OrderPromoMechanism
 * @package Lovata\OrdersShopaholic\Models
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 *
 * @mixin \October\Rain\Database\Builder
 * @mixin \Eloquent
 *
 * @property int                       $id
 * @property int                       $order_id
 * @property int                       $mechanism_id
 * @property string                    $name
 * @property string                    $type
 * @property bool                      $increase
 * @property int                       $priority
 * @property float                     $discount_value
 * @property string                    $discount_type
 * @property bool                      $final_discount
 * @property array                     $property
 * @property int                       $element_id
 * @property string                    $element_type
 * @property array                     $element_data
 * @property string                    $description
 * @property \October\Rain\Argon\Argon $created_at
 * @property \October\Rain\Argon\Argon $updated_at
 *
 * @property Order                     $order
 * @method static Order|\October\Rain\Database\Relations\BelongsTo order()
 * @property PromoMechanism            $mechanism
 * @method static PromoMechanism|\October\Rain\Database\Relations\BelongsTo mechanism()
 *
 * @method static $this withIncrease()
 * @method static $this withDecrease()
 */
class OrderPromoMechanism extends Model
{
    use Validation;
    use NameField;
    use TraitCached;

    const EVENT_GET_DESCRIPTION = 'shopaholic.order.promo_mechanism.description';

    public $table = 'lovata_orders_shopaholic_order_promo_mechanism';

    /** Validation */
    public $rules = [
        'order_id'     => 'required',
        'mechanism_id' => 'required',
        'name'         => 'required',
        'type'         => 'required',
    ];

    public $attributeNames = [
        'name' => 'lovata.toolbox::lang.field.name',
        'type' => 'lovata.toolbox::lang.field.type',
    ];

    public $fillable = [
        'order_id',
        'mechanism_id',
        'name',
        'type',
        'increase',
        'priority',
        'discount_value',
        'discount_type',
        'final_discount',
        'property',
        'element_id',
        'element_type',
        'element_data',
    ];

    public $cached = [
        'id',
        'order_id',
        'mechanism_id',
        'name',
        'type',
        'increase',
        'priority',
        'discount_value',
        'discount_type',
        'final_discount',
        'property',
        'element_id',
        'element_type',
        'element_data',
    ];

    public $jsonable = ['property', 'element_data'];

    public $dates = ['created_at', 'updated_at'];

    public $belongsTo = [
        'order'           => [Order::class],
        'promo_mechanism' => [PromoMechanism::class, 'key' => 'mechanism_id'],
    ];

    /**
     * Get promo mechanism class object by type value
     * @return \Lovata\OrdersShopaholic\Classes\PromoMechanism\InterfacePromoMechanism|null
     */
    public function getTypeObject()
    {
        $sClassName = $this->type;
        if (empty($sClassName) || !class_exists($sClassName)) {
            return null;
        }

        return new $sClassName($this->priority, $this->discount_value, $this->discount_type, $this->final_discount, $this->property, $this->increase);
    }

    /**
     * Get discount type options (backend)
     * @return array
     */
    public function getDiscountTypeOptions() : array
    {
        return [
            PromoMechanism::PERCENT_TYPE => Lang::get('lovata.ordersshopaholic::lang.field.discount_type_'.PromoMechanism::PERCENT_TYPE),
            PromoMechanism::FIXED_TYPE   => Lang::get('lovata.ordersshopaholic::lang.field.discount_type_'.PromoMechanism::FIXED_TYPE),
        ];
    }

    /**
     * Get element with increase flag = true
     * @param PromoMechanism $obQuery
     * @return PromoMechanism
     */
    public function scopeWithIncrease($obQuery)
    {
        return $obQuery->where('increase', true);
    }

    /**
     * Get element with increase flag = false
     * @param PromoMechanism $obQuery
     * @return PromoMechanism
     */
    public function scopeWithDecrease($obQuery)
    {
        return $obQuery->where('increase', false);
    }

    /**
     * Set priority attribute
     * @param string $sValue
     */
    protected function setPriorityAttribute($sValue)
    {
        $sValue = (int) $sValue;
        if ($sValue < 0) {
            $sValue = 0;
        }

        $this->attributes['priority'] = $sValue;
    }

    /**
     * Get discount type name attribute value (backend)
     * @return string
     */
    protected function getDiscountTypeNameAttribute()
    {
        if ($this->discount_type == PromoMechanism::PERCENT_TYPE) {
            return Lang::get('lovata.ordersshopaholic::lang.field.discount_type_'.PromoMechanism::PERCENT_TYPE);
        }

        return Lang::get('lovata.ordersshopaholic::lang.field.discount_type_'.PromoMechanism::FIXED_TYPE);
    }

    /**
     * Get type name attribute value (backend)
     * @return string
     */
    protected function getTypeNameAttribute()
    {
        $sClassName = $this->type;
        if (empty($sClassName) || !class_exists($sClassName)) {
            return null;
        }

        return $sClassName::getName();
    }

    /**
     * Get relation name attribute value (backend)
     * @return string
     */
    protected function getRelationNameAttribute()
    {
        $sResult = $this->name;
        $sTypeName = $this->type_name;
        if (!empty($sTypeName)) {
            $sResult .= ' ('.$sTypeName.')';
        }

        return $sResult;
    }

    /**
     * Get description attribute value
     * @return string
     */
    protected function getDescriptionAttribute()
    {
        $sDescription = (string) Event::fire(self::EVENT_GET_DESCRIPTION, $this, true);

        return $sDescription;
    }

    /**
     * Set discount value attribute
     * @param string $sValue
     */
    protected function setDiscountPriceAttribute($sValue)
    {
        $sValue = PriceHelper::toFloat($sValue);

        $this->attributes['discount_value'] = $sValue;
    }
}
