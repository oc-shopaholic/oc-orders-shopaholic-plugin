<?php namespace Lovata\OrdersShopaholic\Models;

use Model;
use October\Rain\Argon\Argon;
use October\Rain\Database\Traits\Validation;
use October\Rain\Database\Traits\Encryptable;
use Backend\Models\User as BackendUser;

use Kharanenka\Scope\UserBelongsTo;

use Lovata\Toolbox\Classes\Helper\PriceHelper;
use Lovata\Toolbox\Classes\Helper\UserHelper;
use Lovata\Toolbox\Traits\Helpers\TraitCached;
use Lovata\Toolbox\Traits\Helpers\PriceHelperTrait;
use Lovata\Toolbox\Traits\Models\SetPropertyAttributeTrait;

use Lovata\Shopaholic\Models\Currency;
use Lovata\OrdersShopaholic\Classes\PromoMechanism\OrderPromoMechanismProcessor;

/**
 * Class Order
 * @package Lovata\OrdersShopaholic\Models
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 *
 * @mixin \October\Rain\Database\Builder
 * @mixin \Eloquent
 *
 * @property int                                                                         $id
 * @property string                                                                      $order_number
 * @property string                                                                      $secret_key
 * @property int                                                                         $currency_id
 * @property string                                                                      $currency_symbol
 * @property string                                                                      $currency_code
 * @property int                                                                         $user_id
 * @property int                                                                         $status_id
 * @property int                                                                         $manager_id
 * @property int                                                                         $payment_method_id
 * @property int                                                                         $shipping_type_id
 * @property double                                                                      $weight
 * @property string                                                                      $shipping_price
 * @property float                                                                       $shipping_price_value
 * @property string                                                                      $total_price
 * @property float                                                                       $total_price_value
 * @property string                                                                      $position_total_price
 * @property float                                                                       $position_total_price_value
 * @property float                                                                       $shipping_tax_percent
 * @property \Lovata\OrdersShopaholic\Classes\PromoMechanism\TotalPriceContainer         $position_total_price_data
 * @property \Lovata\OrdersShopaholic\Classes\PromoMechanism\ItemPriceContainer          $shipping_price_data
 * @property \Lovata\OrdersShopaholic\Classes\PromoMechanism\TotalPriceContainer         $total_price_data
 * @property array                                                                       $property
 *
 * @property \October\Rain\Argon\Argon                                                   $created_at
 * @property \October\Rain\Argon\Argon                                                   $updated_at
 *
 * @property string                                                                      $transaction_id
 * @property string                                                                      $payment_token
 * @property array                                                                       $payment_data
 * @property array                                                                       $payment_response
 *
 * @property \October\Rain\Database\Collection|OrderPosition[]                           $order_position
 * @method static \October\Rain\Database\Relations\HasMany|OrderPosition order_position()
 *
 * @property \October\Rain\Database\Collection|OrderPromoMechanism[]                     $order_promo_mechanism
 * @method static \October\Rain\Database\Relations\HasMany|OrderPromoMechanism order_promo_mechanism()
 *
 * @property \October\Rain\Database\Collection|Task[]                                    $task
 * @method static \October\Rain\Database\Relations\HasMany|Task task()
 * @property \October\Rain\Database\Collection|Task[]                                    $active_task
 * @method static \October\Rain\Database\Relations\HasMany|Task active_task()
 * @property \October\Rain\Database\Collection|Task[]                                    $completed_task
 * @method static \October\Rain\Database\Relations\HasMany|Task completed_task()
 *
 * @property Currency                                                                    $currency
 * @method static Currency|\October\Rain\Database\Relations\BelongsTo currency()
 *
 * @property Status                                                                      $status
 * @method static Status|\October\Rain\Database\Relations\BelongsTo status()
 *
 * @property \Lovata\Buddies\Models\User                                                 $user
 * @method static \Lovata\Buddies\Models\User|\October\Rain\Database\Relations\BelongsTo user()
 *
 * @property ShippingType                                                                $shipping_type
 * @method static ShippingType|\October\Rain\Database\Relations\BelongsTo shipping_type()
 *
 * @property PaymentMethod                                                               $payment_method
 * @method static PaymentMethod|\October\Rain\Database\Relations\BelongsTo payment_method()
 *
 * Coupons for Shopaholic
 * @property \October\Rain\Database\Collection|\Lovata\CouponsShopaholic\Models\Coupon[] $coupon
 * @method static \October\Rain\Database\Relations\BelongsToMany|\Lovata\CouponsShopaholic\Models\Coupon coupon()
 *
 * CDEK for Shopaholic
 * @property \Gabix\CdekShopaholic\Models\CdekDispatch                                   $cdek
 * @method static \October\Rain\Database\Relations\HasOne|\Gabix\CdekShopaholic\Models\CdekDispatch cdek()
 *
 * ApiShip for Shopaholic
 * @property \Gabix\ApiShipShopaholic\Models\ApiShipDispatch                             $apiship
 * @method static \October\Rain\Database\Relations\HasOne|\Gabix\ApiShipShopaholic\Models\ApiShipDispatch apiship()
 *
 * @method static $this getByNumber(string $sNumber)
 * @method static $this getByStatus(int $iStatusID)
 * @method static $this getByShippingType(int $iShippingTypeID)
 * @method static $this getByPaymentMethod(int $iPaymentMethodID)
 * @method static $this getBySecretKey(string $sNumber)
 * @method static $this getByTransactionID(string $sTransactionID)
 * @method static $this getByPaymentToken(string $sPaymentToken)
 */
