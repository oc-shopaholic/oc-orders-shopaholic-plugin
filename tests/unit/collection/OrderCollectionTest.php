<?php namespace Lovata\OrdersShopaholic\Tests\Unit\Collection;

use Lovata\Toolbox\Tests\CommonTest;

use Lovata\Buddies\Models\User;
use Lovata\Buddies\Facades\AuthHelper;

use Lovata\Shopaholic\Models\Offer;
use Lovata\Shopaholic\Models\Product;

use Lovata\OrdersShopaholic\Models\Order;
use Lovata\OrdersShopaholic\Components\MakeOrder;
use Lovata\OrdersShopaholic\Classes\Item\OrderItem;
use Lovata\OrdersShopaholic\Classes\Processor\CartProcessor;
use Lovata\OrdersShopaholic\Classes\Collection\OrderCollection;
use Lovata\OrdersShopaholic\Classes\Processor\OfferCartPositionProcessor;

/**
 * Class OrderCollectionTest
 * @package Lovata\OrdersShopaholic\Tests\Unit\Collection
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 *
 * @mixin \PHPUnit\Framework\Assert
 */
class OrderCollectionTest extends CommonTest
{
    /** @var  Order */
    protected $obElement;

    /** @var  Product */
    protected $obProduct;

    /** @var  Offer */
    protected $obOffer;

    /** @var  User */
    protected $obUser;

    protected $arProductData = [
        'name'         => 'name',
        'slug'         => 'slug',
        'code'         => 'code',
        'preview_text' => 'preview_text',
        'description'  => 'description',
    ];

