<?php namespace Lovata\OrdersShopaholic\Tests\Unit\Component;

use Kharanenka\Helper\Result;
use Lovata\Buddies\Facades\AuthHelper;
use Lovata\OrdersShopaholic\Components\MakeOrder;
use Lovata\OrdersShopaholic\Models\Order;
use Lovata\OrdersShopaholic\Models\ShippingType;
use Lovata\Shopaholic\Models\Settings;
use Lovata\Toolbox\Tests\CommonTest;

use Lovata\Shopaholic\Models\Offer;
use Lovata\Shopaholic\Models\Product;
use Lovata\Buddies\Models\User;
use Lovata\OrdersShopaholic\Models\Cart;
use Lovata\OrdersShopaholic\Classes\Processor\CartProcessor;
use Lovata\OrdersShopaholic\Classes\Processor\OfferCartPositionProcessor;

/**
 * Class MakeOrderTest
 * @package Lovata\OrdersShopaholic\Tests\Unit\Component
 * @author Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 *
 * @mixin \PHPUnit\Framework\Assert
 */
class MakeOrderTest extends CommonTest
{
    /** @var  Cart */
    protected $obElement;

    /** @var  Product */
    protected $obProduct;

    /** @var  Offer */
    protected $obOffer;

    /** @var  ShippingType */
    protected $obShippingType;

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
    public function testCreateOrderWithAuthUser()
    {
        $this->createTestData();

        $sErrorMessage = 'Method MakeOrder::create is not correct';

        AuthHelper::login($this->obUser);

        $arOfferList = [
            [
                'offer_id' => $this->obOffer->id,
                'quantity' => 2,
                'property' => [
                    'comment' => 'test',
                ],
            ],
        ];

        /** @var CartProcessor $obCartProcessor */
        $obCartProcessor = CartProcessor::instance();
        $obCartProcessor->add($arOfferList, OfferCartPositionProcessor::class);

        $arOrderData = [
            'payment_method_id' => 1,
            'shipping_type_id'  => $this->obShippingType->id,
        ];

        $obComponent = new MakeOrder();
        $obComponent->init();
        $obComponent->create($arOrderData, null);

        /** @var Order $obOrder */
        $obOrder = Order::orderBy('id', 'desc')->first();
        self::assertEquals(true, Result::status(), $sErrorMessage);
        self::assertEquals($this->obUser->id, $obOrder->user_id, $sErrorMessage);
        self::assertEquals(1, $obOrder->payment_method_id, $sErrorMessage);
        self::assertEquals(1, $obOrder->shipping_type_id, $sErrorMessage);

        self::assertEquals('1.10', $obOrder->shipping_price, $sErrorMessage);
        self::assertEquals(1.1, $obOrder->shipping_price_value, $sErrorMessage);

        self::assertEquals('21.10', $obOrder->position_total_price, $sErrorMessage);
        self::assertEquals(21.1, $obOrder->position_total_price_value, $sErrorMessage);

        self::assertEquals('22.20', $obOrder->total_price, $sErrorMessage);
        self::assertEquals(22.2, $obOrder->total_price_value, $sErrorMessage);

        $arOrderProperty = $obOrder->property;

        self::assertEquals($this->obUser->email, $arOrderProperty['email'], $sErrorMessage);

        $obCartProcessor->clear();
    }

