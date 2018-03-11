<?php namespace Lovata\OrdersShopaholic\Tests\Unit\Processor;

use Lang;
use App;
use Kharanenka\Helper\Result;
use Lovata\Buddies\Facades\AuthHelper;
use Lovata\OrdersShopaholic\Classes\OrderProcessor;
use Lovata\OrdersShopaholic\Models\Order;
use Lovata\Shopaholic\Models\Settings;
use Lovata\Toolbox\Tests\CommonTest;

use Lovata\Shopaholic\Models\Offer;
use Lovata\Shopaholic\Models\Product;
use Lovata\Buddies\Models\User;
use Lovata\OrdersShopaholic\Models\Cart;
use Lovata\OrdersShopaholic\Classes\CartProcessor;

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

    /**
     * Test order creating
     */
    public function testCreateOrderWithEmptyCart()
    {
        $this->createTestData();

        $sErrorMessage = 'Method OrderProcessor::create is not correct';

        /** @var CartProcessor $obCartProcessor */
        $obCartProcessor = CartProcessor::instance();
        $obCartProcessor->init();
        $obCartProcessor->clear();

        /** @var OrderProcessor $obOrderProcessor */
        $obOrderProcessor = App::make(OrderProcessor::class);
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
        $obCartProcessor->init();
        $obCartProcessor->add($arOfferList);

        /** @var OrderProcessor $obOrderProcessor */
        $obOrderProcessor = App::make(OrderProcessor::class);
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
            ],
        ];

        /** @var CartProcessor $obCartProcessor */
        $obCartProcessor = CartProcessor::instance();
        $obCartProcessor->init();
        $obCartProcessor->add($arOfferList);

        $arOrderData = [
            'payment_method_id' => 1,
            'shipping_type_id'  => 1,
            'shipping_price'    => '1,1'
        ];

        /** @var OrderProcessor $obOrderProcessor */
        $obOrderProcessor = App::make(OrderProcessor::class);
        $obOrder = $obOrderProcessor->create($arOrderData, $this->obUser);

        self::assertInstanceOf(Order::class, $obOrder, $sErrorMessage);
        self::assertEquals(true, Result::status(), $sErrorMessage);
        self::assertEquals(1, $obOrder->payment_method_id, $sErrorMessage);
        self::assertEquals(1, $obOrder->shipping_type_id, $sErrorMessage);

        self::assertEquals('1.1', $obOrder->shipping_price, $sErrorMessage);
        self::assertEquals(1.1, $obOrder->getShippingPriceValue(), $sErrorMessage);

        self::assertEquals('21.1', $obOrder->offers_total_price, $sErrorMessage);
        self::assertEquals(21.1, $obOrder->getOffersTotalPriceValue(), $sErrorMessage);

        self::assertEquals('22.2', $obOrder->total_price, $sErrorMessage);
        self::assertEquals(22.2, $obOrder->getTotalPriceValue(), $sErrorMessage);

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
        $obCartProcessor->init();
        $obCartProcessor->add($arOfferList);

        /** @var OrderProcessor $obOrderProcessor */
        $obOrderProcessor = App::make(OrderProcessor::class);
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
        $obCartProcessor->init();
        $obCartProcessor->add($arOfferList);

        /** @var OrderProcessor $obOrderProcessor */
        $obOrderProcessor = App::make(OrderProcessor::class);
        $obOrder = $obOrderProcessor->create([], $this->obUser);

        self::assertEquals(null, $obOrder, $sErrorMessage);
        self::assertEquals(false, Result::status(), $sErrorMessage);

        $sMessage = Lang::get('lovata.ordersshopaholic::lang.message.insufficient_amount');
        self::assertEquals($sMessage, Result::message(), $sErrorMessage);

        self::assertEquals(['offer_id' => $this->obOffer->id], Result::data(), $sErrorMessage);

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
        $obCartProcessor->init();
        $obCartProcessor->add($arOfferList);

        /** @var OrderProcessor $obOrderProcessor */
        $obOrderProcessor = App::make(OrderProcessor::class);
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
        $obCartProcessor->init();
        $obCartProcessor->add($arOfferList);

        /** @var OrderProcessor $obOrderProcessor */
        $obOrderProcessor = App::make(OrderProcessor::class);
        $obOrder = $obOrderProcessor->create([], $this->obUser);

        self::assertInstanceOf(Order::class, $obOrder, $sErrorMessage);
        self::assertEquals(true, Result::status(), $sErrorMessage);

        /** @var Offer $obOffer */
        $obOffer = Offer::find($this->obOffer->id);
        self::assertEquals(2, $obOffer->quantity, $sErrorMessage);

        /** @var CartProcessor $obCartProcessor */
        $obCartProcessor = CartProcessor::instance();
        $obCartProcessor->init();
        $obCartProcessor->add($arOfferList);

        /** @var OrderProcessor $obOrderProcessor */
        $obOrderProcessor = App::make(OrderProcessor::class);
        $obOrder = $obOrderProcessor->create([], $this->obUser);

        self::assertEquals(null, $obOrder, $sErrorMessage);
        self::assertEquals(false, Result::status(), $sErrorMessage);

        $sMessage = Lang::get('lovata.ordersshopaholic::lang.message.insufficient_amount');
        self::assertEquals($sMessage, Result::message(), $sErrorMessage);

        self::assertEquals(['offer_id' => $this->obOffer->id], Result::data(), $sErrorMessage);

        $obCartProcessor->clear();
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
        $arCreateData['product_id'] = $this->obProduct->id;
        $this->obOffer = Offer::create($arCreateData);

        $arCreateData['product_id'] = $this->obProduct->id + 1;
        Offer::create($arCreateData);

        $arCreateData = $this->arUserData;

        $this->obUser = User::create($arCreateData);
        $this->obUser = User::find($this->obUser->id);
    }
}