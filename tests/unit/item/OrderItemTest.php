<?php namespace Lovata\OrdersShopaholic\Tests\Unit\Item;

use Lovata\Toolbox\Models\Settings;
use Lovata\Toolbox\Tests\CommonTest;

use Lovata\Shopaholic\Models\Offer;

use Lovata\OrdersShopaholic\Models\Order;
use Lovata\OrdersShopaholic\Models\OrderPosition;
use Lovata\OrdersShopaholic\Models\ShippingType;
use Lovata\OrdersShopaholic\Models\PaymentMethod;
use Lovata\OrdersShopaholic\Classes\Item\OrderItem;
use Lovata\OrdersShopaholic\Classes\Item\OrderPositionItem;
use Lovata\OrdersShopaholic\Classes\Item\PaymentMethodItem;
use Lovata\OrdersShopaholic\Classes\Item\ShippingTypeItem;
use Lovata\OrdersShopaholic\Classes\Item\StatusItem;
use Lovata\OrdersShopaholic\Classes\Collection\OrderPositionCollection;

/**
 * Class OrderItemTest
 * @package Lovata\OrdersShopaholic\Tests\Unit\Item
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 *
 * @mixin \PHPUnit\Framework\Assert
 */
class OrderItemTest extends CommonTest
{
    /** @var  Order */
    protected $obElement;

    /** @var  OrderPosition */
    protected $obOrderPosition;

    /** @var  Offer */
    protected $obOffer;

    /** @var PaymentMethod */
    protected $obPaymentMethod;

    /** @var ShippingType */
    protected $obShippingType;

    protected $arOrderPositionData = [
        'quantity' => 2,
        'property'          => [
            'comment' => 'test'
        ],
    ];

    protected $arOrderData = [
        'status_id'         => 1,
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

    protected $arPaymentData = [
        'name'         => 'name',
        'code'         => 'code',
        'preview_text' => 'preview_text',
    ];

    protected $arShippingType = [
        'name'         => 'name',
        'code'         => 'code',
        'preview_text' => 'preview_text',
        'price'        => '5,5',
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

        $arCreatedData = $this->arOrderData;
        $arCreatedData['id'] = $this->obElement->id;
        $arCreatedData['status_id'] = 1;
        $arCreatedData['secret_key'] = 'test';
        $arCreatedData['payment_method_id'] = $this->obPaymentMethod->id;
        $arCreatedData['shipping_type_id'] = $this->obShippingType->id;
        $arCreatedData['shipping_price'] = $this->obShippingType->price;
        $arCreatedData['shipping_price_value'] = $this->obShippingType->price_value;
        $arCreatedData['total_price'] = '26.60';
        $arCreatedData['total_price_value'] = 26.6;
        $arCreatedData['position_total_price'] = '21.10';
        $arCreatedData['position_total_price_value'] = 21.1;
        $arCreatedData['order_position_id'] = [$this->obOrderPosition->id];

        //Check item fields
        $obItem = OrderItem::make($this->obElement->id);
        foreach ($arCreatedData as $sField => $sValue) {
            self::assertEquals($sValue, $obItem->$sField);
        }

        $obStatusItem = $obItem->status;

        self::assertInstanceOf(StatusItem::class, $obStatusItem);
        self::assertEquals(1, $obStatusItem->id);

        $obPaymentMethodItemItem = $obItem->payment_method;

        self::assertInstanceOf(PaymentMethodItem::class, $obPaymentMethodItemItem);
        self::assertEquals($this->obPaymentMethod->id, $obPaymentMethodItemItem->id);

        $obShippingTypeItem = $obItem->shipping_type;

        self::assertInstanceOf(ShippingTypeItem::class, $obShippingTypeItem);
        self::assertEquals($this->obShippingType->id, $obShippingTypeItem->id);

        $obOrderPositionList = $obItem->order_position;

        self::assertInstanceOf(OrderPositionCollection::class, $obOrderPositionList);

        /** @var OrderPositionItem $obOrderPositionItem */
        $obOrderPositionItem = $obOrderPositionList->first();

        self::assertInstanceOf(OrderPositionItem::class, $obOrderPositionItem);
        self::assertEquals($this->obOrderPosition->id, $obOrderPositionItem->id);
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

        $obItem = OrderItem::make($this->obElement->id);
        self::assertEquals(1, $obItem->status_id);

        //Check cache update
        $this->obElement->status_id = 2;
        $this->obElement->save();

        $obItem = OrderItem::make($this->obElement->id);
        self::assertEquals(2, $obItem->status_id);
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

        $obItem = OrderItem::make($this->obElement->id);
        self::assertEquals(false, $obItem->isEmpty());

        //Remove element
        $this->obElement->delete();

        $obItem = OrderItem::make($this->obElement->id);
        self::assertEquals(true, $obItem->isEmpty());
    }

    /**
     * Create shipping type object for test
     */
    protected function createTestData()
    {
        Settings::set('decimals', 2);

        $this->obOffer = Offer::create($this->arOfferData);

        $this->obPaymentMethod = PaymentMethod::create($this->arPaymentData);
        $this->obShippingType = ShippingType::create($this->arShippingType);

        $this->arOrderData['payment_method_id'] = $this->obPaymentMethod->id;
        $this->arOrderData['shipping_type_id'] = $this->obShippingType->id;
        $this->arOrderData['shipping_price'] = $this->obShippingType->price;

        $this->obElement= Order::create($this->arOrderData);
        $this->obElement->secret_key = 'test';
        $this->obElement->save();

        //Create new element data
        $arCreateData = $this->arOrderPositionData;
        $arCreateData['offer_id'] = $this->obOffer->id;
        $arCreateData['order_id'] = $this->obElement->id;

        $this->obOrderPosition = OrderPosition::create($arCreateData);
    }
}
