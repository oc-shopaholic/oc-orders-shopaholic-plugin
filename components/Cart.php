<?php namespace Lovata\OrdersShopaholic\Components;

use Lang;
use Input;
use Cms\Classes\ComponentBase;
use Kharanenka\Helper\Result;

use Lovata\Toolbox\Traits\Helpers\TraitValidationHelper;

use Lovata\Shopaholic\Classes\Helper\CurrencyHelper;
use Lovata\OrdersShopaholic\Classes\Item\PaymentMethodItem;
use Lovata\OrdersShopaholic\Classes\Item\ShippingTypeItem;
use Lovata\OrdersShopaholic\Classes\Processor\CartProcessor;
use Lovata\OrdersShopaholic\Classes\Processor\OfferCartPositionProcessor;

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
    use TraitValidationHelper;

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
     * Init component data
     */
    public function init()
    {
        $obActiveShippingType = $this->getActiveShippingTypeFromRequest();
        if (!empty($obActiveShippingType) && $obActiveShippingType->isNotEmpty()) {
            CartProcessor::instance()->setActiveShippingType($obActiveShippingType);
        }

        $obActivePaymentMethod = $this->getActivePaymentMethodFromRequest();
        if (!empty($obActivePaymentMethod) && $obActivePaymentMethod->isNotEmpty()) {
            CartProcessor::instance()->setActivePaymentMethod($obActivePaymentMethod);
        }
    }

    /**
     * Add product to cart
     * @return array
     */
    public function onAdd()
    {
        $arRequestData = Input::get('cart');

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
        $sType = Input::get('type', 'offer');

        CartProcessor::instance()->remove($arRequestData, OfferCartPositionProcessor::class, $sType);
        Result::setData(CartProcessor::instance()->getCartData());

        return Result::get();
    }

    /**
     * Restore cart position
     * @return array
     */
    public function onRestore()
    {
        $arRequestData = Input::get('cart');

        CartProcessor::instance()->restore($arRequestData, OfferCartPositionProcessor::class);
        Result::setData(CartProcessor::instance()->getCartData());

        return Result::get();
    }

    /**
     * Sync cart positions
     * @return array
     */
    public function onSync()
    {
        $arRequestData = Input::get('cart');

        CartProcessor::instance()->sync($arRequestData, OfferCartPositionProcessor::class);
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
     * Set active shipping type with using AJAX request
     * @deprecated
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
        $obShippingTypeItem = ShippingTypeItem::make($iShippingTypeID);
        if ($obShippingTypeItem->isEmpty()) {
            $sMessage = Lang::get('lovata.toolbox::lang.message.e_not_correct_request');
            Result::setFalse()->setMessage($sMessage);
            return Result::get();
        }

        CartProcessor::instance()->setActiveShippingType($obShippingTypeItem);
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
     * Get cart data with using Result::get method
     * @return array
     */
    public function onGetCartData()
    {
        return Result::setData(CartProcessor::instance()->getCartData())->get();
    }

    /**
     * Update cart
     * @return array
     */
    public function onSaveData()
    {
        $arUserData = (array) Input::get('user');
        $arCartProperty = (array) Input::get('property');
        $arBillingAddress = (array) Input::get('billing_address');
        $arShippingAddress = (array) Input::get('shipping_address');

        $obCart = CartProcessor::instance()->getCartObject();

        try {

            $obCart->user_data = array_merge((array) $obCart->user_data, $arUserData);
            $obCart->email = array_get($obCart->user_data, 'email');

            $obCart->property = array_merge((array) $obCart->property, $arCartProperty);

            $obCart->billing_address = array_merge((array) $obCart->billing_address, $arBillingAddress);
            $obCart->shipping_address = array_merge((array) $obCart->shipping_address, $arShippingAddress);

            $obCart->shipping_type_id = Input::get('shipping_type_id', $obCart->shipping_type_id);
            $obCart->payment_method_id = Input::get('payment_method_id', $obCart->payment_method_id);

            $obCart->save();
        } catch (\October\Rain\Database\ModelException $obException) {
            $this->processValidationError($obException);
        }

        return Result::get();
    }

    /**
     * Get offers list from cart
     * @param \Lovata\OrdersShopaholic\Classes\Item\ShippingTypeItem $obShippingTypeItem
     * @param \Lovata\OrdersShopaholic\Classes\Item\PaymentMethodItem $obPaymentMethodItem
     * @return \Lovata\OrdersShopaholic\Classes\Collection\CartPositionCollection
     */
    public function get($obShippingTypeItem = null, $obPaymentMethodItem = null)
    {
        CartProcessor::instance()->setActiveShippingType($obShippingTypeItem);
        CartProcessor::instance()->setActivePaymentMethod($obPaymentMethodItem);

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
     * @return \Lovata\OrdersShopaholic\Classes\PromoMechanism\TotalPriceContainer
     */
    public function getTotalPriceData()
    {
        $obPriceData = CartProcessor::instance()->getCartTotalPriceData();

        return $obPriceData;
    }

    /**
     * Get currency symbol
     * @return null|string
     */
    public function getCurrency()
    {
        return CurrencyHelper::instance()->getActiveCurrencySymbol();
    }

    /**
     * Get currency code
     * @return null|string
     */
    public function getCurrencyCode()
    {
        return CurrencyHelper::instance()->getActiveCurrencyCode();
    }

    /**
     * Get active shipping type from request
     * @return ShippingTypeItem
     */
    public function getActiveShippingTypeFromRequest()
    {
        $iShippingTypeID = Input::get('shipping_type_id');
        if (empty($iShippingTypeID)) {
            return null;
        }

        //Get shipping type item
        $obShippingTypeItem = ShippingTypeItem::make($iShippingTypeID);

        return $obShippingTypeItem;
    }

    /**
     * Get active payment method from request
     * @return PaymentMethodItem
     */
    public function getActivePaymentMethodFromRequest()
    {
        $iPaymentMethodID = Input::get('payment_method_id');
        if (empty($iPaymentMethodID)) {
            return null;
        }

        //Get shipping type item
        $obPaymentMethodItem = PaymentMethodItem::make($iPaymentMethodID);

        return $obPaymentMethodItem;
    }
}
