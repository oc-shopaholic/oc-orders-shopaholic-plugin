<?php namespace Lovata\OrdersShopaholic\Components;

use Input;
use Cms\Classes\ComponentBase;
use Kharanenka\Helper\Result;
use Lovata\OrdersShopaholic\Classes\CartProcessor;

/**
 * Class Cart
 * @package Lovata\OrdersShopaholic\Components
 * @author Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
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
            'name'          => 'lovata.ordersshopaholic::lang.component.cart_name',
            'description'   => 'lovata.ordersshopaholic::lang.component.cart_description',
        ];
    }

    /**
     * Init plugin method
     */
    public function init()
    {
        $this->obCartData = app()->make(CartProcessor::class);
    }

    /**
     * Add product to cart
     * @return array
     */
    public function onAdd()
    {
        $arRequestData = Input::get('cart');
        $this->obCartData->add($arRequestData);
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
        
        $this->obCartData->update($arRequestData, $bRewrite, $bIncrement);
        return Result::get();
    }

    /**
     * Remove offers from cart
     * @return array
     */
    public function onRemove()
    {
        $arRequestData = Input::get('cart');
        $this->obCartData->remove($arRequestData);
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
     * @return \Lovata\OrdersShopaholic\Classes\Collection\CartElementCollection
     */
    public function get()
    {
        return $this->obCartData->get();
    }
}