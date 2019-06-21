<?php namespace Lovata\Ordersshopaholic\Models;

use Model;
use October\Rain\Database\Traits\Validation;

use Kharanenka\Scope\TypeField;
use Kharanenka\Scope\UserBelongsTo;
use Lovata\Toolbox\Traits\Helpers\TraitCached;

/**
 * Class UserAddress
 * @package Lovata\Ordersshopaholic\Models
 * @author  Sergey Zakharevich, s.zakharevich@lovata.com, LOVATA Group
 *
 * @mixin \October\Rain\Database\Builder
 * @mixin \Eloquent
 *
 * @property                                                                             $id
 * @property int                                                                         $user_id
 * @property string                                                                      $type
 * @property string                                                                      $country
 * @property string                                                                      $state
 * @property string                                                                      $city
 * @property string                                                                      $street
 * @property string                                                                      $house
 * @property string                                                                      $building
 * @property string                                                                      $flat
 * @property int                                                                         $floor
 * @property string                                                                      $address1
 * @property string                                                                      $address2
 * @property string                                                                      $postcode
 * @property \October\Rain\Argon\Argon                                                   $created_at
 * @property \October\Rain\Argon\Argon                                                   $updated_at
 * @property \Lovata\Buddies\Models\User                                                 $user
 * @method static \October\Rain\Database\Relations\BelongsTo|\Lovata\Buddies\Models\User user()
 */
class UserAddress extends Model
{
    const ADDRESS_TYPE_BILLING = 'billing';
    const ADDRESS_TYPE_SIPPING = 'shipping';

    use TraitCached;
    use UserBelongsTo;
    use TypeField;
    use Validation;

    /**
     * @var string The database table used by the model.
     */
    public $table = 'lovata_orders_shopaholic_user_addresses';

    public $rules = [
        'user_id' => 'required',
        'type'    => 'required',
    ];

    public $attributeNames = [
        'type' => 'lovata.toolbox::lang.field.type',
    ];

    public $visible = [];
    public $hidden = [
        'created_at',
        'updated_at',
        'user_id',
    ];

    /**
     * @var array Fillable fields
     */
    protected $fillable = [
        'user_id',
        'type',
        'country',
        'state',
        'city',
        'street',
        'house',
        'building',
        'flat',
        'floor',
        'address1',
        'address2',
        'postcode',
    ];

    /**
     * @var array Relations
     */
    public $hasOne = [];
    public $hasMany = [];
    public $belongsTo = [];
    public $belongsToMany = [];
    public $morphTo = [];
    public $morphOne = [];
    public $morphMany = [];
    public $attachOne = [];
    public $attachMany = [];
    public $cached = [
        'id',
        'user_id',
        'type',
        'country',
        'state',
        'city',
        'street',
        'house',
        'building',
        'flat',
        'floor',
        'address1',
        'address2',
        'postcode',
    ];

    public static $arCheckedFieldList = [
        'type',
        'country',
        'state',
        'city',
        'street',
        'house',
        'building',
        'flat',
        'floor',
        'address1',
        'address2',
        'postcode',
    ];

    /**
     * Find address by data
     * @param array  $arFindAddress
     * @param string $iUserID
     * @return bool|UserAddress|mixed|null
     */
    public static function findAddressByData($arFindAddress, $iUserID)
    {
        if (empty($arFindAddress) || !is_array($arFindAddress) || empty($iUserID)) {
            return null;
        }

        //Get user address list
        $obAddressList = UserAddress::getByUser($iUserID)->get();
        if ($obAddressList->isEmpty()) {
            return null;
        }

        //We are looking for an address for the complete coincidence of all fields
        foreach ($obAddressList as $obAddress) {
            $bCheck = true;
            foreach (self::$arCheckedFieldList as $sField) {
                if ($obAddress->$sField != array_get($arFindAddress, $sField)) {
                    $bCheck = false;
                    break;
                }
            }

            if ($bCheck) {
                return $obAddress;
            }
        }

        return null;
    }
}
