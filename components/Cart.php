<?php namespace Lovata\OrdersShopaholic\Components;

use Lang;
use Input;
use Cms\Classes\ComponentBase;
use Kharanenka\Helper\Result;

use Lovata\Shopaholic\Classes\Helper\CurrencyHelper;
use Lovata\OrdersShopaholic\Classes\Processor\CartProcessor;
use Lovata\OrdersShopaholic\Classes\Processor\OfferCartPositionProcessor;
use Lovata\OrdersShopaholic\Models\ShippingType;

/**
 * Class Cart
 * @package Lovata\OrdersShopaholic\Components
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 *
 * Campaigns for Shopaholic plugin
 * @method \Lovata\CampaignsShopaholic\Classes\Item\CampaignItem[]|\Lovata\CampaignsShopaholic\Classes\Collection\CampaignCollection getAppliedCampaignList()
 *
 * Coupons for Shopaholic plugin
 * @method array onAddCoupon()
 * @method array onRemoveCoupon()
 * @method array onClearCouponList()
 * @method \Lovata\CouponsShopaholic\Classes\Item\CouponItem[]|\Lovata\CouponsShopaholic\Classes\Collection\CouponCollection getAppliedCouponList()
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
        $obActiveShippingType = $this->getActiveShippingTypeFromRequest();
        if (!empty($obActiveShippingType)) {
            CartProcessor::instance()->setActiveShippingType($obActiveShippingType);
        }
        
        CartProcessor::instance()->add($arRequestData, OfferCartPositionProcessor::class);
        Result::setData(CartProcessor::instance()->getCartData());

        return Result::get();
    }

    /**
     * Update cart
     * @return array
     */
    public function onUpdate()
    {
        $arRequestData = Input::get('cart');
        $obActiveShippingType = $this->getActiveShippingTypeFromRequest();
        if (!empty($obActiveShippingType)) {
            CartProcessor::instance()->setActiveShippingType($obActiveShippingType);
        }

        CartProcessor::instance()->update($arRequestData, OfferCartPositionProcessor::class);
        Result::setData(CartProcessor::instance()->getCartData());

        return Result::get();
    }

    /**
     * Remove offers from cart
     * @return array
     */
    public function onRemove()
    {
        $arRequestData = Input::get('cart');
        $obActiveShippingType = $this->getActiveShippingTypeFromRequest();
        if (!empty($obActiveShippingType)) {
            CartProcessor::instance()->setActiveShippingType($obActiveShippingType);
        }

        CartProcessor::instance()->remove($arRequestData, OfferCartPositionProcessor::class);
        Result::setData(CartProcessor::instance()->getCartData());

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
        Result::setData(CartProcessor::instance()->getCartData());

        return Result::get();
    }

    /**
     * Get cart data (ajax request)
     * @return array
     */
    public function onGetData()
    {
        return CartProcessor::instance()->getCartData();
    }

    /**
     * Get offers list from cart
     * @param ShippingType|\Lovata\OrdersShopaholic\Classes\Item\ShippingTypeItem $obShippingType
     * @return \Lovata\OrdersShopaholic\Classes\Collection\CartPositionCollection
     */
    public function get($obShippingType = null)
    {
        CartProcessor::instance()->setActiveShippingType($obShippingType);

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
     * Get old total price string
     * @return string
     */
    public function getOldTotalPrice()
    {
        $obPriceData = $this->getTotalPriceData();

        return $obPriceData->old_price;
    }

    /**
     * Get old total price value
     * @return float
     */
    public function getOldTotalPriceValue()
    {
        $obPriceData = $this->getTotalPriceData();

        return $obPriceData->old_price_value;
    }

    /**
     * Get discount total price string
     * @return string
     */
    public function getDiscountTotalPrice()
    {
        $obPriceData = $this->getTotalPriceData();

        return $obPriceData->discount_price;
    }

    /**
     * Get discount total price value
     * @return float
     */
    public function getDiscountTotalPriceValue()
    {
        $obPriceData = $this->getTotalPriceData();

        return $obPriceData->discount_price_value;
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

    /**
     * Get currency value
     * @return null|string
     */
    public function getCurrency()
    {
        return CurrencyHelper::instance()->getActive();
    }

    /**
     * Get active shipping type from request
     * @return ShippingType|null
     */
    protected function getActiveShippingTypeFromRequest()
    {
        $iShippingTypeID = Input::get('shipping_type_id');
        if (empty($iShippingTypeID)) {
            return null;
        }

        //Get shipping type object
        $obShippingType = ShippingType::active()->find($iShippingTypeID);

        return $obShippingType;
    }
}
