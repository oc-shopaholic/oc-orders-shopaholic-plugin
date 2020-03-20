<?php namespace Lovata\OrdersShopaholic\Models;

use Lang;
use Model;
use October\Rain\Database\Traits\Validation;

use Kharanenka\Scope\TypeField;
use Kharanenka\Scope\NameField;
use Lovata\Toolbox\Classes\Helper\PriceHelper;

use Lovata\OrdersShopaholic\Classes\PromoMechanism\PromoMechanismStore;

/**
 * Class PromoMechanism
 * @package Lovata\OrdersShopaholic\Models
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 *
 * @mixin \October\Rain\Database\Builder
 * @mixin \Eloquent
 *
 * @property                                           $id
 * @property string                                    $name
 * @property string                                    $type
 * @property bool                                      $increase
 * @property bool                                      $auto_add
 * @property int                                       $priority
 * @property float                                     $discount_value
 * @property string                                    $discount_type
 * @property bool                                      $final_discount
 * @property array                                     $property
 * @property \October\Rain\Argon\Argon                 $created_at
 * @property \October\Rain\Argon\Argon                 $updated_at
 *
 * @method static $this withIncrease()
 * @method static $this withDecrease()
 * @method static $this getAutoAdd()
 */
class PromoMechanism extends Model
{
    use Validation;
    use NameField;
    use TypeField;

    const FIXED_TYPE = 'fixed';
    const PERCENT_TYPE = 'percent';

    public $table = 'lovata_orders_shopaholic_promo_mechanism';

    /** Validation */
    public $rules = [
        'name'           => 'required',
        'type'           => 'required',
        'discount_value' => 'required',
    ];

    public $attributeNames = [
        'name'           => 'lovata.toolbox::lang.field.name',
        'type'           => 'lovata.toolbox::lang.field.type',
        'discount_value' => 'lovata.toolbox::lang.field.discount_value',
    ];

    public $fillable = [
        'name',
        'type',
        'increase',
        'auto_add',
        'priority',
        'discount_value',
        'discount_type',
        'final_discount',
        'property',
    ];

    public $jsonable = ['property'];

    public $dates = ['created_at', 'updated_at'];

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
     * Get promo mechanism list for backend
     * @return array
     */
    public function getTypeOptions() : array
    {
        return PromoMechanismStore::instance()->getMechanismOptions();
    }

    /**
     * Change field properties (backend)
     * @param      $obFieldList
     * @param null $sContext
     */
    public function filterFields($obFieldList, $sContext = null)
    {
        if (!property_exists($obFieldList, 'type')) {
            return;
        }

        $sTypeClass = $obFieldList->type->value;
        if (empty($sTypeClass)) {
            $arClassList = array_keys(PromoMechanismStore::instance()->getMechanismOptions());
            $sTypeClass = array_shift($arClassList);
        }

        if (!empty($sTypeClass) && class_exists($sTypeClass)) {
            $obFieldList->type->comment = $sTypeClass::getDescription();
        }
    }

    /**
     * Get discount type options (backend)
     * @return array
     */
    public function getDiscountTypeOptions() : array
    {
        return [
            self::PERCENT_TYPE => Lang::get('lovata.ordersshopaholic::lang.field.discount_type_'.self::PERCENT_TYPE),
            self::FIXED_TYPE   => Lang::get('lovata.ordersshopaholic::lang.field.discount_type_'.self::FIXED_TYPE),
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
     * Get element with auto_add flag = true
     * @param PromoMechanism $obQuery
     * @return PromoMechanism
     */
    public function scopeGetAutoAdd($obQuery)
    {
        return $obQuery->where('auto_add', true);
    }

    /**
     * Get list with shipping types
     * @return array
     */
    public function getShippingTypeIdOptions()
    {
        $arResult = (array) ShippingType::orderBy('sort_order', 'asc')->lists('name', 'id');

        return $arResult;
    }

    /**
     * Get list with payment methods
     * @return array
     */
    public function getPaymentMethodIdOptions()
    {
        $arResult = (array) PaymentMethod::orderBy('sort_order', 'asc')->lists('name', 'id');

        return $arResult;
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
        if ($this->discount_type == self::PERCENT_TYPE) {
            return Lang::get('lovata.ordersshopaholic::lang.field.discount_type_'.self::PERCENT_TYPE);
        }

        return Lang::get('lovata.ordersshopaholic::lang.field.discount_type_'.self::FIXED_TYPE);
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
     * Set discount value attribute
     * @param string $sValue
     */
    protected function setDiscountPriceAttribute($sValue)
    {
        $sValue = PriceHelper::toFloat($sValue);

        $this->attributes['discount_value'] = $sValue;
    }
}
