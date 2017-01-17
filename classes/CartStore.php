<?php namespace Lovata\OrdersShopaholic\Classes;

use Kharanenka\Helper\Result;
use Lang;
use Cookie;
use Event;
use Lovata\Buddies\Facades\BuddiesAuth;
use Lovata\Buddies\Models\User;
use Lovata\OrdersShopaholic\Models\Cart;
use Lovata\OrdersShopaholic\Models\CartItem;
use Lovata\Shopaholic\Classes\CPrice;
use Lovata\Shopaholic\Models\Settings;
use October\Rain\Database\Collection;
use System\Classes\PluginManager;

/**
 * Class CartStore
 * @package Lovata\OrdersShopaholic\Classes
 * @author Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class CartStore
{
    const COOKIE_NAME = 'shopaholic_cart_id';
 
    /** @var CartStore */
    protected static $obThis;
    
    /** @var int - Cart cookie life time */
    public static $iCookieLifeTime = 2419200;

    /** @var Cart */
    protected $obCart = null;
    
    /** @var User */
    protected $obUser = null;

    protected $arShoppingCart = [];
    
    /**
     * CCart constructor.
     */
    protected function __construct()
    {
        //Get cart cookie life time
        $iCookieLifeTime = Settings::getValue('cart_cookie_lifetime');
        if(!empty($iCookieLifeTime) && $iCookieLifeTime > 0) {
            self::$iCookieLifeTime = $iCookieLifeTime;
        }
        
        //Check auth user
        if(BuddiesAuth::check()) {
            $this->obUser = BuddiesAuth::getUser();
        }
        
        //Get cart id from cookie, if exists
        $iCartID = Cookie::get(self::COOKIE_NAME);

        if(!empty($this->obUser)) {
            //Find cart for authorized user
            $this->findUserCart($iCartID);
        } else {
            //Find card by ID
            $this->obCart = Cart::find($iCartID);
        }

        //Create new cart
        if(empty($this->obCart)){
            $this->createCart();
        }
    }

    /**
     * @param $iCartID
     */
    protected function findUserCart($iCartID)
    {
        //Get cart by user ID
        $this->obCart = Cart::getByUser($this->obUser->id)->first();
        if(empty($this->obCart) || $this->obCart->id == $iCartID) {
            return;
        }

        //Set new user cart ID in cookie
        Cookie::queue(self::COOKIE_NAME, $this->obCart->id, self::$iCookieLifeTime);

        //Get guest cart
        /** @var Cart $obGuestCart */
        $obGuestCart = Cart::find($iCartID);
        if(empty($obGuestCart)) {
            return;
        }

        //get user's cart items before auth
        $obGuestCartItemList = $obGuestCart->item;
        if($obGuestCartItemList->isEmpty()) {
            return;
        }

        //Move guest cart items to user cart
        foreach ($obGuestCartItemList as $obCartItem) {
            $obCartItem->cart_id = $this->obCart->id;
            $obCartItem->save();
        }
    }

    /**
     * Create new cart
     * @return void
     */
    protected function createCart()
    {
        $iUserID = null;
        if(!empty($this->obUser)) {
            $iUserID = $this->obUser->id;
        }
        
        $this->obCart = Cart::create(['user_id' => $iUserID]);
        Cookie::queue(self::COOKIE_NAME, $this->obCart->id, self::$iCookieLifeTime);
    }

    /**
     * @return CartStore
     */
    protected static function getInstance() {
        if(empty(self::$obThis)) {
            self::$obThis = new CartStore();
        }
        
        return self::$obThis;
    }
    
    /**
     * Add offer list in current cart
     * @param array $arOfferList
     * @return void
     */
    public static function add($arOfferList)
    {
        if(empty($arOfferList)) {
            $arErrorData = [
                'message'   => Lang::get('lovata.toolbox::lang.message.e_not_correct_request'),
                'field'     => null,
            ];

            Result::setFalse($arErrorData);
            return;
        }
        
        $obThis = self::getInstance();

        //Add offer list in current cart
        foreach($arOfferList as $arOffer) {
            if(empty($arOffer['offer_id']) || empty($arOffer['quantity']) || $arOffer['quantity'] < 1) {
                continue;
            }

            //Find offer in current cart 
            $obCartItem = CartItem::getByCart($obThis->obCart->id)->getByOffer($arOffer['offer_id'])->first();
            if(!empty($obCartItem)) {
                continue;
            }

            $arOffer['cart_id'] = $obThis->obCart->id;

            if(PluginManager::instance()->hasPlugin('Lovata.CustomShopaholic')) {
                \Lovata\CustomShopaholic\Classes\CartStoreExtend::add($obThis, $arOffer);
                if(!Result::flag()) {
                    return;
                }
            }
            
            CartItem::create($arOffer);
            Event::fire('shopaholic.cart.add', $arOffer['offer_id']);
        }
    }

    /**
     * Updates offer quantity in current shopping cart (rewrite or increment/decrement).
     * @param $arOfferList
     * @param bool $bRewriteQuantity
     * @param bool $bIncrement
     * @return void
     */
    public static function update($arOfferList, $bRewriteQuantity = true, $bIncrement = true)
    {
        if(empty($arOfferList)) {
            $arErrorData = [
                'message'   => Lang::get('lovata.toolbox::lang.message.e_not_correct_request'),
                'field'     => null,
            ];

            Result::setFalse($arErrorData);
            return;
        }

        $obThis = self::getInstance();
        
        //Update offer quantity
        foreach ($arOfferList as $arOffer) {
            
            //Check data
            if(empty($arOffer['offer_id']) || empty($arOffer['quantity']) || $arOffer['quantity'] < 1) {
                continue;
            }

            $obCartItem = CartItem::getByCart($obThis->obCart->id)->getByOffer($arOffer['offer_id'])->first();
            if(empty($obCartItem)) {
                continue;
            }

            //if rewrite quantity flag is true offer quantity in cart will be rewritten
            if($bRewriteQuantity) {
                $obCartItem->quantity = $arOffer['quantity'];
            } elseif($bIncrement) {
                //depending on increment flag set in method params we increment or decrement offer quantity
                $obCartItem->quantity += $arOffer['quantity'];
            } elseif(!$bIncrement && $arOffer['quantity'] < $obCartItem->quantity) {
                $obCartItem->quantity -= $arOffer['quantity'];
            }

            $obCartItem->save();
        }
    }

    /**
     * Remove offer from the current basket
     * @param $arOfferList
     * @return void
     */
    public static function remove($arOfferList)
    {
        if(empty($arOfferList)) {
            $arErrorData = [
                'message'   => Lang::get('lovata.toolbox::lang.message.e_not_correct_request'),
                'field'     => null,
            ];

            Result::setFalse($arErrorData);
            return;
        }

        $obThis = self::getInstance();
        
        //Delete offers from cart
        foreach ($arOfferList as $iOfferID) {
            if(empty($iOfferID)) {
                continue;
            }
            
            /** @var CartItem $obCartItem */
            $obCartItem = CartItem::getByCart($obThis->obCart->id)->getByOffer($iOfferID)->first();
            if(empty($obCartItem)) {
                continue;
            }
            
            $obCartItem->delete();
        }
    }

    /**
     * Clear basket
     */
    public static function clear()
    {
        $obThis = self::getInstance();
        $arCartItemIDList = $obThis->obCart->item()->lists('id');
        if(!empty($arCartItemIDList)) {
            CartItem::destroy($arCartItemIDList);
        }
    }

    /**
     * Get cart offers
     * @return array
     */
    public static function get()
    {
        $obThis = self::getInstance();
        if(!empty($obThis->arShoppingCart)){
            return $obThis->arShoppingCart;
        }
        
        //Get offers list
        $obCartItemList = $obThis->obCart->item;
        if($obCartItemList->isEmpty()){
            return $obThis->arShoppingCart;
        }
        
        //Get offer data
        foreach ($obCartItemList as $obCartItem) {
            
            $arOfferData = $obCartItem->getData();
            if(empty($arOfferData)) {
                continue;
            }

            $obThis->arShoppingCart[$obCartItem->offer_id] = $arOfferData;
        }

        return $obThis->arShoppingCart;
    }
    
    /**
     * Get total quantity and total price for cart
     * @return array
     */
    public static function getInfo()
    {
        $obThis = self::getInstance();
        if(empty($obThis->arShoppingCart)) {
            self::get();
        }
        
        $arResult = [
            'quantity' => 0,
            'offer_count' => 0,
            'price_value' => 0,
            'old_price_value' => 0,
            'price' => '',
            'old_price' => '',
        ];
        
        if(empty($obThis->arShoppingCart)) {
            return $arResult;
        }
        
        foreach($obThis->arShoppingCart as $arOfferData) {
            $arResult['offer_count'] ++;
            $arResult['quantity'] += $arOfferData['cart_quantity'];
            $arResult['price_value'] += $arOfferData['cart_price_value'];
            $arResult['old_price_value'] += $arOfferData['cart_old_price_value'];
        }
        
        $arResult['price'] = CPrice::getPriceInFormat($arResult['price_value']);
        $arResult['old_price'] = CPrice::getPriceInFormat($arResult['old_price_value']);
        
        return $arResult;
    }

    /**
     * Get cart item list
     * @return null|CartItem[]|Collection
     */
    public static function getCartItems()
    {
        $obThis = self::getInstance();
        if(empty($obThis->obCart)) {
            return null;
        }
        
        return $obThis->obCart->item;
    }
}