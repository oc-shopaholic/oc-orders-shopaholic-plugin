<?php namespace Lovata\OrdersShopaholic\Components;

use Input;
use Cms\Classes\ComponentBase;
use Kharanenka\Helper\Result;

use Lovata\OrdersShopaholic\Classes\Processor\CartProcessor;
use Lovata\OrdersShopaholic\Classes\Processor\OfferCartPositionProcessor;

/**
 * Class Cart
 * @package Lovata\OrdersShopaholic\Components
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class Cart extends ComponentBase
{
    /** @var  CartProcessor */
    protected $obCartData;

    /**
     * @return array
     */
    public function componentDetails()
    {
        return [
            'name'        => 'lovata.ordersshopaholic::lang.component.cart_name',
            'description' => 'lovata.ordersshopaholic::lang.component.cart_description',
        ];
    }

    /**
     * Init plugin method
     */
    public function init()
    {
        $this->obCartData = CartProcessor::instance();
    }

    /**
     * Add product to cart
     * @return array
     */
    public function onAdd()
    {
        $arRequestData = Input::get('cart');
        $this->obCartData->add($arRequestData, OfferCartPositionProcessor::class);

        return Result::get();
    }

    /**
     * Update cart
     * @return array
     */
    public function onUpdate()
    {
        $arRequestData = Input::get('cart');

        $this->obCartData->update($arRequestData, OfferCartPositionProcessor::class);

        return Result::get();
    }

    /**
     * Remove offers from cart
     * @return array
     */
    public function onRemove()
    {
        $arRequestData = Input::get('cart');
        $this->obCartData->remove($arRequestData, OfferCartPositionProcessor::class);

        return Result::get();
    }

    /**
     * Clear cart
     */
    public function onClear()
    {
        $this->obCartData->clear();
    }

    /**
     * Get offers list from cart
     * @return \Lovata\OrdersShopaholic\Classes\Collection\CartPositionCollection
     */
    public function get()
    {
        return $this->obCartData->get();
    }
}
