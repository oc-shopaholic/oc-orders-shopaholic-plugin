<?php namespace Lovata\OrdersShopaholic\Tests\Unit\Models;

include_once __DIR__.'/../../../../toolbox/vendor/autoload.php';
include_once __DIR__.'/../../../../../../tests/PluginTestCase.php';

use PluginTestCase;
use Lovata\Buddies\Models\User;
use Lovata\Shopaholic\Models\Offer;
use Lovata\OrdersShopaholic\Models\Order;
use Lovata\OrdersShopaholic\Models\Status;
use Lovata\OrdersShopaholic\Models\OrderPosition;
use Lovata\OrdersShopaholic\Models\PaymentMethod;
use Lovata\OrdersShopaholic\Models\ShippingType;

/**
 * Class OrderTest
 * @package Lovata\OrdersShopaholic\Tests\Unit\Models
 * @author Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 *
 * @mixin \PHPUnit\Framework\Assert
 */
class OrderTest extends PluginTestCase
{
    protected $sModelClass;

    /**
     * OrderTest constructor.
     */
    public function __construct()
    {
        $this->sModelClass = Order::class;
        parent::__construct();
    }

    /**
     * Check model "order_position" relation config
     */
    public function testHasOrderPositionRelation()
    {
        $sErrorMessage = $this->sModelClass.' model has not correct "order_position" relation config';

        /** @var Order $obModel */
        $obModel = new Order();
        self::assertNotEmpty($obModel->hasMany, $sErrorMessage);
        self::assertArrayHasKey('order_position', $obModel->hasMany, $sErrorMessage);
        self::assertEquals(OrderPosition::class, array_shift($obModel->hasMany['order_position']), $sErrorMessage);

        self::assertArrayHasKey('order_offer', $obModel->hasMany, $sErrorMessage);
        self::assertEquals(OrderPosition::class, array_shift($obModel->hasMany['order_offer']), $sErrorMessage);
    }

    /**
     * Check model "status" relation config
     */
    public function testHasStatusRelation()
    {
        $sErrorMessage = $this->sModelClass.' model has not correct "status" relation config';

        /** @var Order $obModel */
        $obModel = new Order();
        self::assertNotEmpty($obModel->belongsTo, $sErrorMessage);
        self::assertArrayHasKey('status', $obModel->belongsTo, $sErrorMessage);
        self::assertEquals(Status::class, array_shift($obModel->belongsTo['status']), $sErrorMessage);
    }

    /**
     * Check model "user" relation config
     */
    public function testHasUserRelation()
    {
        $sErrorMessage = $this->sModelClass.' model has not correct "user" relation config';

        /** @var Order $obModel */
        $obModel = new Order();
        self::assertNotEmpty($obModel->belongsTo, $sErrorMessage);
        self::assertArrayHasKey('user', $obModel->belongsTo, $sErrorMessage);
        self::assertEquals(User::class, array_shift($obModel->belongsTo['user']), $sErrorMessage);
    }

    /**
     * Check model "payment_method" relation config
     */
    public function testHasPaymentMethodRelation()
    {
        $sErrorMessage = $this->sModelClass.' model has not correct "payment_method" relation config';

        /** @var Order $obModel */
        $obModel = new Order();
        self::assertNotEmpty($obModel->belongsTo, $sErrorMessage);
        self::assertArrayHasKey('payment_method', $obModel->belongsTo, $sErrorMessage);
        self::assertEquals(PaymentMethod::class, array_shift($obModel->belongsTo['payment_method']), $sErrorMessage);
    }

    /**
     * Check model "shipping_type" relation config
     */
    public function testHasShippingTypeRelation()
    {
        $sErrorMessage = $this->sModelClass.' model has not correct "shipping_type" relation config';

        /** @var Order $obModel */
        $obModel = new Order();
        self::assertNotEmpty($obModel->belongsTo, $sErrorMessage);
        self::assertArrayHasKey('shipping_type', $obModel->belongsTo, $sErrorMessage);
        self::assertEquals(ShippingType::class, array_shift($obModel->belongsTo['shipping_type']), $sErrorMessage);
    }
}