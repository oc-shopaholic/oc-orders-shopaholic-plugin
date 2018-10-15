<?php namespace Lovata\OrdersShopaholic\Classes\Processor;

use Lang;
use Cookie;
use October\Rain\Support\Traits\Singleton;
use Kharanenka\Helper\Result;

use Lovata\Toolbox\Classes\Helper\UserHelper;
use Lovata\Shopaholic\Models\Settings;
use Lovata\OrdersShopaholic\Models\Cart;
use Lovata\OrdersShopaholic\Models\CartPosition;
use Lovata\OrdersShopaholic\Classes\Item\ShippingTypeItem;
use Lovata\OrdersShopaholic\Classes\PromoMechanism\PriceContainer;
use Lovata\OrdersShopaholic\Classes\Collection\CartPositionCollection;
use Lovata\OrdersShopaholic\Classes\PromoMechanism\CartPromoMechanismProcessor;

/**
 * Class CartProcessor
 * @package Lovata\OrdersShopaholic\Classes\Processor
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class CartProcessor
{
    use Singleton;

    const COOKIE_NAME = 'shopaholic_cart_id';

    /** @var int - Cart cookie life time */
    public static $iCookieLifeTime = 2419200;

    /** @var int - cart ID for unit tests */
    public static $iTestCartID = null;

    /** @var Cart */
    protected $obCart = null;

    /** @var \Lovata\Buddies\Models\User */
    protected $obUser = null;

    /** @var  CartPositionCollection|\Lovata\OrdersShopaholic\Classes\Item\CartPositionItem[] */
    protected $obCartPositionList;

    /** @var \Lovata\OrdersShopaholic\Models\ShippingType */
    protected $obShippingType;

    /** @var CartPromoMechanismProcessor */
    protected $obPromoProcessor;

    /**
     * Get cart object
     * @return Cart
     */
    public function getCartObject()
    {
        return $this->obCart;
    }

    /**
     * Init new cart positions and promo processor
     */
    public function updateCartData()
    {
        $this->initCartPositionList();
        $this->initPromoProcessor();
    }

    /**
     * Add position list in current cart
     * @param array  $arPositionList
     * @param string $sPositionProcessor
     * @return bool
     */
    public function add($arPositionList, $sPositionProcessor)
    {
        if (!$this->validateRequest($arPositionList, $sPositionProcessor)) {
            return false;
        }

        /** @var AbstractCartPositionProcessor $obPositionProcessor */
        $obPositionProcessor = app($sPositionProcessor, [$this->obCart, $this->obUser]);

        //Process position list and add position to cart
        foreach ($arPositionList as $arPositionData) {
            $obPositionProcessor->add($arPositionData);
        }

        $this->updateCartData();

        return $this->prepareSuccessResponse();
    }

    /**
     * Updates position data in current cart (rewrite).
     * @param array  $arPositionList
     * @param string $sPositionProcessor
     * @return bool
     */
    public function update($arPositionList, $sPositionProcessor)
    {
        if (!$this->validateRequest($arPositionList, $sPositionProcessor)) {
            return false;
        }

        /** @var AbstractCartPositionProcessor $obPositionProcessor */
        $obPositionProcessor = app($sPositionProcessor, [$this->obCart, $this->obUser]);

        //Process position list and update position data
        foreach ($arPositionList as $arPositionData) {
            $obPositionProcessor->update($arPositionData);
        }

        $this->updateCartData();

        return $this->prepareSuccessResponse();
    }

    /**
     * Remove position from current cart
     * @param array  $arPositionList
     * @param string $sPositionProcessor
     * @return bool
     * @throws
     */
    public function remove($arPositionList, $sPositionProcessor)
    {
        if (!$this->validateRequest($arPositionList, $sPositionProcessor)) {
            return false;
        }

        /** @var AbstractCartPositionProcessor $obPositionProcessor */
        $obPositionProcessor = app($sPositionProcessor, [$this->obCart, $this->obUser]);

        //Process position list and remove position from cart
        foreach ($arPositionList as $iPositionID) {
            $obPositionProcessor->remove($iPositionID);
        }

        $this->updateCartData();

        return $this->prepareSuccessResponse();
    }

    /**
     * Clear cart
     */
    public function clear()
    {
        if (empty($this->obCart)) {
            return;
        }

        $obCartPositionList = CartPosition::getByCart($this->obCart->id)->get();
        if ($obCartPositionList->isEmpty()) {
            return;
        }

        foreach ($obCartPositionList as $obCartPosition) {
            $obCartPosition->delete();
        }

        $this->obCartPositionList = null;

        $this->updateCartData();
    }

    /**
     * Get cart item collection
     * @return CartPositionCollection|\Lovata\OrdersShopaholic\Classes\Item\CartPositionItem[]
     */
    public function get()
    {
        if (!empty($this->obCartPositionList)) {
            return $this->obCartPositionList;
        }

        $this->updateCartData();

        return $this->obCartPositionList;
    }

    /**
     * Set active shipping type
     * @param \Lovata\OrdersShopaholic\Models\ShippingType|\Lovata\OrdersShopaholic\Classes\Item\ShippingTypeItem $obShippingType
     */
    public function setActiveShippingType($obShippingType)
    {
        if (!empty($obShippingType) && $obShippingType instanceof ShippingTypeItem) {
            $obShippingType = $obShippingType->getObject();
        }

        $this->obShippingType = $obShippingType;

        $this->updateCartData();
    }

    /**
     * Get cart position price data
     * @param int $iPositionID
     * @return PriceContainer
     */
    public function getCartPositionPriceData($iPositionID) : PriceContainer
    {
        if (empty($this->obPromoProcessor)) {
            $this->updateCartData();
        }

        $obPriceData = $this->obPromoProcessor->getPositionPrice($iPositionID);

        return $obPriceData;
    }

    /**
     * Get cart position total price data
     * @return PriceContainer
     */
    public function getCartPositionTotalPriceData() : PriceContainer
    {
        if (empty($this->obPromoProcessor)) {
            $this->updateCartData();
        }

        $obPriceData = $this->obPromoProcessor->getPositionTotalPrice();

        return $obPriceData;
    }

    /**
     * Get shipping price data
     * @return PriceContainer
     */
    public function getShippingPriceData() : PriceContainer
    {
        if (empty($this->obPromoProcessor)) {
            $this->updateCartData();
        }

        $obPriceData = $this->obPromoProcessor->getShippingPrice();

        return $obPriceData;
    }

    /**
     * Get cart total price data
     * @return PriceContainer
     */
    public function getCartTotalPriceData() : PriceContainer
    {
        if (empty($this->obPromoProcessor)) {
            $this->updateCartData();
        }

        $obPriceData = $this->obPromoProcessor->getTotalPrice();

        return $obPriceData;
    }

    /**
     * Get cart data
     * @return array
     */
    public function getCartData()
    {
        $obCartPositionList = $this->get();

        $arResult = [
            'position'             => [],
            'shipping_type_id'     => null,
            'shipping_price'       => $this->getShippingPriceData()->getData(),
            'position_total_price' => $this->getCartPositionTotalPriceData()->getData(),
            'total_price'          => $this->getCartTotalPriceData()->getData(),
            'quantity'             => 0,
            'total_quantity'       => 0,
        ];

        if ($obCartPositionList->isEmpty()) {
            return $arResult;
        }

        foreach ($obCartPositionList as $obCartPositionItem) {
            $arPositionData = [
                'id'           => $obCartPositionItem->id,
                'item_id'      => $obCartPositionItem->item_id,
                'item_type'    => $obCartPositionItem->item_type,
                'quantity'     => (int) $obCartPositionItem->quantity,
                'max_quantity' => (int) $obCartPositionItem->item->quantity,
                'property'     => $obCartPositionItem->property,
            ];

            $arPositionData = $this->getCartPositionPriceData($obCartPositionItem->id)->getData($arPositionData);

            $arResult['quantity']++;
            $arResult['total_quantity'] += $obCartPositionItem->quantity;
            $arResult['position'][$obCartPositionItem->id] = $arPositionData;
        }

        return $arResult;
    }

    /**Init cart data
     */
    protected function init()
    {
        //Get cart cookie life time
        $iCookieLifeTime = Settings::getValue('cart_cookie_lifetime');
        if (!empty($iCookieLifeTime) && $iCookieLifeTime > 0) {
            self::$iCookieLifeTime = $iCookieLifeTime;
        }

        //Get cart id from cookie, if exists
        $iCartID = Cookie::get(self::COOKIE_NAME, self::$iTestCartID);

        //Get auth user
        $this->obUser = UserHelper::instance()->getUser();

        $this->obCart = null;
        if (!empty($this->obUser)) {
            //Find cart for authorized user
            $this->findUserCart($iCartID);
        } else if (!empty($iCartID)) {
            //Find card by ID
            $this->obCart = Cart::with('position')->find($iCartID);
        }

        //Create new cart
        if (empty($this->obCart)) {
            $this->createNewCart();
        }
    }

    /**
     * Init promo processor
     */
    protected function initPromoProcessor()
    {
        $this->obPromoProcessor = new CartPromoMechanismProcessor($this->obCart, $this->obCartPositionList, $this->obShippingType);
    }

    /**
     * Init cart position list
     */
    protected function initCartPositionList()
    {
        if (empty($this->obCart)) {
            $this->obCartPositionList = CartPositionCollection::make();
        }

        /** @var array $arCartPositionIDList */
        $arCartPositionIDList = CartPosition::getByCart($this->obCart->id)->lists('id');
        $this->obCartPositionList = CartPositionCollection::make($arCartPositionIDList);
    }

    /**
     * Find user cart
     * @param int $iCartID
     * @throws
     */
    protected function findUserCart($iCartID)
    {
        //Get cart by user ID
        $this->obCart = Cart::with('position')->getByUser($this->obUser->id)->first();
        if (empty($this->obCart) || $this->obCart->id == $iCartID) {
            return;
        }

        //Set new user cart ID in cookie
        Cookie::queue(self::COOKIE_NAME, $this->obCart->id, self::$iCookieLifeTime);

        //Get guest cart
        /** @var Cart $obGuestCart */
        $obGuestCart = Cart::with('position')->find($iCartID);
        if (empty($obGuestCart)) {
            return;
        }

        //get user's cart items before auth
        $obGuestCartPositionList = $obGuestCart->position;
        if ($obGuestCartPositionList->isEmpty()) {
            return;
        }

        //Move guest cart items to user cart
        foreach ($obGuestCartPositionList as $obCartPosition) {
            $obCartPosition->cart_id = $this->obCart->id;
            $obCartPosition->save();
        }

        //Remove guest cart
        $obGuestCart->delete();
    }

    /**
     * Create new cart
     * @return void
     */
    protected function createNewCart()
    {
        $iUserID = null;
        if (!empty($this->obUser)) {
            $iUserID = $this->obUser->id;
        }

        $this->obCart = Cart::create(['user_id' => $iUserID]);
        Cookie::queue(self::COOKIE_NAME, $this->obCart->id, self::$iCookieLifeTime);
    }

    /**
     * Validate request data
     * @param array  $arPositionList
     * @param string $sPositionProcessor
     * @return bool
     */
    protected function validateRequest($arPositionList, $sPositionProcessor)
    {
        if (empty($this->obCart) || empty($arPositionList) || !is_array($arPositionList) || empty($sPositionProcessor)) {
            $sMessage = Lang::get('lovata.toolbox::lang.message.e_not_correct_request');
            Result::setFalse()->setMessage($sMessage);
            return false;
        }

        return true;
    }

    /**
     * Prepare success response
     * @return bool
     */
    protected function prepareSuccessResponse()
    {
        $this->obCartPositionList = null;
        Result::setTrue();
        return true;
    }
}
