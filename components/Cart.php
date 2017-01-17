<?php namespace Lovata\OrdersShopaholic\Components;

use Input;
use Cms\Classes\ComponentBase;
use Kharanenka\Helper\Result;
use Lovata\OrdersShopaholic\Classes\CartStore;

/**
 * Class Cart
 * @package Lovata\OrdersShopaholic\Components
 * @author Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class Cart extends ComponentBase
{
    public function componentDetails()
    {
        return [
            'name'          => 'lovata.ordersshopaholic::lang.component.cart_name',
            'description'   => 'lovata.ordersshopaholic::lang.component.cart_description',
        ];
    }

    /**
     * Add product to cart
     * @return array
     */
    public function onAdd()
    {
        $arRequestData = Input::get('cart');
        CartStore::add($arRequestData);
        return Result::get();
    }

    /**
     * Update cart
     * @return array
     */
    public function onUpdate()
    {
        $arRequestData = Input::get('cart');
        $bRewrite = Input::get('rewrite');
        $bIncrement = Input::get('increment');
        
        CartStore::update($arRequestData, $bRewrite, $bIncrement);
        return Result::get();
    }

    /**
     * Remove offers from cart
     * @return array
     */
    public function onRemove()
    {
        $arRequestData = Input::get('cart');
        CartStore::remove($arRequestData);
        return Result::get();
    }

    /**
     * Clear cart
     */
    public function onClear()
    {
        CartStore::clear();
    }

    /**
     * Get offers list from cart
     * @return array
     */
    public function get()
    {
        return CartStore::get();
    }

    /**
     * Get cart info
     * @return array
     */
    public function getInfo()
    {
        return CartStore::getInfo();
    }
}