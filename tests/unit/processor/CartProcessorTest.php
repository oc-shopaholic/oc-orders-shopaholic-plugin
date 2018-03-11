<?php namespace Lovata\OrdersShopaholic\Tests\Unit\Processor;

use Lang;
use App;
use Kharanenka\Helper\Result;
use Lovata\OrdersShopaholic\Classes\Collection\CartElementCollection;
use Lovata\OrdersShopaholic\Classes\Item\CartElementItem;
use Lovata\Toolbox\Tests\CommonTest;

use Lovata\Shopaholic\Models\Offer;
use Lovata\Shopaholic\Models\Product;
use Lovata\Buddies\Models\User;
use Lovata\OrdersShopaholic\Models\Cart;
use Lovata\OrdersShopaholic\Classes\CartProcessor;

/**
 * Class CartProcessorTest
 * @package Lovata\OrdersShopaholic\Tests\Unit\Processor
 * @author Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
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

    /**
     * Test add method
     */
    public function testAddMethod()
    {
        $this->createTestData();

        $sErrorMessage = 'Method CartProcessor::add is not correct';

        /** @var CartProcessor $obCartProcessor */
        $obCartProcessor = CartProcessor::instance();
        $obCartProcessor->init();
        $bResult = $obCartProcessor->add([]);

        self::assertEquals(false, $bResult, $sErrorMessage);
        self::assertEquals(false, Result::status(), $sErrorMessage);
        self::assertEquals(Lang::get('lovata.toolbox::lang.message.e_not_correct_request'), Result::message(), $sErrorMessage);

        $arOfferList = [
            [
                'offer_id' => $this->obOffer->id,
                'quantity' => 10,
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

        $bResult = $obCartProcessor->add($arOfferList);

        self::assertEquals(true, $bResult, $sErrorMessage);
        self::assertEquals(true, Result::status(), $sErrorMessage);

        $obCartElementList = $obCartProcessor->get();

        self::assertEquals(1, $obCartElementList->count(), $sErrorMessage);

        /** @var CartElementItem $obCartElementItem */
        $obCartElementItem = $obCartElementList->first();
        self::assertEquals($this->obOffer->id, $obCartElementItem->offer_id, $sErrorMessage);
        self::assertEquals(10, $obCartElementItem->quantity, $sErrorMessage);

        $arOfferList = [
            [
                'offer_id' => $this->obOffer->id,
                'quantity' => 5,
            ],
        ];

        $obCartProcessor->add($arOfferList);
        $obCartElementList = $obCartProcessor->get();

        /** @var CartElementItem $obCartElementItem */
        $obCartElementItem = $obCartElementList->first();
        self::assertEquals(5, $obCartElementItem->quantity, $sErrorMessage);

        $obCartProcessor->clear();
    }

    /**
     * Test update method
     */
    public function testUpdateMethod()
    {
        $this->createTestData();

        $sErrorMessage = 'Method CartProcessor::update is not correct';

        /** @var CartProcessor $obCartProcessor */
        $obCartProcessor = CartProcessor::instance();
        $obCartProcessor->init();
        $bResult = $obCartProcessor->update([]);

        self::assertEquals(false, $bResult, $sErrorMessage);
        self::assertEquals(false, Result::status(), $sErrorMessage);
        self::assertEquals(Lang::get('lovata.toolbox::lang.message.e_not_correct_request'), Result::message(), $sErrorMessage);

        $arOfferList = [
            [
                'offer_id' => $this->obOffer->id,
                'quantity' => 5,
            ],
        ];

        $obCartProcessor->add($arOfferList);

        $arOfferList = [
            [
                'offer_id' => $this->obOffer->id,
                'quantity' => 10,
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

        $bResult = $obCartProcessor->update($arOfferList);

        self::assertEquals(true, $bResult, $sErrorMessage);
        self::assertEquals(true, Result::status(), $sErrorMessage);

        $obCartElementList = $obCartProcessor->get();

        self::assertEquals(1, $obCartElementList->count(), $sErrorMessage);

        /** @var CartElementItem $obCartElementItem */
        $obCartElementItem = $obCartElementList->first();
        self::assertEquals($this->obOffer->id, $obCartElementItem->offer_id, $sErrorMessage);
        self::assertEquals(10, $obCartElementItem->quantity, $sErrorMessage);

        $obCartProcessor->clear();
    }

    /**
     * Test remove method
     */
    public function testRemoveMethod()
    {
        $this->createTestData();

        $sErrorMessage = 'Method CartProcessor::remove is not correct';

        /** @var CartProcessor $obCartProcessor */
        $obCartProcessor = CartProcessor::instance();
        $obCartProcessor->init();
        $bResult = $obCartProcessor->remove([]);

        self::assertEquals(false, $bResult, $sErrorMessage);
        self::assertEquals(false, Result::status(), $sErrorMessage);
        self::assertEquals(Lang::get('lovata.toolbox::lang.message.e_not_correct_request'), Result::message(), $sErrorMessage);

        $arOfferList = [
            [
                'offer_id' => $this->obOffer->id,
                'quantity' => 10,
            ],
        ];

        $obCartProcessor->add($arOfferList);

        $arOfferList = [$this->obOffer->id + 1];
        $bResult = $obCartProcessor->remove($arOfferList);

        self::assertEquals(true, $bResult, $sErrorMessage);
        self::assertEquals(true, Result::status(), $sErrorMessage);

        $obCartElementList = $obCartProcessor->get();

        self::assertEquals(1, $obCartElementList->count(), $sErrorMessage);

        /** @var CartElementItem $obCartElementItem */
        $obCartElementItem = $obCartElementList->first();
        self::assertEquals($this->obOffer->id, $obCartElementItem->offer_id, $sErrorMessage);
        self::assertEquals(10, $obCartElementItem->quantity, $sErrorMessage);

        $arOfferList = [$this->obOffer->id];
        $bResult = $obCartProcessor->remove($arOfferList);

        self::assertEquals(true, $bResult, $sErrorMessage);
        self::assertEquals(true, Result::status(), $sErrorMessage);

        $obCartElementList = $obCartProcessor->get();

        self::assertEquals(0, $obCartElementList->count(), $sErrorMessage);

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
        $obCartProcessor->init();
        $obCartProcessor->clear();

        $obCartElementList = $obCartProcessor->get();

        self::assertEquals(0, $obCartElementList->count(), $sErrorMessage);

        $arOfferList = [
            [
                'offer_id' => $this->obOffer->id,
                'quantity' => 10,
            ],
        ];

        $obCartProcessor->add($arOfferList);
        $obCartProcessor->clear();

        $obCartElementList = $obCartProcessor->get();

        self::assertEquals(0, $obCartElementList->count(), $sErrorMessage);
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
        $obCartProcessor->init();

        $arOfferList = [
            [
                'offer_id' => $this->obOffer->id,
                'quantity' => 10,
            ],
        ];

        $obCartProcessor->add($arOfferList);

        $obCartElementList = $obCartProcessor->get();

        self::assertInstanceOf(CartElementCollection::class, $obCartElementList, $sErrorMessage);
        self::assertEquals(1, $obCartElementList->count(), $sErrorMessage);

        /** @var CartElementItem $obCartElementItem */
        $obCartElementItem = $obCartElementList->first();
        self::assertEquals($this->obOffer->id, $obCartElementItem->offer_id, $sErrorMessage);
        self::assertEquals(10, $obCartElementItem->quantity, $sErrorMessage);

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