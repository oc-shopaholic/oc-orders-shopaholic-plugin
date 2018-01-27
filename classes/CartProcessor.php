<?php namespace Lovata\OrdersShopaholic\Classes;

use Lang;
use Cookie;
use Event;
use October\Rain\Support\Traits\Singleton;
use Kharanenka\Helper\Result;

use Lovata\Buddies\Facades\AuthHelper;

use Lovata\Shopaholic\Classes\Item\OfferItem;
use Lovata\Shopaholic\Models\Settings;
use Lovata\OrdersShopaholic\Models\Cart;
use Lovata\OrdersShopaholic\Models\CartElement;
use Lovata\OrdersShopaholic\Classes\Collection\CartElementCollection;

/**
 * Class CartProcessor
 * @package Lovata\OrdersShopaholic\Classes
 * @author Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class CartProcessor
{
    use Singleton;

    const COOKIE_NAME = 'shopaholic_cart_id';
    
    /** @var int - Cart cookie life time */
    public static $iCookieLifeTime = 2419200;

    /** @var Cart */
    protected $obCart = null;
    
    /** @var \Lovata\Buddies\Models\User */
    protected $obUser = null;

    protected $arShoppingCart = [];
    
    /** @var  CartElementCollection */
    protected $obCartElementList;
    
    /**
     * CartProcessor constructor.
     */
    public function __construct()
    {
        //Get cart cookie life time
        $iCookieLifeTime = Settings::getValue('cart_cookie_lifetime');
        if(!empty($iCookieLifeTime) && $iCookieLifeTime > 0) {
            self::$iCookieLifeTime = $iCookieLifeTime;
        }
        
        //Get cart id from cookie, if exists
        $iCartID = Cookie::get(self::COOKIE_NAME);
        $this->init($iCartID);
    }

    /**Init cart data
     * @param int $iCartID
     */
    public function init($iCartID = null)
    {
        //Get auth user
        $this->obUser = AuthHelper::getUser();

        $this->obCart = null;
        if(!empty($this->obUser)) {
            //Find cart for authorized user
            $this->findUserCart($iCartID);
        } else if(!empty($iCartID)) {
            //Find card by ID
            $this->obCart = Cart::with('item')->find($iCartID);
        }

        //Create new cart
        if(empty($this->obCart)) {
            $this->createNewCart();
        }
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
        $obGuestCartElementList = $obGuestCart->item;
        if($obGuestCartElementList->isEmpty()) {
            return;
        }

        //Move guest cart items to user cart
        foreach ($obGuestCartElementList as $obCartElement) {
            $obCartElement->cart_id = $this->obCart->id;
            $obCartElement->save();
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
     * @return bool
     */
    public function add($arOfferList)
    {
        if(empty($this->obCart) || empty($arOfferList) || !is_array($arOfferList)) {
            $sMessage = Lang::get('lovata.toolbox::lang.message.e_not_correct_request');
            Result::setMessage($sMessage);
            return false;
        }

        //Add offer list in current cart
        foreach($arOfferList as $arOfferData) {
            if(empty($arOfferData['offer_id']) || empty($arOfferData['quantity']) || $arOfferData['quantity'] < 1) {
                continue;
            }

            //Get offer item
            $obOfferItem = OfferItem::make($arOfferData['offer_id']);
            if($obOfferItem->isEmpty() || $obOfferItem->product->isEmpty()) {
                continue;
            }

            //Find offer in current cart 
            $obCartElement = CartElement::getByCart($this->obCart->id)->getByOffer($arOfferData['offer_id'])->first();
            if(!empty($obCartElement)) {

                $obCartElement->quantity = $arOfferData['quantity'];
                $obCartElement->save();

                continue;
            }
            
            $arOfferData['cart_id'] = $this->obCart->id;

            CartElement::create($arOfferData);
            Event::fire('shopaholic.cart.add', $arOfferData['offer_id']);
        }

        $this->obCartElementList = null;
        Result::setTrue();
        return true;
    }

    /**
     * Updates offer quantity in current shopping cart (rewrite or increment/decrement).
     * @param $arOfferList
     * @return bool
     */
    public function update($arOfferList)
    {
        if(empty($this->obCart) || empty($arOfferList) || !is_array($arOfferList)) {
            $sMessage = Lang::get('lovata.toolbox::lang.message.e_not_correct_request');
            Result::setMessage($sMessage);
            return false;
        }
        
        //Update offer quantity
        foreach ($arOfferList as $arOfferData) {
            
            //Check data
            if(empty($arOfferData['offer_id']) || empty($arOfferData['quantity']) || $arOfferData['quantity'] < 1) {
                continue;
            }

            $obCartElement = CartElement::getByCart($this->obCart->id)->getByOffer($arOfferData['offer_id'])->first();
            if(empty($obCartElement)) {
                continue;
            }

            $obCartElement->quantity = $arOfferData['quantity'];
            $obCartElement->save();
        }

        $this->obCartElementList = null;
        return true;
    }

    /**
     * Remove offer from the current basket
     * @param $arOfferList
     * @return bool
     */
    public function remove($arOfferList)
    {
        if(empty($this->obCart) || empty($arOfferList) || !is_array($arOfferList)) {
            $sMessage = Lang::get('lovata.toolbox::lang.message.e_not_correct_request');
            Result::setMessage($sMessage);
            return false;
        }
        
        //Delete offers from cart
        foreach ($arOfferList as $iOfferID) {
            if(empty($iOfferID)) {
                continue;
            }
            
            /** @var CartElement $obCartElement */
            $obCartElement = CartElement::getByCart($this->obCart->id)->getByOffer($iOfferID)->first();
            if(empty($obCartElement)) {
                continue;
            }
            
            $obCartElement->delete();
        }

        $this->obCartElementList = null;
        return true;
    }

    /**
     * Clear cart
     */
    public function clear()
    {
        if(empty($this->obCart)) {
            return;
        }

        $obCartElementList = CartElement::getByCart($this->obCart->id)->get();
        if($obCartElementList->isEmpty()) {
            return;
        }

        foreach ($obCartElementList as $obCartElement) {
            $obCartElement->delete();
        }

        $this->obCartElementList = null;
    }

    /**
     * Get cart item collection
     * @return CartElementCollection
     */
    public function get()
    {
        if(empty($this->obCart)) {
            return CartElementCollection::make();
        }

        if(!empty($this->obCartElementList)) {
            return $this->obCartElementList;
        }
        
        /** @var array $arCartElementIDList */
        $arCartElementIDList = CartElement::getByCart($this->obCart->id)->lists('id');
        $this->obCartElementList = CartElementCollection::make($arCartElementIDList);
        return $this->obCartElementList;
    }
}