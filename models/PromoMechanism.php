<?php namespace Lovata\OrdersShopaholic\Models;

use Lang;
use Model;
use October\Rain\Database\Traits\Validation;

use Kharanenka\Scope\NameField;
use Lovata\Toolbox\Traits\Helpers\PriceHelperTrait;

use Lovata\OrdersShopaholic\Classes\PromoMechanism\PromoMechanismStore;

/**
 * Class PromoMechanism
 * @package Lovata\Shopaholic\Models
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 *
 * @mixin \October\Rain\Database\Builder
 * @mixin \Eloquent
 *
 * @property                                           $id
 * @property string                                    $name
 * @property string                                    $type
 * @property int                                       $priority
 * @property float                                     $discount_value
 * @property float                                     $max_discount
 * @property string                                    $discount_type
 * @property bool                                      $final_discount
 * @property array                                     $property
 * @property \October\Rain\Argon\Argon                 $created_at
 * @property \October\Rain\Argon\Argon                 $updated_at
 */
class PromoMechanism extends Model
{
    use Validation;
    use NameField;
    use PriceHelperTrait;

    const FIXED_TYPE = 'fixed';
    const PERCENT_TYPE = 'percent';

    public $table = 'lovata_orders_shopaholic_promo_mechanism';

    /** Validation */
    public $rules = [
        'name' => 'required',
        'type' => 'required',
    ];

    public $attributeNames = [
        'name' => 'lovata.toolbox::lang.field.name',
        'type' => 'lovata.toolbox::lang.field.type',
    ];

    public $casts = [
        'priority' => 'integer',
    ];

    public $fillable = [
        'name',
        'type',
        'priority',
        'discount_value',
        'max_discount',
        'discount_type',
        'final_discount',
        'property',
    ];

    public $jsonable = ['property'];

    public $dates = ['created_at', 'updated_at'];

    public $arPriceField = ['discount_value', 'max_discount'];

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

        return new $sClassName();
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
}