class Order extends Model
{
    use UserBelongsTo;
    use TraitCached;
    use Validation;
    use PriceHelperTrait;
    use SetPropertyAttributeTrait;
    use Encryptable;

    public $table = 'lovata_orders_shopaholic_orders';

    public $rules = [];
    public $attributeNames = [];

    public $arPriceField = [
        'total_price',
        'shipping_price',
        'position_total_price',
    ];

    public $jsonable = ['property'];
    public $dates = ['created_at', 'updated_at'];
    public $encryptable = ['payment_data', 'payment_response'];
    public $hidden = ['payment_data', 'payment_response'];

    public $fillable = [
        'user_id',
        'status_id',
        'shipping_type_id',
        'payment_method_id',
        'shipping_price',
        'property',
        'currency_id',
        'shipping_tax_percent',
        'manager_id',
        'payment_data',
    ];

    public $cached = [
        'id',
        'secret_key',
        'order_number',
        'user_id',
        'status_id',
        'currency_id',
        'payment_method_id',
        'shipping_type_id',
        'shipping_tax_percent',
        'property',
        'created_at',
        'updated_at',
    ];

    public $hasMany = [
        'order_position'        => [
            OrderPosition::class,
        ],
        'order_offer'           => [
            OrderPosition::class,
            'condition' => 'item_type = \Lovata\Shopaholic\Models\Offer',
        ],
        'order_promo_mechanism' => [
            OrderPromoMechanism::class
        ],
        'task'                  => [
            Task::class
        ],
        'active_task'           => [
            Task::class,
            'scope' => 'getActiveTask',
        ],
        'completed_task'        => [
            Task::class,
            'scope' => 'getCompletedTask',
        ],
    ];
    public $belongsToMany = [];

    public $belongsTo = [
        'status'         => [Status::class, 'order' => 'sort_order asc'],
        'payment_method' => [PaymentMethod::class, 'order' => 'sort_order asc'],
        'shipping_type'  => [ShippingType::class, 'order' => 'sort_order asc'],
        'currency'       => [Currency::class, 'order' => 'sort_order asc'],
    ];

    public $attachOne = [];
    public $attachMany = [];

    /**
     * Order constructor.
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        $sUserModelClass = UserHelper::instance()->getUserModel();
        if (!empty($sUserModelClass)) {
            $this->belongsTo['user'] = [$sUserModelClass];
        }

        parent::__construct($attributes);
    }

    /**
     * Get orders by number
     * @param Order  $obQuery
     * @param string $sData
     * @return Order
     */
    public function scopeGetByNumber($obQuery, $sData)
    {
        if (!empty($sData)) {
            $obQuery->where('order_number', $sData);
        }

        return $obQuery;
    }

