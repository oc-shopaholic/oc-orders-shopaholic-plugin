<?php namespace Lovata\OrdersShopaholic\Tests\Unit\Processor;

use Lang;
use Kharanenka\Helper\Result;
use Lovata\Toolbox\Tests\CommonTest;

use Lovata\Buddies\Models\User;
use Lovata\Shopaholic\Models\Offer;
use Lovata\Shopaholic\Models\Product;

use Lovata\OrdersShopaholic\Models\Cart;
use Lovata\OrdersShopaholic\Classes\Collection\CartPositionCollection;
use Lovata\OrdersShopaholic\Classes\Item\CartPositionItem;
use Lovata\OrdersShopaholic\Classes\Processor\CartProcessor;
use Lovata\OrdersShopaholic\Classes\Processor\OfferCartPositionProcessor;

/**
 * Class WithoutConditionDiscountPositionTest
 * @package Lovata\OrdersShopaholic\Tests\Unit\Processor
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 *
 * @mixin \PHPUnit\Framework\Assert
 */
class WithoutConditionDiscountPositionTest extends CommonTest
{
    /** @var  Cart */
    protected $obElement;

    /** @var  Product */
    protected $obProduct;

    /** @var  Offer */
    protected $obOffer;

    /** @var  User */
    protected $obUser;

    protected $arProductData = [
        'active'       => true,
        'name'         => 'name',
        'slug'         => 'slug',
        'code'         => 'code',
        'preview_text' => 'preview_text',
        'description'  => 'description',
    ];

    protected $arOfferData = [
        'active'       => true,
        'name'         => 'name',
        'code'         => 'code',
        'preview_text' => 'preview_text',
        'description'  => 'description',
        'price'        => '10,55',
        'old_price'    => '11,50',
        'quantity'     => 5,
    ];

    protected $arUserData = [
        'email'                 => 'email@email.com',
        'name'                  => 'name',
        'last_name'             => 'last_name',
        'middle_name'           => 'middle_name',
        'phone_list'            => ['123', '321'],
        'property'              => ['birthday' => '2017-10-21'],
        'password'              => 'test',
        'password_confirmation' => 'test',
    ];

    public function setUp()
    {
        parent::setUp();
        $this->runPluginRefreshCommand('lovata.popularityshopaholic', false);
    }


    /**
     * Create shipping type object for test
     */
    protected function createTestData()
    {
        //Create product data
        $arCreateData = $this->arProductData;
        $this->obProduct = Product::create($arCreateData);

        //Create offer data
        $arCreateData = $this->arOfferData;
        $arCreateData['product_id'] = $this->obProduct->id;
        $this->obOffer = Offer::create($arCreateData);

        $arCreateData = $this->arUserData;

        $this->obUser = User::create($arCreateData);
        $this->obUser = User::find($this->obUser->id);
    }
}
