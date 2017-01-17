<?php namespace Lovata\OrdersShopaholic\Models;

use Carbon\Carbon;
use Event;
use Kharanenka\Scope\UserBelongsTo;
use Lovata\Buddies\Models\User;
use Lovata\Shopaholic\Classes\CPrice;
use Lovata\Shopaholic\Models\Offer;
use Model;
use October\Rain\Database\Builder;
use October\Rain\Database\Collection;
use October\Rain\Database\Relations\BelongsToMany;

/**
 * Class Order
 * @package Lovata\Shopaholic\Models
 * @author Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 *
 * @mixin Builder
 * @mixin \Eloquent
 * 
 * @property int $id
 * @property int $user_id
 * @property int $status_id
 * @property int $payment_method_id
 * @property int $shipping_type_id
 * 
 * User data
 * @property string $name
 * @property string $last_name
 * @property string $email
 * 
 * Billing address data
 * @property string $billing_country
 * @property string $billing_state
 * @property string $billing_city
 * @property string $billing_street
 * @property string $billing_zip
 * @property string $billing_text_address
 * 
 * Shipping address
 * @property string $shipping_country
 * @property string $shipping_state
 * @property string $shipping_city
 * @property string $shipping_street
 * @property string $shipping_zip
 * @property string $shipping_text_address
 * 
 * Order data
 * @property string $order_number
 * @property string $user_comment
 * @property string $order_comment
 * @property float $shipping_price
 * @property float $total_price
 * 
 * @property $offers_total_price
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property int $manager_id
 * 
 * @property Collection|Offer[] $offer
 * @method static Offer|BelongsToMany offer()
 * 
 * @property Status $status
 * @property User $user
 * @property ShippingType $shipping_type
 * @property PaymentMethod $payment_method
 * @property \Backend\Models\User $manager
 * 
 * @method static $this today()
 * @method static $this number($sNumber)
 */
class Order extends Model
{
    use UserBelongsTo;
    
    const CACHE_TAG_ELEMENT = 'shopaholic-order-element';
    const CACHE_TAG_LIST = 'shopaholic-order-list';
    
    public $table = 'lovata_ordersshopaholic_orders';

    protected $appends = [
        'total_price',
        'quantity',
        'shipping_price',
        'offers_total_price',
        'user_new',
        'phone_list',
    ];

    protected $casts = [
        'total_price' => 'float',
    ];

    protected $dates = ['created_at', 'updated_at'];

    protected $fillable = [
        'user_id',
        'status_id',
        'shipping_type_id',
        'payment_method_id',
        
        'name',
        'last_name',
        'email',
        
        'billing_country',
        'billing_state',
        'billing_city',
        'billing_street',
        'billing_zip',
        'billing_text_address',
        
        'shipping_country',
        'shipping_state',
        'shipping_city',
        'shipping_street',
        'shipping_zip',
        'shipping_text_address',
        
        'user_comment',
        'shipping_price',
        'order_comment',
    ];

    public $belongsToMany = [
        'offer' => [
            'Lovata\Shopaholic\Models\Offer',
            'table' => 'lovata_ordersshopaholic_offer_order',
            'pivot' => ['price', 'old_price', 'quantity', 'code'],
            'key' => 'order_id',
            'otherKey' => 'offer_id',
            'pivotModel'=> 'Lovata\OrdersShopaholic\Models\OfferOrder',
        ]
    ];

    public $belongsTo = [
        'status' => ['Lovata\OrdersShopaholic\Models\Status', 'order' => 'sort_order asc'],
        'user' => ['Lovata\Buddies\Models\User'],
        'payment_method' => ['Lovata\OrdersShopaholic\Models\PaymentMethod', 'order' => 'sort_order asc'],
        'shipping_type' => ['Lovata\OrdersShopaholic\Models\ShippingType', 'order' => 'sort_order asc'],
        'manager' => ['Backend\Models\User'],
    ];