    protected $arOfferData = [
        'active'       => true,
        'name'         => 'name',
        'code'         => 'code',
        'preview_text' => 'preview_text',
        'description'  => 'description',
        'price'        => '10,55',
        'old_price'    => '11,50',
        'quantity'     => 5,
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

    public function setUp()
    {
        parent::setUp();
        $this->runPluginRefreshCommand('lovata.popularityshopaholic', false);
    }

    /**
     * Check item collection
     */
    public function testCollectionItem()
    {
        $this->createTestData();
        if (empty($this->obElement)) {
            return;
        }

        //Check item collection
        $obCollection = OrderCollection::make([$this->obElement->id]);

        /** @var OrderItem $obItem */
        $obItem = $obCollection->first();
        self::assertInstanceOf(OrderItem::class, $obItem);
        self::assertEquals($this->obElement->id, $obItem->id);
    }

    /**
     * Test "user" method
     */
    public function testUserMethod()
    {
        $this->createTestData();
        if (empty($this->obElement)) {
            return;
        }

        //Check "user" method
        $obCollection  = OrderCollection::make()->user($this->obUser->id);

        /** @var OrderItem $obItem */
        $obItem = $obCollection->first();
        self::assertEquals($this->obElement->id, $obItem->id);

        //Change user_id field value
        $this->obElement->user_id = $this->obUser->id + 1;
        $this->obElement->save();

        //Check "user" method
        $obCollection  = OrderCollection::make()->user($this->obUser->id);

        self::assertEquals(true, $obCollection->isEmpty());

        //Change user_id field value
        $this->obElement->user_id = $this->obUser->id;
        $this->obElement->save();

        //Check "user" method
        $obCollection  = OrderCollection::make()->user($this->obUser->id);

        /** @var OrderItem $obItem */
        $obItem = $obCollection->first();
        self::assertEquals($this->obElement->id, $obItem->id);

        $this->obElement->delete();

        //Check "user" method
        $obCollection  = OrderCollection::make()->user($this->obUser->id);

        self::assertEquals(true, $obCollection->isEmpty());
    }

    /**
     * Test "status" method
     */
    public function testStatusMethod()
    {
        $this->createTestData();
        if (empty($this->obElement)) {
            return;
        }

        //Check "status" method
        $obCollection = OrderCollection::make()->status(1);
        $obUserCollection = OrderCollection::make()->status(1, $this->obUser->id);

        /** @var OrderItem $obItem */
        $obItem = $obCollection->first();
        self::assertEquals($this->obElement->id, $obItem->id);
        
        $obItem = $obUserCollection->first();
        self::assertEquals($this->obElement->id, $obItem->id);

        //Change status_id field value
        $this->obElement->status_id = 2;
        $this->obElement->save();

        //Check "status" method
        $obCollection = OrderCollection::make()->status(1);
        $obUserCollection = OrderCollection::make()->status(1, $this->obUser->id);

        self::assertEquals(true, $obCollection->isEmpty());
        self::assertEquals(true, $obUserCollection->isEmpty());

        //Change status_id field value
        $this->obElement->status_id = 1;
        $this->obElement->save();

        //Check "status" method
        $obCollection = OrderCollection::make()->status(1);
        $obUserCollection = OrderCollection::make()->status(1, $this->obUser->id);

        $obItem = $obCollection->first();
        self::assertEquals($this->obElement->id, $obItem->id);

        $obItem = $obUserCollection->first();
        self::assertEquals($this->obElement->id, $obItem->id);

        $this->obElement->delete();

        //Check "status" method
        $obCollection = OrderCollection::make()->status(1);
        $obUserCollection = OrderCollection::make()->status(1, $this->obUser->id);

        self::assertEquals(true, $obCollection->isEmpty());
        self::assertEquals(true, $obUserCollection->isEmpty());
    }

    /**
     * Test "shippingType" method
     */
    public function testShippingTypeMethod()
    {
        $this->createTestData();
        if (empty($this->obElement)) {
            return;
        }

        //Check "shippingType" method
        $obCollection = OrderCollection::make()->shippingType(1);
        $obUserCollection = OrderCollection::make()->shippingType(1, $this->obUser->id);

        /** @var OrderItem $obItem */
        $obItem = $obCollection->first();
        self::assertEquals($this->obElement->id, $obItem->id);

        $obItem = $obUserCollection->first();
        self::assertEquals($this->obElement->id, $obItem->id);

        //Change shipping_type_id field value
        $this->obElement->shipping_type_id = 2;
        $this->obElement->save();

        //Check "shippingType" method
        $obCollection = OrderCollection::make()->shippingType(1);
        $obUserCollection = OrderCollection::make()->shippingType(1, $this->obUser->id);

        self::assertEquals(true, $obCollection->isEmpty());
        self::assertEquals(true, $obUserCollection->isEmpty());

        //Change shipping_type_id field value
        $this->obElement->shipping_type_id = 1;
        $this->obElement->save();

        //Check "shippingType" method
        $obCollection = OrderCollection::make()->shippingType(1);
        $obUserCollection = OrderCollection::make()->shippingType(1, $this->obUser->id);

        $obItem = $obCollection->first();
        self::assertEquals($this->obElement->id, $obItem->id);

        $obItem = $obUserCollection->first();
        self::assertEquals($this->obElement->id, $obItem->id);

        $this->obElement->delete();

        //Check "shippingType" method
        $obCollection = OrderCollection::make()->shippingType(1);
        $obUserCollection = OrderCollection::make()->shippingType(1, $this->obUser->id);

        self::assertEquals(true, $obCollection->isEmpty());
        self::assertEquals(true, $obUserCollection->isEmpty());
    }

    /**
     * Test "paymentMethod" method
     */
    public function testPaymentMethodMethod()
    {
        $this->createTestData();
        if (empty($this->obElement)) {
            return;
        }

        //Check "paymentMethod" method
        $obCollection = OrderCollection::make()->paymentMethod(1);
        $obUserCollection = OrderCollection::make()->paymentMethod(1, $this->obUser->id);

        /** @var OrderItem $obItem */
        $obItem = $obCollection->first();
        self::assertEquals($this->obElement->id, $obItem->id);

        $obItem = $obUserCollection->first();
        self::assertEquals($this->obElement->id, $obItem->id);

        //Change payment_method_id field value
        $this->obElement->payment_method_id = 2;
        $this->obElement->save();

        //Check "paymentMethod" method
        $obCollection = OrderCollection::make()->paymentMethod(1);
        $obUserCollection = OrderCollection::make()->paymentMethod(1, $this->obUser->id);

        self::assertEquals(true, $obCollection->isEmpty());
        self::assertEquals(true, $obUserCollection->isEmpty());

        //Change payment_method_id field value
        $this->obElement->payment_method_id = 1;
        $this->obElement->save();

        //Check "paymentMethod" method
        $obCollection = OrderCollection::make()->paymentMethod(1);
        $obUserCollection = OrderCollection::make()->paymentMethod(1, $this->obUser->id);

        $obItem = $obCollection->first();
        self::assertEquals($this->obElement->id, $obItem->id);

        $obItem = $obUserCollection->first();
        self::assertEquals($this->obElement->id, $obItem->id);

        $this->obElement->delete();

        //Check "paymentMethod" method
        $obCollection = OrderCollection::make()->paymentMethod(1);
        $obUserCollection = OrderCollection::make()->paymentMethod(1, $this->obUser->id);

        self::assertEquals(true, $obCollection->isEmpty());
        self::assertEquals(true, $obUserCollection->isEmpty());
    }

    /**
     * Create shipping type object for test
     */
    protected function createTestData()
    {
        //Create product data
        $arCreateData = $this->arProductData;
        $arCreateData['active'] = true;
        $this->obProduct = Product::create($arCreateData);

        //Create offer data
        $arCreateData = $this->arOfferData;
        $arCreateData['active'] = true;
        $arCreateData['product_id'] = $this->obProduct->id;
        $this->obOffer = Offer::create($arCreateData);

        $arCreateData = $this->arUserData;

        $this->obUser = User::create($arCreateData);
        $this->obUser = User::find($this->obUser->id);

        AuthHelper::login($this->obUser);

        $arOfferList = [
            [
                'offer_id' => $this->obOffer->id,
                'quantity' => 2,
            ],
        ];

        /** @var CartProcessor $obCartProcessor */
        $obCartProcessor = CartProcessor::instance();
        $obCartProcessor->add($arOfferList, OfferCartPositionProcessor::class);

        $arOrderData = [
            'payment_method_id' => 1,
            'shipping_type_id'  => 1,
            'shipping_price'    => '1,1'
        ];

        $obComponent = new MakeOrder();
        $obComponent->init();
        $obComponent->create($arOrderData, null);

        /** @var Order $obOrder */
        $this->obElement = Order::orderBy('id', 'desc')->first();
    }
}
