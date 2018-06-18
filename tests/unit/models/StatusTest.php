<?php namespace Lovata\OrdersShopaholic\Tests\Unit\Models;

include_once __DIR__.'/../../../../toolbox/vendor/autoload.php';
include_once __DIR__.'/../../../../../../tests/PluginTestCase.php';

use PluginTestCase;
use Lovata\OrdersShopaholic\Models\Order;
use Lovata\OrdersShopaholic\Models\Status;
use Lovata\Toolbox\Traits\Tests\TestModelValidationNameField;

/**
 * Class StatusTest
 * @package Lovata\OrdersShopaholic\Tests\Unit\Models
 * @author Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 *
 * @mixin \PHPUnit\Framework\Assert
 */
class StatusTest extends PluginTestCase
{
    use TestModelValidationNameField;

    protected $sModelClass;

    /**
     * StatusTest constructor.
     */
    public function __construct()
    {
        $this->sModelClass = Status::class;
        parent::__construct();
    }

    /**
     * Check model "images" config
     */
    public function testHasValidationSlugField()
    {
        //Create model object
        /** @var Status $obModel */
        $obModel = new $this->sModelClass();

        //Get validation rules array and check it
        $arValidationRules = $obModel->rules;
        self::assertNotEmpty($arValidationRules, $this->sModelClass.' model has empty validation rules array');

        //Check rules for "slug" field
        self::assertArrayHasKey('code', $arValidationRules, $this->sModelClass.' model not has validation rules for field "code"');
        self::assertNotEmpty($arValidationRules['code'], $this->sModelClass.' model not has validation rules for field "code"');

        $arValidationCondition = explode('|', $arValidationRules['code']);
        self::assertContains('required', $arValidationCondition,$this->sModelClass.' model not has validation rule "required" for field "code"');
        self::assertContains('unique:'.$obModel->table, $arValidationCondition,$this->sModelClass.' model not has validation rule "unique" for field "code"');
    }

    /**
     * Check model "order" relation config
     */
    public function testHasOrderRelation()
    {
        $sErrorMessage = $this->sModelClass.' model has not correct "order" relation config';

        /** @var Status $obModel */
        $obModel = new Status();
        self::assertNotEmpty($obModel->hasMany, $sErrorMessage);
        self::assertArrayHasKey('order', $obModel->hasMany, $sErrorMessage);
        self::assertEquals(Order::class, $obModel->hasMany['order'], $sErrorMessage);
    }
}
