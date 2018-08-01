<?php namespace Lovata\OrdersShopaholic\Tests\Unit\Collection;

use Lovata\Toolbox\Models\Settings;
use Lovata\Toolbox\Tests\CommonTest;

use Lovata\Shopaholic\Models\Offer;
use Lovata\Shopaholic\Models\Product;
use Lovata\OrdersShopaholic\Models\CartPosition;
use Lovata\OrdersShopaholic\Classes\Item\CartPositionItem;
use Lovata\OrdersShopaholic\Classes\Collection\CartPositionCollection;

/**
 * Class CartPositionCollectionTest
 * @package Lovata\OrdersShopaholic\Tests\Unit\Collection
 * @author Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 *
 * @mixin \PHPUnit\Framework\Assert
 */
class CartPositionCollectionTest extends CommonTest
{
    /** @var  CartPosition */
    protected $obElement;

    /** @var  Product */
    protected $obProduct;

    /** @var  Offer */
    protected $obOffer;

    protected $arCreateData = [
        'quantity' => 10,
    ];

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

    /**
     * Check item collection
     */
    public function testCollectionItem()
    {
        $this->createTestData();
        if(empty($this->obElement)) {
            return;
        }

        $sErrorMessage = 'CartPosition collection item data is not correct';

        //Check item collection
        $obCollection = CartPositionCollection::make([$this->obElement->id]);

        /** @var CartPositionItem $obItem */
        $obItem = $obCollection->first();
        self::assertInstanceOf(CartPositionItem::class, $obItem, $sErrorMessage);
        self::assertEquals($this->obElement->id, $obItem->id, $sErrorMessage);
    }

    /**
     * Check item collection "hasProduct" method
     */
    public function testHasProductMethod()
    {
        $this->createTestData();
        if(empty($this->obElement)) {
            return;
        }

        $sErrorMessage = 'CartPosition collection "hasProduct" method is not correct';

        //Check item collection after create
        $obCollection = CartPositionCollection::make([$this->obElement->id]);

        self::assertEquals(true, $obCollection->hasProduct($this->obProduct->id), $sErrorMessage);
        self::assertEquals(false, $obCollection->hasProduct($this->obProduct->id + 1), $sErrorMessage);
    }

    /**
     * Check item collection "hasOffer" method
     */
    public function testHasOfferMethod()
    {
        $this->createTestData();
        if(empty($this->obElement)) {
            return;
        }

        $sErrorMessage = 'CartPosition collection "hasOffer" method is not correct';

        //Check item collection after create
        $obCollection = CartPositionCollection::make([$this->obElement->id]);

        self::assertEquals(true, $obCollection->hasOffer($this->obOffer->id), $sErrorMessage);
        self::assertEquals(false, $obCollection->hasOffer($this->obOffer->id + 1), $sErrorMessage);
    }

    /**
     * Check item collection "getTotalPrice" method
     */
    public function testGetTotalPriceMethod()
    {
        $this->createTestData();
        if(empty($this->obElement)) {
            return;
        }

        $sErrorMessage = 'CartPosition collection "getTotalPrice" method is not correct';

        //Check item collection after create
        $obCollection = CartPositionCollection::make([$this->obElement->id]);

        self::assertEquals('105.50', $obCollection->getTotalPrice(), $sErrorMessage);
    }

    /**
     * Check item collection "getTotalPriceValue" method
     */
    public function testGetTotalPriceValueMethod()
    {
        $this->createTestData();
        if(empty($this->obElement)) {
            return;
        }

        $sErrorMessage = 'CartPosition collection "getTotalPriceValue" method is not correct';

        //Check item collection after create
        $obCollection = CartPositionCollection::make([$this->obElement->id]);

        self::assertEquals(105.5, $obCollection->getTotalPriceValue(), $sErrorMessage);
    }
    
    /**
     * Create shipping type object for test
     */
    protected function createTestData()
    {
        Settings::set('decimals', 2);

        //Create product data
        $arCreateData = $this->arProductData;
        $arCreateData['active'] = true;
        $this->obProduct = Product::create($arCreateData);

        //Create offer data
        $arCreateData = $this->arOfferData;
        $arCreateData['product_id'] = $this->obProduct->id;
        $this->obOffer = Offer::create($arCreateData);

        //Create new element data
        $arCreateData = $this->arCreateData;
        $arCreateData['item_id'] = $this->obOffer->id;
        $arCreateData['item_type'] = Offer::class;
        $arCreateData['cart_id'] = 1;

        $this->obElement = CartPosition::create($arCreateData);
    }
}