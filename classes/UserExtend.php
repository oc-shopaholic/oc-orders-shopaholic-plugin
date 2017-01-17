<?php namespace Lovata\OrdersShopaholic\Classes;

use Lovata\Buddies\Models\User;
use Lovata\OrdersShopaholic\Models\Address;
use Lovata\OrdersShopaholic\Models\Order;
use Lovata\OrdersShopaholic\Models\Phone;
use October\Rain\Database\Collection;
use October\Rain\Database\Relations\HasMany;

/**
 * Class UserExtend
 * @package Lovata\OrdersShopaholic\Classes
 * @author Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 * 
 * @property Collection|Phone[] $order_phones
 * @property Collection|Address[] $order_address
 * @property Collection|Order[] $orders
 * 
 * @method static $this|HasMany order_phones()
 * @method static $this|HasMany order_address()
 * @method static $this|HasMany orders()
 */
class UserExtend
{
    /**
     * Extend "User" model constructor
     * @param User $obUser
     */
    public static function extendConstructor(&$obUser)
    {
        $obUser->hasMany['order_address'] = [
            'Lovata\OrdersShopaholic\Models\Address',
            'key' => 'user_id',
        ];

        $obUser->hasMany['order_phones'] = [
            'Lovata\OrdersShopaholic\Models\Phone',
            'key' => 'user_id',
        ];

        $obUser->hasMany['order'] = [
            'Lovata\OrdersShopaholic\Models\Order',
            'key' => 'user_id',
        ];
    }
    
    /**
     * Extend "User" model "getData" model
     * @param $arResult
     * @param User $obUser
     */
    public static function extendGetData(&$arResult, $obUser)
    {
        //
    }
    
    /**
     * Extend "User" model "getCacheData" model
     * @param $arResult
     * @param int $iElementID
     * @param User $obUser
     */
    public static function extendGetCacheData(&$arResult, $iElementID, $obUser = null)
    {
        //
    }
}