<?php namespace Lovata\OrdersShopaholic\Tests\Unit\Item;

use Lovata\Toolbox\Tests\CommonTest;

use Lovata\OrdersShopaholic\Models\ShippingType;
use Lovata\OrdersShopaholic\Classes\Item\ShippingTypeItem;

/**
 * Class ShippingTypeItemTest
 * @package Lovata\OrdersShopaholic\Tests\Unit\Item
 * @author Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 *
 * @mixin \PHPUnit\Framework\Assert
 */
class ShippingTypeItemTest extends CommonTest
{
    /** @var  ShippingType */
    protected $obElement;

    protected $arCreateData = [
        'name'         => 'name',
        'code'         => 'code',
        'preview_text' => 'preview_text',
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

        $sErrorMessage = 'ShippingType item data is not correct';

        $arCreatedData = $this->arCreateData;
        $arCreatedData['id'] = $this->obElement->id;

        //Check item fields
        $obItem = ShippingTypeItem::make($this->obElement->id);
        foreach ($arCreatedData as $sField => $sValue) {
            self::assertEquals($sValue, $obItem->$sField, $sErrorMessage);
        }
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

        $sErrorMessage = 'ShippingType item data is not correct, after model update';

        $obItem = ShippingTypeItem::make($this->obElement->id);
        self::assertEquals('name', $obItem->name, $sErrorMessage);

        //Check cache update
        $this->obElement->name = 'test';
        $this->obElement->save();

        $obItem = ShippingTypeItem::make($this->obElement->id);
        self::assertEquals('test', $obItem->name, $sErrorMessage);
    }

    /**
     * Check item data, after active flag = false
     */
    public function testActiveFlag()
    {
        $this->createTestData();
        if(empty($this->obElement)) {
            return;
        }

        $sErrorMessage = 'ShippingType item data is not correct, after model active flag = false';

        $obItem = ShippingTypeItem::make($this->obElement->id);
        self::assertEquals(false, $obItem->isEmpty(), $sErrorMessage);

        //Check active flag in item data
        $this->obElement->active = false;
        $this->obElement->save();

        $obItem = ShippingTypeItem::make($this->obElement->id);
        self::assertEquals(true, $obItem->isEmpty(), $sErrorMessage);
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

        $sErrorMessage = 'ShippingType item data is not correct, after model remove';

        $obItem = ShippingTypeItem::make($this->obElement->id);
        self::assertEquals(false, $obItem->isEmpty(), $sErrorMessage);

        //Remove element
        $this->obElement->delete();

        $obItem = ShippingTypeItem::make($this->obElement->id);
        self::assertEquals(true, $obItem->isEmpty(), $sErrorMessage);
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