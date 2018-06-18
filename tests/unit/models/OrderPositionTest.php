<?php namespace Lovata\OrdersShopaholic\Tests\Unit\Models;

include_once __DIR__.'/../../../../toolbox/vendor/autoload.php';
include_once __DIR__.'/../../../../../../tests/PluginTestCase.php';

use PluginTestCase;
use Lovata\Shopaholic\Models\Offer;
use Lovata\OrdersShopaholic\Models\Order;
use Lovata\OrdersShopaholic\Models\OrderPosition;

/**
 * Class OrderPositionTest
 * @package Lovata\OrdersShopaholic\Tests\Unit\Models
 * @author Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 *
 * @mixin \PHPUnit\Framework\Assert
 */
class OrderPositionTest extends PluginTestCase
{
    protected $sModelClass;

    /**
     * OrderPositionTest constructor.
     */
    public function __construct()
    {
        $this->sModelClass = OrderPosition::class;
        parent::__construct();
    }

    /**
     * Check model "item" relation config
     */
    public function testHasItemRelation()
    {
        $sErrorMessage = $this->sModelClass.' model has not correct "item" relation config';

        /** @var OrderPosition $obModel */
        $obModel = new OrderPosition();
        self::assertNotEmpty($obModel->morphTo, $sErrorMessage);
        self::assertArrayHasKey('item', $obModel->morphTo, $sErrorMessage);
    }

    /**
     * Check model "order" relation config
     */
    public function testHasOrderRelation()
    {
        $sErrorMessage = $this->sModelClass.' model has not correct "order" relation config';

        /** @var OrderPosition $obModel */
        $obModel = new OrderPosition();
        self::assertNotEmpty($obModel->belongsTo, $sErrorMessage);
        self::assertArrayHasKey('order', $obModel->belongsTo, $sErrorMessage);
        self::assertEquals(Order::class, array_shift($obModel->belongsTo['order']), $sErrorMessage);
    }

    /**
     * Check model "offer" relation config
     */
    public function testHasOfferRelation()
    {
        $sErrorMessage = $this->sModelClass.' model has not correct "offer" relation config';

        /** @var OrderPosition $obModel */
        $obModel = new OrderPosition();
        self::assertNotEmpty($obModel->belongsTo, $sErrorMessage);
        self::assertArrayHasKey('offer', $obModel->belongsTo, $sErrorMessage);
        self::assertEquals(Offer::class, array_shift($obModel->belongsTo['offer']), $sErrorMessage);
    }
}