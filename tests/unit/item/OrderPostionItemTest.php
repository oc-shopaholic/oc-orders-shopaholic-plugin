<?php namespace Lovata\OrdersShopaholic\Tests\Unit\Item;

use Lovata\Toolbox\Models\Settings;
use Lovata\Toolbox\Tests\CommonTest;

use Lovata\Shopaholic\Models\Offer;
use Lovata\Shopaholic\Classes\Item\OfferItem;

use Lovata\OrdersShopaholic\Models\Order;
use Lovata\OrdersShopaholic\Models\OrderPosition;
use Lovata\OrdersShopaholic\Classes\Item\OrderItem;
use Lovata\OrdersShopaholic\Classes\Item\OrderPositionItem;

/**
 * Class OrderPositionItemTest
 * @package Lovata\OrdersShopaholic\Tests\Unit\Item
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 *
 * @mixin \PHPUnit\Framework\Assert
 */
class OrderPositionItemTest extends CommonTest
{
    /** @var  OrderPosition */
    protected $obElement;

    /** @var  Order */
    protected $obOrder;

    /** @var  Offer */
    protected $obOffer;

    protected $arCreateData = [
        'quantity' => 2,
        'property'          => [
            'comment' => 'test'
        ],
    ];

    protected $arOrderData = [
        'status_id'         => 1,
        'shipping_type_id'  => 1,
        'payment_method_id' => 1,
        'shipping_price'    => '10,05',
        'property'          => [
            'comment' => 'test'
        ],
    ];

    protected $arOfferData = [
        'active'       => true,
        'product_id'   => 1,
        'name'         => 'name',
        'code'         => 'code',
        'preview_text' => 'preview_text',
        'description'  => 'description',
        'price'        => '10,55',
        'old_price'    => '11,50',
        'quantity'     => 5,
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

        $arCreatedData = $this->arCreateData;
        $arCreatedData['id'] = $this->obElement->id;
        $arCreatedData['item_id'] = $this->obOffer->id;
        $arCreatedData['price'] = $this->obOffer->price;
        $arCreatedData['price_value'] = $this->obOffer->price_value;
        $arCreatedData['old_price'] = $this->obOffer->old_price;
        $arCreatedData['old_price_value'] = $this->obOffer->old_price_value;
        $arCreatedData['total_price'] = '21.10';
        $arCreatedData['total_price_value'] = 21.1;
        $arCreatedData['item_type'] = Offer::class;
        $arCreatedData['order_id'] = $this->obOrder->id;

        //Check item fields
        $obItem = OrderPositionItem::make($this->obElement->id);
        foreach ($arCreatedData as $sField => $sValue) {
            self::assertEquals($sValue, $obItem->$sField);
        }

        $obOrderItem = $obItem->order;

        self::assertInstanceOf(OrderItem::class, $obOrderItem);
        self::assertEquals($this->obOrder->id, $obOrderItem->id);

        $obOfferItem = $obItem->offer;
        $obPositionItem = $obItem->item;

        self::assertInstanceOf(OfferItem::class, $obOfferItem);
        self::assertInstanceOf(OfferItem::class, $obPositionItem);
        self::assertEquals($this->obOffer->id, $obOfferItem->id);
        self::assertEquals($this->obOffer->id, $obPositionItem->id);
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

        $obItem = OrderPositionItem::make($this->obElement->id);
        self::assertEquals(2, $obItem->quantity);

        //Check cache update
        $this->obElement->quantity = 3;
        $this->obElement->save();

        $obItem = OrderPositionItem::make($this->obElement->id);
        self::assertEquals(3, $obItem->quantity);
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

        $obItem = OrderPositionItem::make($this->obElement->id);
        self::assertEquals(false, $obItem->isEmpty());

        //Remove element
        $this->obElement->delete();

        $obItem = OrderPositionItem::make($this->obElement->id);
        self::assertEquals(true, $obItem->isEmpty());
    }

    /**
     * Create shipping type object for test
     */
    protected function createTestData()
    {
        Settings::set('decimals', 2);

        $this->obOffer = Offer::create($this->arOfferData);
        $this->obOrder = Order::create($this->arOrderData);

        //Create new element data
        $arCreateData = $this->arCreateData;
        $arCreateData['offer_id'] = $this->obOffer->id;
        $arCreateData['order_id'] = $this->obOrder->id;

        $this->obElement = OrderPosition::create($arCreateData);
    }
}
