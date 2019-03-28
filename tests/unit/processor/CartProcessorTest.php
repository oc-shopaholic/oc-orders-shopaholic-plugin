<?php namespace Lovata\OrdersShopaholic\Tests\Unit\Processor;

use Lang;
use Kharanenka\Helper\Result;
use Lovata\OrdersShopaholic\Classes\Collection\CartPositionCollection;
use Lovata\OrdersShopaholic\Classes\Item\CartPositionItem;
use Lovata\OrdersShopaholic\Classes\Processor\OfferCartPositionProcessor;
use Lovata\Toolbox\Tests\CommonTest;

use Lovata\Shopaholic\Models\Offer;
use Lovata\Shopaholic\Models\Product;
use Lovata\Buddies\Models\User;
use Lovata\OrdersShopaholic\Models\Cart;
use Lovata\OrdersShopaholic\Classes\Processor\CartProcessor;

/**
 * Class CartProcessorTest
 * @package Lovata\OrdersShopaholic\Tests\Unit\Processor
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 *
 * @mixin \PHPUnit\Framework\Assert
 */
class CartProcessorTest extends CommonTest
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

    public function setUp()
    {
        parent::setUp();
        $this->runPluginRefreshCommand('lovata.popularityshopaholic', false);
    }

    /**
     * Test add method
     */
    public function testAddMethod()
    {
        $this->createTestData();

        $sErrorMessage = 'Method CartProcessor::add is not correct';

        $obCart = Cart::create([]);
        CartProcessor::$iTestCartID = $obCart->id;

        /** @var CartProcessor $obCartProcessor */
        $obCartProcessor = CartProcessor::instance();
        $bResult = $obCartProcessor->add([], OfferCartPositionProcessor::class);

        self::assertEquals(false, $bResult, $sErrorMessage);
        self::assertEquals(false, Result::status(), $sErrorMessage);
        self::assertEquals(Lang::get('lovata.toolbox::lang.message.e_not_correct_request'), Result::message(), $sErrorMessage);

        $arOfferList = [
            [
                'offer_id' => $this->obOffer->id,
                'quantity' => 10,
                'property' => ['comment' => 'test'],
            ],
            [
                'offer_id' => $this->obOffer->id + 2,
                'quantity' => -1,
            ],
            [
                'offer_id' => $this->obOffer->id + 1,
                'quantity' => 5,
            ],
        ];

        $bResult = $obCartProcessor->add($arOfferList, OfferCartPositionProcessor::class);

        self::assertEquals(true, $bResult, $sErrorMessage);
        self::assertEquals(true, Result::status(), $sErrorMessage);

        $obCartPositionList = $obCartProcessor->get();

        self::assertEquals(1, $obCartPositionList->count(), $sErrorMessage);

        /** @var CartPositionItem $obCartPositionItem */
        $obCartPositionItem = $obCartPositionList->first();
        self::assertEquals($this->obOffer->id, $obCartPositionItem->item_id, $sErrorMessage);
        self::assertEquals(10, $obCartPositionItem->quantity, $sErrorMessage);
        self::assertEquals(['comment' => 'test'], $obCartPositionItem->property, $sErrorMessage);

        $arOfferList = [
            [
                'offer_id' => $this->obOffer->id,
                'quantity' => 5,
                'property' => ['comment' => 'test'],
            ],
        ];

        $obCartProcessor->add($arOfferList, OfferCartPositionProcessor::class);
        $obCartPositionList = $obCartProcessor->get();

        /** @var CartPositionItem $obCartPositionItem */
        $obCartPositionItem = $obCartPositionList->first();
        self::assertEquals(5, $obCartPositionItem->quantity, $sErrorMessage);

        $obCartProcessor->clear();
    }

    /**
     * Test update method
     */
    public function testUpdateMethod()
    {
        $this->createTestData();

        $sErrorMessage = 'Method CartProcessor::update is not correct';

        $obCart = Cart::create([]);
        CartProcessor::$iTestCartID = $obCart->id;

        /** @var CartProcessor $obCartProcessor */
        $obCartProcessor = CartProcessor::instance();
        $bResult = $obCartProcessor->update([], OfferCartPositionProcessor::class);

        self::assertEquals(false, $bResult, $sErrorMessage);
        self::assertEquals(false, Result::status(), $sErrorMessage);
        self::assertEquals(Lang::get('lovata.toolbox::lang.message.e_not_correct_request'), Result::message(), $sErrorMessage);

        $arOfferList = [
            [
                'offer_id' => $this->obOffer->id,
                'quantity' => 5,
                'property' => ['comment' => 'test1'],
            ],
        ];

        $obCartProcessor->add($arOfferList, OfferCartPositionProcessor::class);

        $arOfferList = [
            [
                'offer_id' => $this->obOffer->id,
                'quantity' => 10,
                'property' => ['comment' => 'test1'],
            ],
            [
                'offer_id' => $this->obOffer->id,
                'quantity' => -1,
            ],
            [
                'offer_id' => $this->obOffer->id + 1,
                'quantity' => 5,
            ],
        ];

        $bResult = $obCartProcessor->update($arOfferList, OfferCartPositionProcessor::class);

        self::assertEquals(true, $bResult, $sErrorMessage);
        self::assertEquals(true, Result::status(), $sErrorMessage);

        $obCartPositionList = $obCartProcessor->get();

        self::assertEquals(1, $obCartPositionList->count(), $sErrorMessage);

        /** @var CartPositionItem $obCartPositionItem */
        $obCartPositionItem = $obCartPositionList->first();
        self::assertEquals($this->obOffer->id, $obCartPositionItem->item_id, $sErrorMessage);
        self::assertEquals(10, $obCartPositionItem->quantity, $sErrorMessage);
        self::assertEquals(['comment' => 'test1'], $obCartPositionItem->property, $sErrorMessage);

        $obCartProcessor->clear();
    }

    /**
     * Test remove method
     */
    public function testRemoveMethod()
    {
        $this->createTestData();

        $sErrorMessage = 'Method CartProcessor::remove is not correct';

        $obCart = Cart::create([]);
        CartProcessor::$iTestCartID = $obCart->id;

        /** @var CartProcessor $obCartProcessor */
        $obCartProcessor = CartProcessor::instance();
        $bResult = $obCartProcessor->remove([], OfferCartPositionProcessor::class);

        self::assertEquals(false, $bResult, $sErrorMessage);
        self::assertEquals(false, Result::status(), $sErrorMessage);
        self::assertEquals(Lang::get('lovata.toolbox::lang.message.e_not_correct_request'), Result::message(), $sErrorMessage);

        $arOfferList = [
            [
                'offer_id' => $this->obOffer->id,
                'quantity' => 10,
            ],
        ];

        $obCartProcessor->add($arOfferList, OfferCartPositionProcessor::class);

        $arOfferList = [$this->obOffer->id + 1];
        $bResult = $obCartProcessor->remove($arOfferList, OfferCartPositionProcessor::class);

        self::assertEquals(true, $bResult, $sErrorMessage);
        self::assertEquals(true, Result::status(), $sErrorMessage);

        $obCartPositionList = $obCartProcessor->get();

        self::assertEquals(1, $obCartPositionList->count(), $sErrorMessage);

        /** @var CartPositionItem $obCartPositionItem */
        $obCartPositionItem = $obCartPositionList->first();
        self::assertEquals($this->obOffer->id, $obCartPositionItem->item_id, $sErrorMessage);
        self::assertEquals(10, $obCartPositionItem->quantity, $sErrorMessage);

        $arOfferList = [$this->obOffer->id];
        $bResult = $obCartProcessor->remove($arOfferList, OfferCartPositionProcessor::class);

        self::assertEquals(true, $bResult, $sErrorMessage);
        self::assertEquals(true, Result::status(), $sErrorMessage);

        $obCartPositionList = $obCartProcessor->get();

        self::assertEquals(0, $obCartPositionList->count(), $sErrorMessage);

        $obCartProcessor->clear();
    }

    /**
     * Test clear method
     */
    public function testClearMethod()
    {
        $this->createTestData();

        $sErrorMessage = 'Method CartProcessor::clear is not correct';

        /** @var CartProcessor $obCartProcessor */
        $obCartProcessor = CartProcessor::instance();
        $obCartProcessor->clear();

        $obCartPositionList = $obCartProcessor->get();

        self::assertEquals(0, $obCartPositionList->count(), $sErrorMessage);

        $arOfferList = [
            [
                'offer_id' => $this->obOffer->id,
                'quantity' => 10,
            ],
        ];

        $obCartProcessor->add($arOfferList, OfferCartPositionProcessor::class);
        $obCartProcessor->clear();

        $obCartPositionList = $obCartProcessor->get();

        self::assertEquals(0, $obCartPositionList->count(), $sErrorMessage);
    }

    /**
     * Test get method
     */
    public function testGetMethod()
    {
        $this->createTestData();

        $sErrorMessage = 'Method CartProcessor::get is not correct';

        /** @var CartProcessor $obCartProcessor */
        $obCartProcessor = CartProcessor::instance();

        $arOfferList = [
            [
                'offer_id' => $this->obOffer->id,
                'quantity' => 10,
            ],
        ];

        $obCartProcessor->add($arOfferList, OfferCartPositionProcessor::class);

        $obCartPositionList = $obCartProcessor->get();

        self::assertInstanceOf(CartPositionCollection::class, $obCartPositionList, $sErrorMessage);
        self::assertEquals(1, $obCartPositionList->count(), $sErrorMessage);

        /** @var CartPositionItem $obCartPositionItem */
        $obCartPositionItem = $obCartPositionList->first();
        self::assertEquals($this->obOffer->id, $obCartPositionItem->item_id, $sErrorMessage);
        self::assertEquals(10, $obCartPositionItem->quantity, $sErrorMessage);

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