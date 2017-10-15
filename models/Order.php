<?php namespace Lovata\OrdersShopaholic\Models;

use Model;
use Carbon\Carbon;

use Kharanenka\Scope\UserBelongsTo;

use Lovata\Buddies\Models\User;
use Lovata\Shopaholic\Models\Offer;
use Lovata\Shopaholic\Classes\Helper\PriceHelper;

/**
 * Class Order
 * @package Lovata\Shopaholic\Models
 * @author Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 *
 * @mixin \October\Rain\Database\Builder
 * @mixin \Eloquent
 * 
 * @property int $id
 * @property string $order_number
 * @property int $user_id
 * @property int $status_id
 * @property int $payment_method_id
 * @property int $shipping_type_id
 * @property string $shipping_price
 * @property string $total_price
 * @property string $offers_total_price
 * @property array $property
 *
 * @property \October\Rain\Argon\Argon $created_at
 * @property \October\Rain\Argon\Argon $updated_at
 * 
 * @property \October\Rain\Database\Collection|Offer[] $offer
 * @method static Offer|\October\Rain\Database\Relations\BelongsToMany offer()
 * 
 * @property Status $status
 * @method static Status|\October\Rain\Database\Relations\BelongsTo status()
 *
 * @property User $user
 * @method static User|\October\Rain\Database\Relations\BelongsTo user()
 *
 * @property ShippingType $shipping_type
 * @method static ShippingType|\October\Rain\Database\Relations\BelongsTo shipping_type()
 *
 * @property PaymentMethod $payment_method
 * @method static PaymentMethod|\October\Rain\Database\Relations\BelongsTo payment_method()
 *
 * @method static $this getByNumber(string $sNumber)
 */
class Order extends Model
{
    use UserBelongsTo;

    public $table = 'lovata_orders_shopaholic_orders';

    protected $appends = [
        'total_price',
        'quantity',
        'shipping_price',
        'offers_total_price',
    ];

    protected $casts = [
        'property' => 'array',
    ];

    protected $dates = ['created_at', 'updated_at'];

    public $fillable = [
        'user_id',
        'status_id',
        'shipping_type_id',
        'payment_method_id',
        'shipping_price',
        'property',
    ];

    public $belongsToMany = [
        'offer' => [
            Offer::class,
            'table'      => 'lovata_orders_shopaholic_offer_order',
            'pivot'      => ['price', 'old_price', 'quantity', 'code'],
            'key'        => 'order_id',
            'otherKey'   => 'offer_id',
            'pivotModel' => OfferOrder::class,
        ]
    ];

    public $belongsTo = [
        'status'         => [Status::class, 'order' => 'sort_order asc'],
        'user'           => [User::class],
        'payment_method' => [PaymentMethod::class, 'order' => 'sort_order asc'],
        'shipping_type'  => [ShippingType::class, 'order' => 'sort_order asc'],
    ];

    /**
     * Get orders by number
     * @param Order $obQuery
     * @param string $sData
     * @return Order
     */
    public function scopeGetByNumber($obQuery, $sData)
    {
        if(!empty($sData)) {
            $obQuery->where('order_number', $sData);
        }
        
        return $obQuery;
    }

    /**
     * Get total price value
     * @return double
     */
    public function getTotalPriceValue()
    {
        if(isset($this->attributes['total_price'])) {
            return (float) $this->attributes['total_price'];
        }
        
        return 0;
    }
    
    /**
     * @param  float  $dPrice
     * @return string
     */
    public function getTotalPriceAttribute($dPrice)
    {
        /** @var PriceHelper $obPriceHelper */
        $obPriceHelper = app()->make(PriceHelper::class);
        return $obPriceHelper->get($dPrice);
    }

    /**
     * Get total price value
     * @return double
     */
    public function getOffersTotalPriceValue()
    {
        return $this->getTotalPriceValue() - $this->getShippingPriceValue();
    }
    
    /**
     * @param  float  $dPrice
     * @return string
     */
    public function getOffersTotalPriceAttribute($dPrice)
    {
        /** @var PriceHelper $obPriceHelper */
        $obPriceHelper = app()->make(PriceHelper::class);
        return $obPriceHelper->get($this->getOffersTotalPriceValue());
    }
    public function setOffersTotalPriceAttribute($iPrice) {}

    /**
     * Get shipping price value
     * @return double
     */
    public function getShippingPriceValue()
    {
        if(isset($this->attributes['shipping_price'])) {
            return $this->attributes['shipping_price'];
        }
        
        return 0;
    }

    /**
     * @param  float  $dPrice
     * @return string
     */
    public function getShippingPriceAttribute($dPrice)
    {
        /** @var PriceHelper $obPriceHelper */
        $obPriceHelper = app()->make(PriceHelper::class);
        return $obPriceHelper->get($dPrice);
    }
    
    /**
     * @param  string
     */
    public function setShippingPriceAttribute($sPrice)
    {
        $sPrice = str_replace(',', '.', $sPrice);
        $sPrice = (float) preg_replace("/[^0-9\.]/", "",$sPrice);
        $this->attributes['shipping_price'] = $sPrice;
    }
    
    /**
     * Get offer count
     * @return int
     */
    public function getQuantityAttribute()
    {
        return $this->offer->count();
    }

    /**
     * Before save model method
     */
    public function beforeSave()
    {
        //Generate new order number
        $this->generateOrderNumber();

        //count and set total order price
        $iOffersTotalPrice = $this->getOffersTotalPrice();
                
        $iTotalPrice = (float) $this->getShippingPriceValue() + $iOffersTotalPrice;
        $this->attributes['total_price'] = $iTotalPrice;
    }

    /**
     * Generate new order number
     */
    protected function generateOrderNumber()
    {
        // if there is no saved order number create the new one
        if(!empty($this->order_number)) {
            return;
        }

        $obDate = Carbon::today()->startOfDay();
        $bAvailableNumber = false;
        $iTodayOrdersCount = $this->where('created_at', '>=', $obDate->toDateTimeString())->count() + 1;

        do {
            while(strlen($iTodayOrdersCount) < 4) {
                $iTodayOrdersCount = '0'.$iTodayOrdersCount;
            }

            $this->order_number = Carbon::today()->format('ymd') . '-' . $iTodayOrdersCount;
            if(empty($this->getByNumber($this->order_number)->first())){
                $bAvailableNumber = true;
            }else{
                $iTodayOrdersCount++;
            }
        } while (!$bAvailableNumber);
    }

    /**
     * Get offers total price
     * @return float
     */
    public function getOffersTotalPrice()
    {
        $fTotalPrice = 0;

        //Get offers list
        $this->setRelations([]);
        $obOfferList = $this->offer;
        
        if($obOfferList->isEmpty()) {
            return $fTotalPrice;
        }

        /** @var Offer $obOffer */
        foreach ($obOfferList as $obOffer) {

            /** @var OfferOrder $obPivot */
            $obPivot = $obOffer->pivot;

            $fTotalPrice += $obPivot->getTotalPriceValue();
        }

        return (float) $fTotalPrice;
    }
}