    /**
     * Get orders by status
     * @param Order  $obQuery
     * @param string $sData
     * @return Order
     */
    public function scopeGetByStatus($obQuery, $sData)
    {
        if (!empty($sData)) {
            $obQuery->where('status_id', $sData);
        }

        return $obQuery;
    }

    /**
     * Get orders by shipping type
     * @param Order  $obQuery
     * @param string $sData
     * @return Order
     */
    public function scopeGetByShippingType($obQuery, $sData)
    {
        if (!empty($sData)) {
            $obQuery->where('shipping_type_id', $sData);
        }

        return $obQuery;
    }

    /**
     * Get orders by payment method
     * @param Order  $obQuery
     * @param string $sData
     * @return Order
     */
    public function scopeGetByPaymentMethod($obQuery, $sData)
    {
        if (!empty($sData)) {
            $obQuery->where('payment_method_id', $sData);
        }

        return $obQuery;
    }

    /**
     * Get orders by secret_key field
     * @param Order  $obQuery
     * @param string $sData
     * @return Order
     */
    public function scopeGetBySecretKey($obQuery, $sData)
    {
        if (!empty($sData)) {
            $obQuery->where('secret_key', $sData);
        }

        return $obQuery;
    }

    /**
     * Get order by transaction ID
     * @param Order  $obQuery
     * @param string $sData
     * @return Order
     */
    public function scopeGetByTransactionID($obQuery, $sData)
    {
        if (!empty($sData)) {
            $obQuery->where('transaction_id', $sData);
        }

        return $obQuery;
    }

    /**
     * Get order by payment token
     * @param Order  $obQuery
     * @param string $sData
     * @return Order
     */
    public function scopeGetByPaymentToken($obQuery, $sData)
    {
        if (!empty($sData)) {
            $obQuery->where('payment_token', $sData);
        }

        return $obQuery;
    }

    /**
     * Get manager list (backend)
     * @return array
     */
    public function getManagerIdOptions()
    {
        $obUserList = BackendUser::where('is_superuser', false)->get();
        if ($obUserList->isEmpty()) {
            return [];
        }

        $arUserList = [];

        foreach ($obUserList as $obUser) {
            $arUserList[$obUser->id] = $obUser->first_name.' '.$obUser->last_name.' ('.$obUser->email.')';
        }

        return $arUserList;
    }

    /**
     * Get position total price data
     * @return \Lovata\OrdersShopaholic\Classes\PromoMechanism\TotalPriceContainer
     */
    public function getPositionTotalPriceDataAttribute()
    {
        $obPriceData = $this->getPromoMechanismProcessor()->getPositionTotalPrice();

        return $obPriceData;
    }

    /**
     * Get shipping price data
     * @return \Lovata\OrdersShopaholic\Classes\PromoMechanism\ItemPriceContainer
     */
    public function getShippingPriceDataAttribute()
    {
        $obPriceData = $this->getPromoMechanismProcessor()->getShippingPrice();

        return $obPriceData;
    }

    /**
     * Get total price data
     * @return \Lovata\OrdersShopaholic\Classes\PromoMechanism\TotalPriceContainer
     */
    public function getTotalPriceDataAttribute()
    {
        $obPriceData = $this->getPromoMechanismProcessor()->getTotalPrice();

        return $obPriceData;
    }

    /**
     * Get position count
     * @return int
     */
    public function getQuantityAttribute()
    {
        return $this->order_position->count();
    }

    /**
     * Get order property value
     * @param string $sField
     * @return mixed
     */
    public function getProperty($sField)
    {
        $arPropertyList = $this->property;
        if (empty($arPropertyList) || empty($sField)) {
            return null;
        }

        return array_get($arPropertyList, $sField);
    }

    /**
     * Get order status code
     * @return string|null
     */
    public function getStatusCode()
    {
        $obStatus = $this->status;
        if (empty($obStatus)) {
            return null;
        }

        return $obStatus->code;
    }

    /**
     * Get order shipping type code
     * @return string|null
     */
    public function getShippingTypeCode()
    {
        $obShippingType = $this->shipping_type;
        if (empty($obShippingType)) {
            return null;
        }

        return $obShippingType->code;
    }