    /**
     * Test order creating
     */
    public function testCreateOrderWithLogoutUser()
    {
        $this->createTestData();

        Settings::set('create_new_user', true);
        $sErrorMessage = 'Method MakeOrder::create is not correct';

        AuthHelper::logout();

        $arOfferList = [
            [
                'offer_id' => $this->obOffer->id,
                'quantity' => 2,
            ],
        ];

        $obCart = Cart::create([]);
        CartProcessor::$iTestCartID = $obCart->id;

        /** @var CartProcessor $obCartProcessor */
        $obCartProcessor = CartProcessor::instance();
        $obCartProcessor->add($arOfferList, OfferCartPositionProcessor::class);

        $arOrderData = [
            'payment_method_id' => 1,
            'shipping_type_id'  => 1,
            'shipping_price'    => '1,1'
        ];

        $arUserData = [
            'email'       => 'email@email.com',
            'name'        => 'name',
            'last_name'   => 'last_name',
            'middle_name' => 'middle_name',
            'phone_list'  => ['123', '321'],
        ];

        $obComponent = new MakeOrder();
        $obComponent->init();
        $obComponent->create($arOrderData, $arUserData);

        /** @var Order $obOrder */
        $obOrder = Order::orderBy('id', 'desc')->first();
        self::assertEquals(true, Result::status(), $sErrorMessage);
        self::assertEquals($this->obUser->id, $obOrder->user_id, $sErrorMessage);
        self::assertEquals(1, $obOrder->payment_method_id, $sErrorMessage);
        self::assertEquals(1, $obOrder->shipping_type_id, $sErrorMessage);

        self::assertEquals('1.10', $obOrder->shipping_price, $sErrorMessage);
        self::assertEquals(1.1, $obOrder->shipping_price_value, $sErrorMessage);

        self::assertEquals('21.10', $obOrder->position_total_price, $sErrorMessage);
        self::assertEquals(21.1, $obOrder->position_total_price_value, $sErrorMessage);

        self::assertEquals('22.20', $obOrder->total_price, $sErrorMessage);
        self::assertEquals(22.2, $obOrder->total_price_value, $sErrorMessage);

        $arOrderProperty = $obOrder->property;

        self::assertEquals($this->obUser->email, $arOrderProperty['email'], $sErrorMessage);

        $obCartProcessor->clear();
    }

    /**
     * Test order creating
     */
    public function testCreateOrderWithNewUser()
    {
        $this->createTestData();

        Settings::set('create_new_user', true);
        $sErrorMessage = 'Method MakeOrder::create is not correct';

        AuthHelper::logout();

        $arOfferList = [
            [
                'offer_id' => $this->obOffer->id,
                'quantity' => 2,
            ],
        ];

        $obCart = Cart::create([]);
        CartProcessor::$iTestCartID = $obCart->id;

        /** @var CartProcessor $obCartProcessor */
        $obCartProcessor = CartProcessor::instance();
        $obCartProcessor->add($arOfferList, OfferCartPositionProcessor::class);

        $arOrderData = [
            'payment_method_id' => 1,
            'shipping_type_id'  => 1,
            'shipping_price'    => '1,1'
        ];

        $arUserData = [
            'email'       => 'email1@email.com',
            'name'        => 'name',
            'last_name'   => 'last_name',
            'middle_name' => 'middle_name',
            'phone_list'  => ['123', '321'],
        ];

        $obComponent = new MakeOrder();
        $obComponent->init();
        $obComponent->create($arOrderData, $arUserData);

        /** @var Order $obOrder */
        $obOrder = Order::orderBy('id', 'desc')->first();
        $obUser = User::orderBy('id', 'desc')->first();

        self::assertEquals(true, Result::status(), $sErrorMessage);
        self::assertEquals($obUser->id, $obOrder->user_id, $sErrorMessage);
        self::assertEquals(1, $obOrder->payment_method_id, $sErrorMessage);
        self::assertEquals(1, $obOrder->shipping_type_id, $sErrorMessage);

        self::assertEquals('1.10', $obOrder->shipping_price, $sErrorMessage);
        self::assertEquals(1.1, $obOrder->shipping_price_value, $sErrorMessage);

        self::assertEquals('21.10', $obOrder->position_total_price, $sErrorMessage);
        self::assertEquals(21.1, $obOrder->position_total_price_value, $sErrorMessage);

        self::assertEquals('22.20', $obOrder->total_price, $sErrorMessage);
        self::assertEquals(22.2, $obOrder->total_price_value, $sErrorMessage);

        $arOrderProperty = $obOrder->property;

        self::assertEquals($obUser->email, $arOrderProperty['email'], $sErrorMessage);

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