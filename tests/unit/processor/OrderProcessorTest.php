<?php namespace Lovata\OrdersShopaholic\Tests\Unit\Processor;

use Lang;
use Kharanenka\Helper\Result;
use Lovata\Buddies\Facades\AuthHelper;
use Lovata\OrdersShopaholic\Classes\Processor\OfferCartPositionProcessor;
use Lovata\OrdersShopaholic\Classes\Processor\OrderProcessor;
use Lovata\OrdersShopaholic\Models\ShippingType;
use Lovata\OrdersShopaholic\Models\Order;
use Lovata\OrdersShopaholic\Models\OrderPosition;
use Lovata\Shopaholic\Models\Settings;
use Lovata\Toolbox\Tests\CommonTest;

use Lovata\Shopaholic\Models\Offer;
use Lovata\Shopaholic\Models\Product;
use Lovata\Buddies\Models\User;
use Lovata\OrdersShopaholic\Models\Cart;
use Lovata\OrdersShopaholic\Classes\Processor\CartProcessor;

/**
 * Class OrderProcessorTest
 * @package Lovata\OrdersShopaholic\Tests\Unit\Processor
 * @author Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 *
 * @mixin \PHPUnit\Framework\Assert
 */
class OrderProcessorTest extends CommonTest
{
    /** @var  Cart */
    protected $obElement;

    /** @var  Product */
    protected $obProduct;

    /** @var  Offer */
    protected $obOffer;

    /** @var  User */
    protected $obUser;

    /** @var  ShippingType */
    protected $obShippingType;

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

    protected $arShippingTypeData = [
        'name'         => 'name',
        'code'         => 'code',
        'preview_text' => 'preview_text',
        'price'        => '1,10',
    ];

    public function setUp()
    {
        parent::setUp();
        $this->runPluginRefreshCommand('lovata.popularityshopaholic', false);
    }

    /**
     * Test order creating
     */
    public function testCreateOrderWithEmptyCart()
    {
        $this->createTestData();

        $sErrorMessage = 'Method OrderProcessor::create is not correct';

        /** @var CartProcessor $obCartProcessor */
        $obCartProcessor = CartProcessor::instance();
        $obCartProcessor->clear();

        /** @var OrderProcessor $obOrderProcessor */
        $obOrderProcessor = OrderProcessor::instance();
        $obOrder = $obOrderProcessor->create([]);

        self::assertEquals(null, $obOrder, $sErrorMessage);
        self::assertEquals(false, Result::status(), $sErrorMessage);
        self::assertEquals(Lang::get('lovata.ordersshopaholic::lang.message.empty_cart'), Result::message(), $sErrorMessage);
    }

    /**
     * Test order creating
     */
    public function testAttachUserToOrder()
    {
        $this->createTestData();

        $sErrorMessage = 'Method OrderProcessor::create is not correct';

        AuthHelper::login($this->obUser);

        $arOfferList = [
            [
                'offer_id' => $this->obOffer->id,
                'quantity' => 1,
            ],
        ];

        /** @var CartProcessor $obCartProcessor */
        $obCartProcessor = CartProcessor::instance();
        $obCartProcessor->add($arOfferList, OfferCartPositionProcessor::class);

        /** @var OrderProcessor $obOrderProcessor */
        $obOrderProcessor = OrderProcessor::instance();
        $obOrder = $obOrderProcessor->create([], $this->obUser);

        self::assertInstanceOf(Order::class, $obOrder, $sErrorMessage);
        self::assertEquals(true, Result::status(), $sErrorMessage);
        self::assertEquals($this->obUser->id, $obOrder->user_id, $sErrorMessage);

        $obCartProcessor->clear();
    }