    /**
     * Get order payment method code
     * @return string|null
     */
    public function getPaymentMethodCode()
    {
        $obPaymentMethod = $this->payment_method;
        if (empty($obPaymentMethod)) {
            return null;
        }

        return $obPaymentMethod->code;
    }

    /**
     * Get shipping price value from model
     * @return float
     */
    public function getShippingPriceValue()
    {
        $fPrice = $this->getAttributeFromArray('shipping_price');

        return $fPrice;
    }

    /**
     * Before save model method
     */
    public function beforeSave()
    {
        //Generate new order number
        $this->generateOrderNumber();
    }

    /**
     * Get full shipping price, without discounts
     * @return string
     */
    public function getFullShippingPriceAttribute()
    {
        return PriceHelper::format($this->getShippingPriceValue());
    }

    /**
     * Set full shipping price (used in backend)
     * @param string $sValue
     */
    public function setFullShippingPriceAttribute($sValue)
    {
        $this->shipping_price = $sValue;
    }

    /**
     * Generate secret key
     * @return string
     */
    public function generateSecretKey()
    {
        return md5($this->order_number.(string) microtime(true));
    }

    /**
     * Generate new order number
     */
    protected function generateOrderNumber()
    {
        // if there is no saved order number create the new one
        if (!empty($this->order_number)) {
            return;
        }

        $obDate = Argon::today()->startOfDay();
        $bAvailableNumber = false;
        $iTodayOrdersCount = $this->where('created_at', '>=', $obDate->toDateTimeString())->count() + 1;

        do {
            while (strlen($iTodayOrdersCount) < 4) {
                $iTodayOrdersCount = '0'.$iTodayOrdersCount;
            }

            $this->order_number = Argon::today()->format('ymd').'-'.$iTodayOrdersCount;
            if (empty($this->getByNumber($this->order_number)->first())) {
                $bAvailableNumber = true;
            } else {
                $iTodayOrdersCount++;
            }
        } while (!$bAvailableNumber);

        $this->secret_key = $this->generateSecretKey();
    }

    /**
     * Create object of OrderPromoMechanismProcessor class for Order
     */
    protected function getPromoMechanismProcessor()
    {
        return OrderPromoMechanismProcessor::get($this);
    }

    /**
     * Get position total price value
     * @return float
     */
    protected function getPositionTotalPriceValueAttribute()
    {
        $obPriceData = $this->getPromoMechanismProcessor()->getPositionTotalPrice();

        return $obPriceData->price_value;
    }

    /**
     * Get shipping price value
     * @return float
     */
    protected function getShippingPriceValueAttribute()
    {
        $obPriceData = $this->getPromoMechanismProcessor()->getShippingPrice();

        return $obPriceData->price_value;
    }

    /**
     * Get total price value
     * @return float
     */
    protected function getTotalPriceValueAttribute()
    {
        $obPriceData = $this->getPromoMechanismProcessor()->getTotalPrice();

        return $obPriceData->price_value;
    }

    /**
     * Get currency_symbol attribute value
     * @return null|string
     */
    protected function getCurrencySymbolAttribute()
    {
        //Get currency object
        $obCurrency = $this->currency;
        if (empty($obCurrency)) {
            return null;
        }

        return $obCurrency->symbol;
    }

    /**
     * Get currency_code attribute value
     * @return null|string
     */
    protected function getCurrencyCodeAttribute()
    {
        //Get currency object
        $obCurrency = $this->currency;
        if (empty($obCurrency)) {
            return null;
        }

        return $obCurrency->code;
    }

    /**
     * Set manager_id attribute value
     * @param $sValue
     */
    protected function setManagerIdAttribute($sValue)
    {
        $this->attributes['manager_id'] = (int) $sValue;
    }

    /**
     * Get total weight
     * @return int
     */
    protected function getWeightAttribute()
    {
        $obOrderPositionList = $this->order_position;
        if ($obOrderPositionList->isEmpty()) {
            return 0;
        }

        $iWeight = 0;
        foreach ($obOrderPositionList as $obOrderPosition) {
            $iWeight += (float) $obOrderPosition->weight;
        }

        return $iWeight;
    }
}