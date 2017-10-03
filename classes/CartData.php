<?php namespace Lovata\OrdersShopaholic\Classes;

use Lang;
use Cookie;
use Event;
use Kharanenka\Helper\Result;
use Lovata\Buddies\Models\User;
use Lovata\Buddies\Facades\BuddiesAuth;
use Lovata\OrdersShopaholic\Models\Cart;
use Lovata\OrdersShopaholic\Models\CartItem;
use Lovata\Shopaholic\Models\Settings;
use Lovata\OrdersShopaholic\Classes\Collection\CartElementCollection;
use October\Rain\Argon\Argon;

/**
 * Class CartData
 * @package Lovata\OrdersShopaholic\Classes
 * @author Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class CartData
{
    const COOKIE_NAME = 'shopaholic_cart_id';
    
    /** @var int - Cart cookie life time */
    public static $iCookieLifeTime = 2419200;

    /** @var Cart */
    protected $obCart = null;
    
    /** @var User */
    protected $obUser = null;

    protected $arShoppingCart = [];
    
    /** @var  CartElementCollection */
    protected $obCartItemList;
    
    /**
     * CartData constructor.
     */
    public function __construct()
    {
        //Get cart cookie life time
        $iCookieLifeTime = Settings::getValue('cart_cookie_lifetime');
        if(!empty($iCookieLifeTime) && $iCookieLifeTime > 0) {
            self::$iCookieLifeTime = $iCookieLifeTime;
        }
        
        //Get auth user
        $this->obUser = BuddiesAuth::getUser();
        
        //Get cart id from cookie, if exists
        $iCartID = Cookie::get(self::COOKIE_NAME);

        if(!empty($this->obUser)) {
            //Find cart for authorized user
            $this->findUserCart($iCartID);
        } else {
            //Find card by ID
            $this->obCart = Cart::with('item')->find($iCartID);
        }

        //Create new cart
        if(empty($this->obCart)) {
            $this->createNewCart();
        }

        $this->checkCartOffers();
    }

    /**
     * Find user cart
     * @param int $iCartID
     */
    protected function findUserCart($iCartID)
    {
        //Get cart by user ID
        $this->obCart = Cart::with('item')->getByUser($this->obUser->id)->first();
        if(empty($this->obCart) || $this->obCart->id == $iCartID) {
            return;
        }

        //Set new user cart ID in cookie
        Cookie::queue(self::COOKIE_NAME, $this->obCart->id, self::$iCookieLifeTime);

        //Get guest cart
        /** @var Cart $obGuestCart */
        $obGuestCart = Cart::with('item')->find($iCartID);
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
        if(!empty($this->obUser)) {
            $iUserID = $this->obUser->id;
        }
        
        $this->obCart = Cart::create(['user_id' => $iUserID]);
        Cookie::queue(self::COOKIE_NAME, $this->obCart->id, self::$iCookieLifeTime);
    }
    
    /**
     * Add offer list in current cart
     * @param array $arOfferList
     * @return void
     */
    public function add($arOfferList)
    {
        if(empty($arOfferList)) {
            $arErrorData = [
                'message'   => Lang::get('lovata.toolbox::lang.message.e_not_correct_request'),
                'field'     => null,
            ];

            Result::setFalse($arErrorData);
            return;
        }

        //Add offer list in current cart
        foreach($arOfferList as $arOffer) {
            if(empty($arOffer['offer_id']) || empty($arOffer['quantity']) || $arOffer['quantity'] < 1) {
                continue;
            }
            
            //Find offer in current cart 
            $obCartItem = CartItem::getByCart($this->obCart->id)->getByOffer($arOffer['offer_id'])->first();
            if(!empty($obCartItem)) {
                continue;
            }
            
            $arOffer['cart_id'] = $this->obCart->id;
            
            CartItem::create($arOffer);
            Event::fire('shopaholic.cart.add', $arOffer['offer_id']);
        }

        if(empty($this->obCart)) {
            return;
        }

        $this->obCart->load('item');
    }

    /**
     * Updates offer quantity in current shopping cart (rewrite or increment/decrement).
     * @param $arOfferList
     * @param bool $bRewriteQuantity
     * @param bool $bIncrement
     * @return void
     */
    public function update($arOfferList, $bRewriteQuantity = true, $bIncrement = true)
    {
        if(empty($arOfferList)) {
            $arErrorData = [
                'message'   => Lang::get('lovata.toolbox::lang.message.e_not_correct_request'),
                'field'     => null,
            ];

            Result::setFalse($arErrorData);
            return;
        }
        
        //Update offer quantity
        foreach ($arOfferList as $arOffer) {
            
            //Check data
            if(empty($arOffer['offer_id']) || empty($arOffer['quantity']) || $arOffer['quantity'] < 1) {
                continue;
            }

            $obCartItem = CartItem::getByCart($this->obCart->id)->getByOffer($arOffer['offer_id'])->first();
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

        if(empty($this->obCart)) {
            return;
        }

        $this->obCart->load('item');
    }

    /**
     * Remove offer from the current basket
     * @param $arOfferList
     * @return void
     */
    public function remove($arOfferList)
    {
        if(empty($arOfferList)) {
            $arErrorData = [
                'message'   => Lang::get('lovata.toolbox::lang.message.e_not_correct_request'),
                'field'     => null,
            ];

            Result::setFalse($arErrorData);
            return;
        }
        
        //Delete offers from cart
        foreach ($arOfferList as $iOfferID) {
            if(empty($iOfferID)) {
                continue;
            }
            
            /** @var CartItem $obCartItem */
            $obCartItem = CartItem::getByCart($this->obCart->id)->getByOffer($iOfferID)->first();
            if(empty($obCartItem)) {
                continue;
            }
            
            $obCartItem->delete();
        }
        
        if(empty($this->obCart)) {
            return;
        }
        
        $this->obCart->load('item');
    }

    /**
     * Clear cart
     */
    public function clear()
    {
        /** @var array $arCartItemIDList */
        $arCartItemIDList = $this->obCart->item()->lists('id');
        if(!empty($arCartItemIDList)) {
            CartItem::destroy($arCartItemIDList);
        }
    }

    /**
     * Get cart item collection
     * @return CartElementCollection
     */
    public function get()
    {
        if(!empty($this->obCartItemList)) {
            return $this->obCartItemList;
        }
        
        /** @var array $arCartItemIDList */
        $arCartItemIDList = $this->obCart->item->lists('id');
        $this->obCartItemList = CartElementCollection::make($arCartItemIDList);
        return $this->obCartItemList;
    }

    /**
     * Check cart items and remove old offers
     */
    public function checkCartOffers()
    {
        if(empty($this->obCart)) {
            return;
        }
        
        //Check update cart field
        $obUpdateDate = $this->obCart->updated_at;
        $obDateNow = Argon::now()->subHour();
        if($obDateNow < $obUpdateDate) {
            return;
        }
        
        //Get cart item list
        $obItemList =$this->obCart->item;
        if($obItemList->isEmpty()) {
            return;
        }
        
        foreach($obItemList as $obCartItem) {
            
            //Get offer
            $obOffer = $obCartItem->offer;
            if(empty($obOffer) || !$obOffer->active) {
                $obCartItem->delete();
                continue;
            }
            
            //Get offer product
            $obProduct = $obOffer->product;
            if(empty($obProduct) || !$obProduct->active) {
                $obCartItem->delete();
                continue;
            }
        }
        
        $this->obCart->load('item');
        $this->obCart->updated_at = Argon::now();
        $this->obCart->save();
    }
}