    /**
     * Get all today orders
     * @param \Illuminate\Database\Eloquent\Builder $obQuery
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeToday($obQuery)
    {
        return $obQuery->whereDate('created_at', '=', Carbon::today()->toDateString());
    }

    /**
     * Get orders by number
     * @param \Illuminate\Database\Eloquent\Builder $obQuery
     * @param string $sData
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeNumber($obQuery, $sData)
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
            return $this->attributes['total_price'];
        }
        
        return 0;
    }
    
    /**
     * @param  string  $iPrice
     * @return string
     */
    public function getTotalPriceAttribute($iPrice)
    {
        return CPrice::getPriceInFormat($iPrice);
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
     * @param  string  $iPrice
     * @return string
     */
    public function getOffersTotalPriceAttribute($iPrice)
    {
        return CPrice::getPriceInFormat($this->getOffersTotalPriceValue());
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
     * @param  string  $iPrice
     * @return string
     */
    public function getShippingPriceAttribute($iPrice)
    {
        return CPrice::getPriceInFormat($iPrice);
    }
    
    /**
     * @param  string  $sPrice
     */
    public function setShippingPriceAttribute($sPrice)
    {
        $sPrice = str_replace(',', '.', $sPrice);
        $sPrice = preg_replace("/[^0-9\.]/", "",$sPrice);
        $this->attributes['shipping_price'] = $sPrice;
    }
    
    /**
     * Get product count
     * @return int
     */
    public function getQuantityAttribute()
    {
        return $this->offer->count();
    }


    public function getUserNameSearchAttribute() {}
    public function setUserNameSearchAttribute() {}

    public function getUserSearchAttribute() {}
    public function setUserSearchAttribute($sValue) {

        if(!empty($sValue) && $sValue != $this->user_id) {
            $this->user_id = $sValue;
        }
    }

    /**
     * Get flag 'create new user'
     * @return bool
     */
    public function getUserNewAttribute() {}
    public function setUserNewAttribute($sValue) {

        if($sValue) {
            //Get user by email 
            $obUser = User::email($this->email)->first();
            if(!empty($obUser)) {
                $this->user_id = $obUser->id;
            }
        }
    }

    public function beforeSave()
    {
        // if there is no saved order number create the new one
        if(empty($this->order_number)) {
            
            $bAvailableNumber = false;
            $iTodayOrdersCount = $this->today()->count() + 1;
            
            do {
                while(strlen($iTodayOrdersCount) < 4) {
                    $iTodayOrdersCount = '0'.$iTodayOrdersCount;
                }
                
                $this->order_number = Carbon::today()->format('ymd') . '-' . $iTodayOrdersCount;
                if(empty($this->number($this->order_number)->first())){
                    $bAvailableNumber = true;
                }else{
                    $iTodayOrdersCount++;
                }
            } while (!$bAvailableNumber);
        }


        //count and set total order price
        $iOffersTotalPrice = $this->getOffersTotalPrice();
                
        $iTotalPrice = (float) $this->getShippingPriceValue() + $iOffersTotalPrice;
        $this->attributes['total_price'] = $iTotalPrice;
    }
    
    public function afterUpdate()
    {
        Event::fire('shopaholic.order.updated', $this);
    }

    public function afterDelete()
    {
        Event::fire('shopaholic.order.deleted', $this);
    }

    /**
     * Get user phone list
     * @return array
     */
    public function getPhoneListOptions() {

        $obUser = $this->user;
        if(empty($obUser)) {
            return [];
        }

        $arPhones = $obUser->phones;
        if($arPhones->isEmpty()) {
            return [];
        }

        $arResult = [];
        /** @var Phone $obPhone */
        foreach($arPhones as $obPhone) {
            $arResult[$obPhone->phone] = $obPhone->phone;
        }

        return $arResult;
    }

    /**
     * Get offers total price
     * @return float|int
     */
    public function getOffersTotalPrice() {

        $iTotalPrice = 0;

        //Get offers list
        $obOrder = $this->load(['offer']);
        $arOffers = $obOrder->offer;
        
        if($arOffers->isEmpty()) {
            return $iTotalPrice;
        }

        /** @var Offer $obOffer */
        foreach ($arOffers as $obOffer) {
            $iTotalPrice += (float) $obOffer->pivot->getPriceValue() * (int) $obOffer->pivot->quantity;
        }

        return $iTotalPrice;
    }
}