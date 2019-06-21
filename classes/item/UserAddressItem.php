<?php namespace Lovata\OrdersShopaholic\Classes\Item;

use Lovata\Toolbox\Classes\Item\ElementItem;

use Lovata\OrdersShopaholic\Models\UserAddress;

/**
 * Class UserAddressItem
 * @package Lovata\OrdersShopaholic\Classes\Item
 * @author  Sergey Zakharevich, s.zakharevich@lovata.com, LOVATA Group
 *
 * @property int    $id
 * @property int    $user_id
 * @property string $type
 * @property string $country
 * @property string $state
 * @property string $city
 * @property string $street
 * @property string $house
 * @property string $building
 * @property string $flat
 * @property int    $floor
 * @property string $address1
 * @property string $address2
 * @property string $postcode
 */

class UserAddressItem extends ElementItem
{
    const MODEL_CLASS = UserAddress::class;

    /** @var UserAddress */
    protected $obElement = null;
}
