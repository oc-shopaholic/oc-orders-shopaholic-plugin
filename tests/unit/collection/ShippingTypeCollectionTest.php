<?php namespace Lovata\OrdersShopaholic\Tests\Unit\Collection;

use Lovata\Toolbox\Tests\CommonTest;

use Lovata\OrdersShopaholic\Models\PaymentMethod;
use Lovata\OrdersShopaholic\Classes\Item\PaymentMethodItem;
use Lovata\OrdersShopaholic\Classes\Collection\PaymentMethodCollection;

/**
 * Class PaymentMethodCollectionTest
 * @package Lovata\OrdersShopaholic\Tests\Unit\Collection
 * @author Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 *
 * @mixin \PHPUnit\Framework\Assert
 */
class PaymentMethodCollectionTest extends CommonTest
{
    /** @var  PaymentMethod */
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

        $sErrorMessage = 'PaymentMethod collection item data is not correct';

        //Check item collection
        $obCollection = PaymentMethodCollection::make([$this->obElement->id]);

        /** @var PaymentMethodItem $obItem */
        $obItem = $obCollection->first();
        self::assertInstanceOf(PaymentMethodItem::class, $obItem, $sErrorMessage);
        self::assertEquals($this->obElement->id, $obItem->id, $sErrorMessage);
    }

    /**
     * Check item collection "active" method
     */
    public function testActiveList()
    {
        PaymentMethodCollection::make()->active();

        $this->createTestData();
        if(empty($this->obElement)) {
            return;
        }

        $sErrorMessage = 'PaymentMethod collection "active" method is not correct';

        //Check item collection after create
        $obCollection = PaymentMethodCollection::make()->active();

        /** @var PaymentMethodItem $obItem */
        $obItem = $obCollection->first();
        self::assertInstanceOf(PaymentMethodItem::class, $obItem, $sErrorMessage);
        self::assertEquals($this->obElement->id, $obItem->id, $sErrorMessage);

        $this->obElement->active = false;
        $this->obElement->save();

        //Check item collection, after active = false
        $obCollection = PaymentMethodCollection::make()->active();
        self::assertEquals(true, $obCollection->isEmpty(), $sErrorMessage);

        $this->obElement->active = true;
        $this->obElement->save();

        //Check item collection, after active = true
        $obCollection = PaymentMethodCollection::make()->active();

        /** @var PaymentMethodItem $obItem */
        $obItem = $obCollection->first();
        self::assertInstanceOf(PaymentMethodItem::class, $obItem, $sErrorMessage);
        self::assertEquals($this->obElement->id, $obItem->id, $sErrorMessage);

        $this->obElement->delete();

        //Check item collection, after element remove
        $obCollection = PaymentMethodCollection::make()->active();
        self::assertEquals(true, $obCollection->isEmpty(), $sErrorMessage);
    }
    
    /**
     * Create payment method object for test
     */
    protected function createTestData()
    {
        //Create new element data
        $arCreateData = $this->arCreateData;
        $arCreateData['active'] = true;

        $this->obElement = PaymentMethod::create($arCreateData);
    }
}