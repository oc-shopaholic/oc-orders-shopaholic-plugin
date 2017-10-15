<?php namespace Lovata\OrdersShopaholic\Tests\Unit\Collection;

use Lovata\Toolbox\Tests\CommonTest;

use Lovata\OrdersShopaholic\Models\ShippingType;
use Lovata\OrdersShopaholic\Classes\Item\ShippingTypeItem;
use Lovata\OrdersShopaholic\Classes\Collection\ShippingTypeCollection;

/**
 * Class ShippingTypeCollectionTest
 * @package Lovata\OrdersShopaholic\Tests\Unit\Collection
 * @author Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 *
 * @mixin \PHPUnit\Framework\Assert
 */
class ShippingTypeCollectionTest extends CommonTest
{
    /** @var  ShippingType */
    protected $obElement;

    protected $arCreateData = [
        'name'         => 'name',
        'code'         => 'code',
        'preview_text' => 'preview_text',
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

        $sErrorMessage = 'ShippingType collection item data is not correct';

        //Check item collection
        $obCollection = ShippingTypeCollection::make([$this->obElement->id]);

        /** @var ShippingTypeItem $obItem */
        $obItem = $obCollection->first();
        self::assertInstanceOf(ShippingTypeItem::class, $obItem, $sErrorMessage);
        self::assertEquals($this->obElement->id, $obItem->id, $sErrorMessage);
    }

    /**
     * Check item collection "active" method
     */
    public function testActiveList()
    {
        ShippingTypeCollection::make()->active();

        $this->createTestData();
        if(empty($this->obElement)) {
            return;
        }

        $sErrorMessage = 'ShippingType collection "active" method is not correct';

        //Check item collection after create
        $obCollection = ShippingTypeCollection::make()->active();

        /** @var ShippingTypeItem $obItem */
        $obItem = $obCollection->first();
        self::assertInstanceOf(ShippingTypeItem::class, $obItem, $sErrorMessage);
        self::assertEquals($this->obElement->id, $obItem->id, $sErrorMessage);

        $this->obElement->active = false;
        $this->obElement->save();

        //Check item collection, after active = false
        $obCollection = ShippingTypeCollection::make()->active();
        self::assertEquals(true, $obCollection->isEmpty(), $sErrorMessage);

        $this->obElement->active = true;
        $this->obElement->save();

        //Check item collection, after active = true
        $obCollection = ShippingTypeCollection::make()->active();

        /** @var ShippingTypeItem $obItem */
        $obItem = $obCollection->first();
        self::assertInstanceOf(ShippingTypeItem::class, $obItem, $sErrorMessage);
        self::assertEquals($this->obElement->id, $obItem->id, $sErrorMessage);

        $this->obElement->delete();

        //Check item collection, after element remove
        $obCollection = ShippingTypeCollection::make()->active();
        self::assertEquals(true, $obCollection->isEmpty(), $sErrorMessage);
    }
    
    /**
     * Create shipping type object for test
     */
    protected function createTestData()
    {
        //Create new element data
        $arCreateData = $this->arCreateData;
        $arCreateData['active'] = true;

        $this->obElement = ShippingType::create($arCreateData);
    }
}