<?php namespace Lovata\OrdersShopaholic\Tests\Unit\Item;

use Lovata\Shopaholic\Classes\Item\OfferItem;
use Lovata\Toolbox\Tests\CommonTest;

use Lovata\Shopaholic\Models\Offer;
use Lovata\Shopaholic\Models\Product;
use Lovata\OrdersShopaholic\Models\CartElement;
use Lovata\OrdersShopaholic\Classes\Item\CartElementItem;

/**
 * Class CartElementItemTest
 * @package Lovata\OrdersShopaholic\Tests\Unit\Item
 * @author Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 *
 * @mixin \PHPUnit\Framework\Assert
 */
class CartElementItemTest extends CommonTest
{
    /** @var  CartElement */
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
     * Check item fields
     */
    public function testItemFields()
    {
        $this->createTestData();
        if(empty($this->obElement)) {
            return;
        }

        $sErrorMessage = 'CartElement item data is not correct';

        $arCreatedData = $this->arCreateData;
        $arCreatedData['id'] = $this->obElement->id;

        //Check item fields
        $obItem = CartElementItem::make($this->obElement->id);
        foreach ($arCreatedData as $sField => $sValue) {
            self::assertEquals($sValue, $obItem->$sField, $sErrorMessage);
        }

        self::assertEquals('105.50', $obItem->price, $sErrorMessage);
        self::assertEquals(105.50, $obItem->price_value, $sErrorMessage);

        $obOfferItem = $obItem->offer;
        self::assertInstanceOf(OfferItem::class, $obOfferItem, $sErrorMessage);
        self::assertEquals($this->obOffer->id, $obOfferItem->id, $sErrorMessage);
    }

    /**
     * Check update cache item data, after update model data
     */
    public function testItemClearCache()
    {
        $this->createTestData();
        if(empty($this->obElement)) {
            return;
        }

        $sErrorMessage = 'CartElement item data is not correct, after model update';

        $obItem = CartElementItem::make($this->obElement->id);
        self::assertEquals(10, $obItem->quantity, $sErrorMessage);

        //Check cache update
        $this->obElement->quantity = 15;
        $this->obElement->save();

        $obItem = CartElementItem::make($this->obElement->id);
        self::assertEquals(15, $obItem->quantity, $sErrorMessage);
    }

    /**
     * Check update cache item data, after remove element
     */
    public function testRemoveElement()
    {
        $this->createTestData();
        if(empty($this->obElement)) {
            return;
        }

        $sErrorMessage = 'CartElement item data is not correct, after model remove';

        $obItem = CartElementItem::make($this->obElement->id);
        self::assertEquals(false, $obItem->isEmpty(), $sErrorMessage);

        //Remove element
        $this->obElement->delete();

        $obItem = CartElementItem::make($this->obElement->id);
        self::assertEquals(true, $obItem->isEmpty(), $sErrorMessage);
    }

    /**
     * Create payment method object for test
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

        //Create new element data
        $arCreateData = $this->arCreateData;
        $arCreateData['offer_id'] = $this->obOffer->id;

        $this->obElement = CartElement::create($arCreateData);
    }
}