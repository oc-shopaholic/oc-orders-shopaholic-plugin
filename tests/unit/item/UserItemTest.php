<?php namespace Lovata\OrdersShopaholic\Tests\Unit\Item;

use Lovata\Buddies\Models\User;
use Lovata\Toolbox\Tests\CommonTest;

use Lovata\Buddies\Classes\Item\UserItem;
use Lovata\OrdersShopaholic\Models\Order;
use Lovata\OrdersShopaholic\Classes\Item\OrderItem;
use Lovata\OrdersShopaholic\Classes\Collection\OrderCollection;

/**
 * Class UserItemTest
 * @package Lovata\OrdersShopaholic\Tests\Unit\Item
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 *
 * @mixin \PHPUnit\Framework\Assert
 */
class UserItemTest extends CommonTest
{
    /** @var  Order */
    protected $obElement;

    /** @var User */
    protected $obUser;

    protected $arOrderData = [
        'user_id'         => 1,
    ];

    protected $arUserData = [
        'email'                 => 'email@email.com',
        'name'                  => 'name',
        'last_name'             => 'last_name',
        'middle_name'           => 'middle_name',
        'phone_list'            => ['123', '321'],
        'property'              => ['birthday' => '2017-10-21'],
        'password'              => 'test',
        'password_confirmation' => 'test',
    ];

    /**
     * Check item fields
     */
    public function testItemFields()
    {
        $this->createTestData();
        if (empty($this->obElement)) {
            return;
        }

        //Check item fields
        $obItem = UserItem::make($this->obUser->id);
        $obOrderList = $obItem->order;

        self::assertInstanceOf(OrderCollection::class, $obOrderList);

        /** @var OrderItem $obOrderItem */
        $obOrderItem = $obOrderList->first();
        self::assertEquals($this->obElement->id, $obOrderItem->id);
    }

    /**
     * Check update cache item data, after update model data
     */
    public function testItemClearCache()
    {
        $this->createTestData();
        if (empty($this->obElement)) {
            return;
        }

        $this->obElement->user_id = $this->obUser->id + 1;
        $this->obElement->save();

        $obItem = UserItem::make($this->obUser->id);
        $obOrderList = $obItem->order;

        self::assertEquals(true, $obOrderList->isEmpty());

        $this->obElement->user_id = $this->obUser->id;
        $this->obElement->save();

        UserItem::clearCache($this->obUser->id);
        $obItem = UserItem::make($this->obUser->id);
        $obItem->setAttribute('order', null);

        $obOrderList = $obItem->order;

        /** @var OrderItem $obOrderItem */
        $obOrderItem = $obOrderList->first();
        self::assertEquals($this->obElement->id, $obOrderItem->id);
    }

    /**
     * Check update cache item data, after remove element
     */
    public function testRemoveElement()
    {
        $this->createTestData();
        if (empty($this->obElement)) {
            return;
        }

        $obItem = UserItem::make($this->obUser->id);
        $obOrderList = $obItem->order;

        /** @var OrderItem $obOrderItem */
        $obOrderItem = $obOrderList->first();
        self::assertEquals($this->obElement->id, $obOrderItem->id);

        //Remove element
        $this->obElement->delete();

        UserItem::clearCache($this->obUser->id);
        $obItem = UserItem::make($this->obUser->id);
        $obItem->setAttribute('order', null);
        $obOrderList = $obItem->order;

        self::assertEquals(true, $obOrderList->isEmpty());
    }

    /**
     * Create shipping type object for test
     */
    protected function createTestData()
    {
        $arCreateData = $this->arUserData;

        $this->obUser = User::create($arCreateData);
        $this->obUser = User::find($this->obUser->id);

        $arCreateData = $this->arUserData;
        $arCreateData['user_id'] = $this->obUser->id;
        $this->obElement= Order::create($arCreateData);
    }
}
