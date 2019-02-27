<?php namespace Lovata\OrdersShopaholic\Tests\Unit\Models;

use Lovata\Toolbox\Tests\CommonTest;

use Lovata\Buddies\Models\User;
use Lovata\OrdersShopaholic\Models\Order;

/**
 * Class UserTest
 * @package Lovata\OrdersShopaholic\Tests\Unit\Models
 * @author Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 *
 * @mixin \PHPUnit\Framework\Assert
 */
class UserTest extends CommonTest
{
    protected $sModelClass;

    /**
     * UserTest constructor.
     */
    public function __construct()
    {
        $this->sModelClass = User::class;
        parent::__construct();
    }

    /**
     * Check model "order" relation config
     */
    public function testHasOrderRelation()
    {
        $sErrorMessage = $this->sModelClass.' model has not correct "order" relation config';

        /** @var User $obModel */
        $obModel = new User();
        self::assertNotEmpty($obModel->hasMany, $sErrorMessage);
        self::assertArrayHasKey('order', $obModel->hasMany, $sErrorMessage);
        self::assertEquals(Order::class, array_shift($obModel->hasMany['order']), $sErrorMessage);
    }
}
