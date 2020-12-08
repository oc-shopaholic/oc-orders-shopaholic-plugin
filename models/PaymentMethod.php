<?php namespace Lovata\OrdersShopaholic\Models;

use Model;
use October\Rain\Database\Traits\Sortable;
use October\Rain\Database\Traits\Validation;
use October\Rain\Database\Traits\Encryptable;

use Kharanenka\Scope\ActiveField;
use Kharanenka\Scope\CodeField;

use Lovata\Toolbox\Traits\Helpers\TraitCached;
use Lovata\OrdersShopaholic\Interfaces\PaymentGatewayInterface;

/**
 * Class PaymentMethod
 * @package Lovata\OrdersShopaholic\Models
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 *
 * @mixin \October\Rain\Database\Builder
 * @mixin \Eloquent
 *
 * @property                                                                $id
 * @property bool                                                           $active
 * @property string                                                         $code
 * @property string                                                         $name
 * @property string                                                         $preview_text
 * @property int                                                            $sort_order
 * @property \October\Rain\Argon\Argon                                      $created_at
 * @property \October\Rain\Argon\Argon                                      $updated_at
 *
 * @property string                                                         $gateway_id
 * @property string                                                         $gateway_currency
 * @property array                                                          $gateway_property
 * @property int                                                            $before_status_id
 * @property int                                                            $after_status_id
 * @property int                                                            $cancel_status_id
 * @property int                                                            $fail_status_id
 * @property bool                                                           $send_purchase_request
 * @property bool                                                           $restore_cart
 *
 * @property \October\Rain\Database\Collection|Order[]                      $order
 * @method static Order|\October\Rain\Database\Relations\HasMany order()
 *
 * @property Status                                                         $before_status
 * @property Status                                                         $after_status
 * @property Status                                                         $cancel_status
 * @property Status                                                         $fail_status
 *
 * @method static Status|\October\Rain\Database\Relations\BelongsTo before_status()
 * @method static Status|\October\Rain\Database\Relations\BelongsTo after_status()
 * @method static Status|\October\Rain\Database\Relations\BelongsTo cancel_status()
 * @method static Status|\October\Rain\Database\Relations\BelongsTo fail_status()
 *
 * @property \Lovata\OrdersShopaholic\Interfaces\PaymentGatewayInterface    $gateway
 *
 * @property \October\Rain\Database\Collection|ShippingRestriction[]        $payment_restriction
 * @method static ShippingRestriction|\October\Rain\Database\Relations\BelongsToMany payment_restriction()
 */
class PaymentMethod extends Model
{
    use ActiveField;
    use CodeField;
    use Validation;
    use Sortable;
    use TraitCached;
    use Encryptable;

    const EVENT_GET_GATEWAY_LIST = 'shopaholic.payment_method.get.gateway.list';

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
        'name'             => 'lovata.toolbox::lang.field.name',
        'code'             => 'lovata.toolbox::lang.field.code',
        'gateway_currency' => 'lovata.ordersshopaholic::lang.field.gateway_currency',
    ];

    public $belongsTo = [
        'before_status' => [Status::class, 'order' => 'sort_order asc'],
        'after_status'  => [Status::class, 'order' => 'sort_order asc'],
        'cancel_status' => [Status::class, 'order' => 'sort_order asc'],
        'fail_status'   => [Status::class, 'order' => 'sort_order asc'],
    ];

    public $jsonable = [];
    public $encryptable = ['gateway_property'];
    public $hidden = ['gateway_property'];
    public $fillable = [
        'active',
        'code',
        'name',
        'sort_order',
        'preview_text',
        'gateway_id',
        'gateway_currency',
        'gateway_property',
        'before_status_id',
        'after_status_id',
        'cancel_status_id',
        'fail_status_id',
    ];

    public $cached = [
        'id',
        'name',
        'code',
        'preview_text',
    ];

    public $dates = ['created_at', 'updated_at'];

    public $hasMany = ['order' => Order::class];

    public $belongsToMany = [
        'payment_restriction' => [
            PaymentRestriction::class,
            'table' => 'lovata_ordersshopaholic_payment_restrictions_link',
        ],
    ];

    protected $arGatewayClassList = [];

    /**
     * @return null|\Lovata\OrdersShopaholic\Interfaces\PaymentGatewayInterface
     */
    public function getGatewayAttribute()
    {
        //Get gateway properties
        if (empty($this->gateway_id) || !isset($this->arGatewayClassList[$this->gateway_id])) {
            return null;
        }

        $sGatewayClass = $this->arGatewayClassList[$this->gateway_id];
        if (!class_exists($sGatewayClass)) {
            return null;
        }

        $obGatewayClass = new $sGatewayClass();
        if (!$obGatewayClass instanceof PaymentGatewayInterface) {
            return null;
        }

        return $obGatewayClass;
    }

    /**
     * Add gateway class
     * @param string $sCode
     * @param string $sClassName
     */
    public function addGatewayClass($sCode, $sClassName)
    {
        if (empty($sCode) || empty($sClassName) || !class_exists($sClassName) || isset($this->arGatewayClassList[$sCode])) {
            return;
        }

        $this->arGatewayClassList[$sCode] = $sClassName;
    }

    /**
     * Get order property value
     * @param string $sField
     * @return mixed
     */
    public function getProperty($sField)
    {
        $arPropertyList = $this->gateway_property;
        if (empty($arPropertyList) || empty($sField)) {
            return null;
        }

        return array_get($arPropertyList, $sField);
    }

    /**
     * Find element by code and return element object
     * @param string $sCode
     * @return $this
     */
    public static function getFirstByCode($sCode)
    {
        if (empty($sCode)) {
            return null;
        }

        $obStatus = self::getByCode($sCode)->first();

        return $obStatus;
    }
}
