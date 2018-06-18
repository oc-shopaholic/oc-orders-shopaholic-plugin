<?php namespace Lovata\OrdersShopaholic\Tests\Unit\Collection;

use Lovata\Toolbox\Tests\CommonTest;

use Lovata\OrdersShopaholic\Models\Status;
use Lovata\OrdersShopaholic\Classes\Item\StatusItem;
use Lovata\OrdersShopaholic\Classes\Collection\StatusCollection;

/**
 * Class StatusCollectionTest
 * @package Lovata\OrdersShopaholic\Tests\Unit\Collection
 * @author Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 *
 * @mixin \PHPUnit\Framework\Assert
 */
class StatusCollectionTest extends CommonTest
{
    /** @var  Status */
    protected $obElement;

    protected $arCreateData = [
        'name'           => 'name',
        'code'           => 'code',
        'preview_text'   => 'preview_text',
        'is_user_show'   => true,
        'user_status_id' => 1,
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

        $sErrorMessage = 'Status collection item data is not correct';

        //Check item collection
        $obCollection = StatusCollection::make([$this->obElement->id]);

        /** @var StatusItem $obItem */
        $obItem = $obCollection->last();
        self::assertInstanceOf(StatusItem::class, $obItem, $sErrorMessage);
        self::assertEquals($this->obElement->id, $obItem->id, $sErrorMessage);
    }

    /**
     * Check item collection "isUserShow" method
     */
    public function testisUserShowList()
    {
        StatusCollection::make()->isUserShow();

        $this->createTestData();
        if(empty($this->obElement)) {
            return;
        }

        $sErrorMessage = 'Status collection "isUserShow" method is not correct';

        //Check item collection after create
        $obCollection = StatusCollection::make()->isUserShow();

        /** @var StatusItem $obItem */
        $obItem = $obCollection->first();
        self::assertInstanceOf(StatusItem::class, $obItem, $sErrorMessage);
        self::assertEquals($this->obElement->id, $obItem->id, $sErrorMessage);

        $this->obElement->is_user_show = false;
        $this->obElement->save();

        //Check item collection, after active = false
        $obCollection = StatusCollection::make()->isUserShow();
        self::assertEquals(true, $obCollection->isEmpty(), $sErrorMessage);

        $this->obElement->is_user_show = true;
        $this->obElement->save();

        //Check item collection, after active = true
        $obCollection = StatusCollection::make()->isUserShow();

        /** @var StatusItem $obItem */
        $obItem = $obCollection->first();
        self::assertInstanceOf(StatusItem::class, $obItem, $sErrorMessage);
        self::assertEquals($this->obElement->id, $obItem->id, $sErrorMessage);

        $this->obElement->delete();

        //Check item collection, after element remove
        $obCollection = StatusCollection::make()->isUserShow();
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

        $this->obElement = Status::create($arCreateData);
    }
}