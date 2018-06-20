<?php namespace Lovata\OrdersShopaholic\Tests\Unit\Models;

include_once __DIR__.'/../../../../toolbox/vendor/autoload.php';
include_once __DIR__.'/../../../../../../tests/PluginTestCase.php';

use PluginTestCase;
use Lovata\OrdersShopaholic\Models\Cart;
use Lovata\OrdersShopaholic\Models\CartPosition;

/**
 * Class CartPositionTest
 * @package Lovata\OrdersShopaholic\Tests\Unit\Models
 * @author Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 *
 * @mixin \PHPUnit\Framework\Assert
 */
class CartPositionTest extends PluginTestCase
{
    protected $sModelClass;

    /**
     * CartPositionTest constructor.
     */
    public function __construct()
    {
        $this->sModelClass = CartPosition::class;
        parent::__construct();
    }

    /**
     * Check model "item" relation config
     */
    public function testHasItemRelation()
    {
        $sErrorMessage = $this->sModelClass.' model has not correct "item" relation config';

        /** @var CartPosition $obModel */
        $obModel = new CartPosition();
        self::assertNotEmpty($obModel->morphTo, $sErrorMessage);
        self::assertArrayHasKey('item', $obModel->morphTo, $sErrorMessage);
    }

    /**
     * Check model "cart" relation config
     */
    public function testHasCartRelation()
    {
        $sErrorMessage = $this->sModelClass.' model has not correct "cart" relation config';

        /** @var CartPosition $obModel */
        $obModel = new CartPosition();
        self::assertNotEmpty($obModel->belongsTo, $sErrorMessage);
        self::assertArrayHasKey('cart', $obModel->belongsTo, $sErrorMessage);
        self::assertEquals(Cart::class, array_shift($obModel->belongsTo['cart']), $sErrorMessage);
    }
}