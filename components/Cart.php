<?php namespace Lovata\OrdersShopaholic\Components;

use Input;
use Cms\Classes\ComponentBase;
use Kharanenka\Helper\Result;

use Lovata\OrdersShopaholic\Classes\Processor\CartProcessor;
use Lovata\OrdersShopaholic\Classes\Processor\OfferCartPositionProcessor;
use Lovata\OrdersShopaholic\Models\ShippingType;

/**
 * Class Cart
 * @package Lovata\OrdersShopaholic\Components
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class Cart extends ComponentBase
{
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
     * Add product to cart
     * @return array
     */
    public function onAdd()
    {
        $arRequestData = Input::get('cart');
        CartProcessor::instance()->add($arRequestData, OfferCartPositionProcessor::class);
        if (Result::status()) {
            Result::setTrue(CartProcessor::instance()->getCartData());
        }

        return Result::get();
    }

    /**
     * Update cart
     * @return array
     */
    public function onUpdate()
    {
        $arRequestData = Input::get('cart');

        CartProcessor::instance()->update($arRequestData, OfferCartPositionProcessor::class);
        if (Result::status()) {
            Result::setTrue(CartProcessor::instance()->getCartData());
        }

        return Result::get();
    }

    /**
     * Remove offers from cart
     * @return array
     */
    public function onRemove()
    {
        $arRequestData = Input::get('cart');
        CartProcessor::instance()->remove($arRequestData, OfferCartPositionProcessor::class);
        if (Result::status()) {
            Result::setTrue(CartProcessor::instance()->getCartData());
        }

        return Result::get();
    }

    /**
     * Clear cart
     */
    public function onClear()
    {
        CartProcessor::instance()->clear();
    }

    /**
     * Clear cart
     */
    public function onSetShippingType()
    {
        $iShippingTypeID = Input::get('shipping_type_id');
        if (empty($iShippingTypeID)) {
            $sMessage = Lang::get('lovata.toolbox::lang.message.e_not_correct_request');
            Result::setFalse()->setMessage($sMessage);
            return Result::get();
        }

        //Get shipping type object
        $obShippingType = ShippingType::active()->find($iShippingTypeID);
        if (empty($obShippingType)) {
            $sMessage = Lang::get('lovata.toolbox::lang.message.e_not_correct_request');
            Result::setFalse()->setMessage($sMessage);
            return Result::get();
        }

        CartProcessor::instance()->setActiveShippingType($obShippingType);
        if (Result::status()) {
            Result::setTrue(CartProcessor::instance()->getCartData());
        }

        return Result::get();
    }

    /**
     * Get offers list from cart
     * @return \Lovata\OrdersShopaholic\Classes\Collection\CartPositionCollection
     */
    public function get()
    {
        return CartProcessor::instance()->get();
    }

    /**
     * Get total price string
     * @return string
     */
    public function getTotalPrice()
    {
        $obPriceData = $this->getTotalPriceData();

        return $obPriceData->price;
    }

    /**
     * Get total price value
     * @return float
     */
    public function getTotalPriceValue()
    {
        $obPriceData = $this->getTotalPriceData();

        return $obPriceData->price_value;
    }

    /**
     * Get total position price data
     * @return \Lovata\OrdersShopaholic\Classes\PromoMechanism\PriceContainer
     */
    public function getTotalPriceData()
    {
        $obPriceData = CartProcessor::instance()->getCartTotalPriceData();

        return $obPriceData;
    }
}
