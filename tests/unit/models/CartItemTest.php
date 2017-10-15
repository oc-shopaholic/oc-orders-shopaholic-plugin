<?php namespace Lovata\OrdersShopaholic\Tests\Unit\Models;

include_once __DIR__.'/../../../../toolbox/vendor/autoload.php';
include_once __DIR__.'/../../../../../../tests/PluginTestCase.php';

use PluginTestCase;
use Lovata\Shopaholic\Models\Offer;
use Lovata\OrdersShopaholic\Models\Cart;
use Lovata\OrdersShopaholic\Models\CartElement;

/**
 * Class OrderTest
 * @package Lovata\OrdersShopaholic\Tests\Unit\Models
 * @author Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 *
 * @mixin \PHPUnit\Framework\Assert
 */
class CartElementTest extends PluginTestCase
{
    protected $sModelClass;

    /**
     * CartElementTest constructor.
     */
    public function __construct()
    {
        $this->sModelClass = CartElement::class;
        parent::__construct();
    }

    /**
     * Check model "offer" relation config
     */
    public function testHasOfferRelation()
    {
        $sErrorMessage = $this->sModelClass.' model has not correct "offer" relation config';

        /** @var CartElement $obModel */
        $obModel = new CartElement();
        self::assertNotEmpty($obModel->belongsTo, $sErrorMessage);
        self::assertArrayHasKey('offer', $obModel->belongsTo, $sErrorMessage);
        self::assertEquals(Offer::class, array_shift($obModel->belongsTo['offer']), $sErrorMessage);
    }

    /**
     * Check model "cart" relation config
     */
    public function testHasCartRelation()
    {
        $sErrorMessage = $this->sModelClass.' model has not correct "cart" relation config';

        /** @var CartElement $obModel */
        $obModel = new CartElement();
        self::assertNotEmpty($obModel->belongsTo, $sErrorMessage);
        self::assertArrayHasKey('cart', $obModel->belongsTo, $sErrorMessage);
        self::assertEquals(Cart::class, array_shift($obModel->belongsTo['cart']), $sErrorMessage);
    }
}