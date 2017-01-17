<?php namespace Lovata\OrdersShopaholic\Models;

use Kharanenka\Scope\UserBelongsTo;
use Model;
use Carbon\Carbon;

/**
 * Class Address
 * @package Lovata\Shopaholic\Models
 * @author Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 *
 * @property $id
 * @property int $user_id
 * @property string $type
 * @property string $country
 * @property string $state
 * @property string $city
 * @property string $street
 * @property string $zip
 * @property string $address
 * @property bool $default
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * 
 * @method static $this getByUser(int $iUserID)
 */
class Address extends Model
{
    use UserBelongsTo;
    
    public $table = 'lovata_ordersshopaholic_addresses';

    const BILLING_TYPE = 'billing';
    const SHIPPING_TYPE = 'shipping';

    protected $fillable = [
        'user_id',
        'type',
        'country',
        'state',
        'city',
        'street',
        'zip',
        'default',
        'address',
    ];

    protected $dates = ['created_at', 'updated_at'];

    /**
     * Create new user address
     * @param Order $obOrder
     * //TODO: Метод треббует рефакторинга
     */
    public static function createUserAddress($obOrder) {
        
        //Get user object
        $obUser = $obOrder->user;
        if(empty($obUser)) {
            return;
        }

        //Get user address
        $arAddress = Address::getByUser($obUser->id)->get();

        //Need create new address
        $bNeedCreateBillingAddress = !empty($obOrder->billing_country) ||
            !empty($obOrder->billing_state) ||
            !empty($obOrder->billing_city) ||
            !empty($obOrder->billing_street) ||
            !empty($obOrder->billing_zip) ||
            !empty($obOrder->billing_text_address);

        $bNeedCreateShippingAddress = !empty($obOrder->shipping_country) ||
            !empty($obOrder->shipping_state) ||
            !empty($obOrder->shipping_city) ||
            !empty($obOrder->shipping_street) ||
            !empty($obOrder->shipping_zip) ||
            !empty($obOrder->shipping_text_address);

        if(!$arAddress->isEmpty()) {
            /** @var Address $obAddress */
            foreach($arAddress as $obAddress) {
                
                //Check billing address
                if($obAddress->type == Address::BILLING_TYPE && $bNeedCreateBillingAddress) {
                    if(!empty($obOrder->billing_country) && $obOrder->billing_country != $obAddress->country) {
                        continue;
                    }

                    if(!empty($obOrder->billing_state) && $obOrder->billing_state != $obAddress->state) {
                        continue;
                    }

                    if(!empty($obOrder->billing_city) && $obOrder->billing_city != $obAddress->city) {
                        continue;
                    }

                    if(!empty($obOrder->billing_street) && $obOrder->billing_street != $obAddress->street) {
                        continue;
                    }

                    if(!empty($obOrder->billing_zip) && $obOrder->billing_zip != $obAddress->zip) {
                        continue;
                    }

                    if(!empty($obOrder->billing_text_address) && $obOrder->billing_text_address != $obAddress->address) {
                        continue;
                    }

                    $bNeedCreateBillingAddress = false;
                    break;
                //Check shipping address
                } else if($obAddress->type == Address::SHIPPING_TYPE && $bNeedCreateShippingAddress) {
                    if(!empty($obOrder->shipping_country) && $obOrder->shipping_country != $obAddress->country) {
                        continue;
                    }

                    if(!empty($obOrder->shipping_state) && $obOrder->shipping_state != $obAddress->state) {
                        continue;
                    }

                    if(!empty($obOrder->shipping_city) && $obOrder->shipping_city != $obAddress->city) {
                        continue;
                    }

                    if(!empty($obOrder->shipping_street) && $obOrder->shipping_street != $obAddress->street) {
                        continue;
                    }

                    if(!empty($obOrder->shipping_zip) && $obOrder->shipping_zip != $obAddress->zip) {
                        continue;
                    }

                    if(!empty($obOrder->shipping_text_address) && $obOrder->shipping_text_address != $obAddress->address) {
                        continue;
                    }

                    $bNeedCreateShippingAddress = false;
                    break;
                }
            }
        }

        //Create billing address
        if($bNeedCreateBillingAddress) {
            $obNewAddress = Address::create([
                'type' => Address::BILLING_TYPE,
                'user_id' => $obUser->id,
                'country' => $obOrder->billing_country,
                'state' => $obOrder->billing_state,
                'city' => $obOrder->billing_city,
                'street' => $obOrder->billing_street,
                'zip' => $obOrder->billing_zip,
                'address' => $obOrder->billing_text_address,
            ]);
        }

        //Create shipping address
        if($bNeedCreateShippingAddress) {
            $obNewAddress = Address::create([
                'type' => Address::SHIPPING_TYPE,
                'user_id' => $obUser->id,
                'country' => $obOrder->shipping_country,
                'state' => $obOrder->shipping_state,
                'city' => $obOrder->shipping_city,
                'street' => $obOrder->shipping_street,
                'zip' => $obOrder->shipping_zip,
                'address' => $obOrder->shipping_text_address,
            ]);
        }
    }
}