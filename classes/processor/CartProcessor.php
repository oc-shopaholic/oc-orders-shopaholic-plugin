<?php namespace Lovata\OrdersShopaholic\Classes\Processor;

use Lang;
use Cookie;
use October\Rain\Support\Traits\Singleton;
use Kharanenka\Helper\Result;

use Lovata\Toolbox\Classes\Helper\UserHelper;
use Lovata\Shopaholic\Models\Settings;
use Lovata\OrdersShopaholic\Models\Cart;
use Lovata\OrdersShopaholic\Models\CartPosition;
use Lovata\OrdersShopaholic\Classes\Collection\CartPositionCollection;

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

    /** @var  CartPositionCollection */
    protected $obCartPositionList;

    /**
     * Add position list in current cart
     * @param array $arPositionList
     * @param string $sPositionProcessor
     * @return bool1
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

        return $this->prepareSuccessResponse();
    }

    /**
     * Updates position data in current cart (rewrite).
     * @param array $arPositionList
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

        return $this->prepareSuccessResponse();
    }

    /**
     * Remove position from current cart
     * @param array $arPositionList
     * @param string  $sPositionProcessor
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
    }

    /**
     * Get cart item collection
     * @return CartPositionCollection
     */
    public function get()
    {
        if (empty($this->obCart)) {
            return CartPositionCollection::make();
        }

        if (!empty($this->obCartPositionList)) {
            return $this->obCartPositionList;
        }

        /** @var array $arCartPositionIDList */
        $arCartPositionIDList = CartPosition::getByCart($this->obCart->id)->lists('id');
        $this->obCartPositionList = CartPositionCollection::make($arCartPositionIDList);

        return $this->obCartPositionList;
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
     * @param array $arPositionList
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