    /**
     * Test order creating
     */
    public function testOrderFields()
    {
        $this->createTestData();

        $sErrorMessage = 'Method OrderProcessor::create is not correct';

        AuthHelper::login($this->obUser);

        $arOfferList = [
            [
                'offer_id' => $this->obOffer->id,
                'quantity' => 2,
                'property' => ['comment' => 'test'],
            ],
        ];

        /** @var CartProcessor $obCartProcessor */
        $obCartProcessor = CartProcessor::instance();
        $obCartProcessor->add($arOfferList, OfferCartPositionProcessor::class);

        $arOrderData = [
            'payment_method_id' => 1,
            'shipping_type_id'  => $this->obShippingType->id,
        ];

        /** @var OrderProcessor $obOrderProcessor */
        $obOrderProcessor = OrderProcessor::instance();
        $obOrder = $obOrderProcessor->create($arOrderData, $this->obUser);

        self::assertInstanceOf(Order::class, $obOrder, $sErrorMessage);
        self::assertEquals(true, Result::status(), $sErrorMessage);
        self::assertEquals(1, $obOrder->payment_method_id, $sErrorMessage);
        self::assertEquals(1, $obOrder->shipping_type_id, $sErrorMessage);

        self::assertEquals('1.10', $obOrder->shipping_price, $sErrorMessage);
        self::assertEquals(1.1, $obOrder->shipping_price_value, $sErrorMessage);

        self::assertEquals('21.10', $obOrder->position_total_price, $sErrorMessage);
        self::assertEquals(21.1, $obOrder->position_total_price_value, $sErrorMessage);

        self::assertEquals('22.20', $obOrder->total_price, $sErrorMessage);
        self::assertEquals(22.2, $obOrder->total_price_value, $sErrorMessage);

        //Get order position
        /** @var OrderPosition $obOrderPosition */
        $obOrderPosition = $obOrder->order_position->first();

        self::assertInstanceOf(OrderPosition::class, $obOrderPosition, $sErrorMessage);
        self::assertEquals(2, $obOrderPosition->quantity, $sErrorMessage);
        self::assertEquals(['comment' => 'test'], $obOrderPosition->property, $sErrorMessage);

        $arRouteData = [
            'id'     => $obOrder->id,
            'number' => $obOrder->order_number,
            'key'    => $obOrder->secret_key,
        ];

        self::assertEquals($arRouteData, Result::data(), $sErrorMessage);

        $obCartProcessor->clear();
    }

    /**
     * Test order creating
     */
    public function testCheckingQuantity()
    {
        $this->createTestData();

        $sErrorMessage = 'Method OrderProcessor::create is not correct';

        AuthHelper::login($this->obUser);

        $arOfferList = [
            [
                'offer_id' => $this->obOffer->id,
                'quantity' => 20,
            ],
        ];

        /** @var CartProcessor $obCartProcessor */
        $obCartProcessor = CartProcessor::instance();
        $obCartProcessor->add($arOfferList, OfferCartPositionProcessor::class);

        /** @var OrderProcessor $obOrderProcessor */
        $obOrderProcessor = OrderProcessor::instance();
        $obOrder = $obOrderProcessor->create([], $this->obUser);

        self::assertInstanceOf(Order::class, $obOrder, $sErrorMessage);
        self::assertEquals(true, Result::status(), $sErrorMessage);

        $obCartProcessor->clear();
    }

    /**
     * Test order creating
     */
    public function testCheckingQuantityFail()
    {
        $this->createTestData();

        $sErrorMessage = 'Method OrderProcessor::create is not correct';

        AuthHelper::login($this->obUser);

        $arOfferList = [
            [
                'offer_id' => $this->obOffer->id,
                'quantity' => 20,
            ],
        ];

        Settings::set('check_offer_quantity', true);

        /** @var CartProcessor $obCartProcessor */
        $obCartProcessor = CartProcessor::instance();
        $obCartProcessor->add($arOfferList, OfferCartPositionProcessor::class);

        /** @var OrderProcessor $obOrderProcessor */
        $obOrderProcessor = OrderProcessor::instance();
        $obOrder = $obOrderProcessor->create([], $this->obUser);

        self::assertEquals(null, $obOrder, $sErrorMessage);
        self::assertEquals(false, Result::status(), $sErrorMessage);

        $sMessage = Lang::get('lovata.ordersshopaholic::lang.message.insufficient_amount');
        self::assertEquals($sMessage, Result::message(), $sErrorMessage);

        self::assertEquals(['cart_position_id' => 1], Result::data(), $sErrorMessage);

        $obCartProcessor->clear();
    }

    /**
     * Test order creating
     */
    public function testCheckingDecrementQuantity()
    {
        $this->createTestData();

        $sErrorMessage = 'Method OrderProcessor::create is not correct';

        AuthHelper::login($this->obUser);

        $arOfferList = [
            [
                'offer_id' => $this->obOffer->id,
                'quantity' => 3,
            ],
        ];

        /** @var CartProcessor $obCartProcessor */
        $obCartProcessor = CartProcessor::instance();
        $obCartProcessor->add($arOfferList, OfferCartPositionProcessor::class);

        /** @var OrderProcessor $obOrderProcessor */
        $obOrderProcessor = OrderProcessor::instance();
        $obOrder = $obOrderProcessor->create([], $this->obUser);

        self::assertInstanceOf(Order::class, $obOrder, $sErrorMessage);
        self::assertEquals(true, Result::status(), $sErrorMessage);

        /** @var Offer $obOffer */
        $obOffer = Offer::find($this->obOffer->id);
        self::assertEquals(5, $obOffer->quantity, $sErrorMessage);
    }

    /**
     * Test order creating
     */
    public function testCheckingDecrementQuantityFail()
    {
        $this->createTestData();

        $sErrorMessage = 'Method OrderProcessor::create is not correct';

        AuthHelper::login($this->obUser);

        $arOfferList = [
            [
                'offer_id' => $this->obOffer->id,
                'quantity' => 3,
            ],
        ];

        Settings::set('decrement_offer_quantity', true);

        /** @var CartProcessor $obCartProcessor */
        $obCartProcessor = CartProcessor::instance();
        $obCartProcessor->add($arOfferList, OfferCartPositionProcessor::class);

        /** @var OrderProcessor $obOrderProcessor */
        $obOrderProcessor = OrderProcessor::instance();
        $obOrder = $obOrderProcessor->create([], $this->obUser);

        self::assertInstanceOf(Order::class, $obOrder, $sErrorMessage);
        self::assertEquals(true, Result::status(), $sErrorMessage);

        /** @var Offer $obOffer */
        $obOffer = Offer::find($this->obOffer->id);
        self::assertEquals(2, $obOffer->quantity, $sErrorMessage);

        /** @var CartProcessor $obCartProcessor */
        $obCartProcessor = CartProcessor::instance();
        $obCartProcessor->add($arOfferList, OfferCartPositionProcessor::class);

        /** @var OrderProcessor $obOrderProcessor */
        $obOrderProcessor = OrderProcessor::instance();
        $obOrder = $obOrderProcessor->create([], $this->obUser);

        self::assertEquals(null, $obOrder, $sErrorMessage);
        self::assertEquals(false, Result::status(), $sErrorMessage);

        $sMessage = Lang::get('lovata.ordersshopaholic::lang.message.insufficient_amount');
        self::assertEquals($sMessage, Result::message(), $sErrorMessage);

        $obCartProcessor->clear();
    }

    /**
     * Create shipping type object for test
     */
    protected function createTestData()
    {
        \Lovata\Toolbox\Models\Settings::set('decimals', 2);

        //Create product data
        $arCreateData = $this->arProductData;
        $arCreateData['active'] = true;
        $this->obProduct = Product::create($arCreateData);

        //Create offer data
        $arCreateData = $this->arOfferData;
        $arCreateData['product_id'] = $this->obProduct->id;
        $this->obOffer = Offer::create($arCreateData);

        $arCreateData['product_id'] = $this->obProduct->id + 1;
        Offer::create($arCreateData);

        $this->obShippingType = ShippingType::create($this->arShippingTypeData);

        $arCreateData = $this->arUserData;

        $this->obUser = User::create($arCreateData);
        $this->obUser = User::find($this->obUser->id);
    }
}