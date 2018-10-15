<?php namespace Lovata\OrdersShopaholic\Classes\PromoMechanism;

use Event;
use Lovata\Toolbox\Classes\Helper\PriceHelper;

use Lovata\OrdersShopaholic\Models\Cart;
use Lovata\OrdersShopaholic\Classes\Collection\CartPositionCollection;

/**
 * Class CartPromoMechanismProcessor
 * @package Lovata\OrdersShopaholic\Classes\PromoMechanism
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class CartPromoMechanismProcessor extends AbstractPromoMechanismProcessor
{
    const EVENT_GET_MECHANISM_LIST = 'shopaholic.order.promo_mechanism.cart.add_class';

    /** @var CartPositionCollection|\Lovata\OrdersShopaholic\Classes\Item\CartPositionItem[] */
    protected $obPositionList;

    /** @var Cart */
    protected $obCart;

    /** @var \Lovata\OrdersShopaholic\Models\ShippingType */
    protected $obShippingType;

    /**
     * CartPromoMechanismProcessor constructor.
     * @param Cart                                         $obCart
     * @param CartPositionCollection                       $obCartPositionList
     * @param \Lovata\OrdersShopaholic\Models\ShippingType $obShippingType
     */
    public function __construct(Cart $obCart, CartPositionCollection $obCartPositionList, $obShippingType)
    {
        $this->obPositionPriceData = new PriceContainer(0, 0);
        $this->obShippingPriceData = new PriceContainer(0, 0);
        $this->obTotalPriceData = new PriceContainer(0, 0);

        $this->obCart = $obCart;
        $this->obPositionList = $obCartPositionList;
        $this->obShippingType = $obShippingType;

        $this->calculate();
    }

    /**
     * Get position list
     * @return \Lovata\OrdersShopaholic\Classes\Collection\CartPositionCollection|\Lovata\OrdersShopaholic\Classes\Item\CartPositionItem[]
     */
    public function getPositionList()
    {
        return $this->obPositionList;
    }

    /**
     * @return Cart
     */
    public function getCartObject()
    {
        return $this->obCart;
    }

    /**
     * @return CartPositionCollection
     */
    public function getCartPositionList()
    {
        return $this->obPositionList;
    }

    /**
     * Process position list and apply discounts for positions
     */
    protected function applyPositionDiscounts()
    {
        if ($this->obPositionList->isEmpty()) {
            return;
        }

        $this->arDiscountPositionList = $this->applySortingByPriority($this->arDiscountPositionList);

        foreach ($this->obPositionList as $obCartPositionItem) {
            $fPrice = $obCartPositionItem->item->price_value;
            $fOldPrice = $obCartPositionItem->item->old_price_value > 0 ? $obCartPositionItem->item->old_price_value : $obCartPositionItem->item->price_value;

            $fPrice = PriceHelper::round($fPrice * $obCartPositionItem->quantity);
            $fOldPrice = PriceHelper::round($fOldPrice * $obCartPositionItem->quantity);

            $obPriceData = new PriceContainer($fPrice, $fOldPrice, $obCartPositionItem->quantity);

            if (!empty($this->arDiscountPositionList)) {
                foreach ($this->arDiscountPositionList as $obMechanism) {
                    $fNewPrice = $obMechanism->calculate($obPriceData->price_value, $this, $obCartPositionItem);
                    if (!$obMechanism->isApplied()) {
                        continue;
                    }

                    $obPriceData->addDiscount($fNewPrice, $obMechanism);
                    if ($obMechanism->isFinal()) {
                        break;
                    }
                }
            }

            $this->arPositionPrice[$obCartPositionItem->id] = $obPriceData;

            $this->obPositionPriceData->price_value += $obPriceData->price_value;
            $this->obPositionPriceData->old_price_value += $obPriceData->old_price_value;
            $this->obPositionPriceData->discount_price_value += $obPriceData->discount_price_value;
        }
    }

    /**
     * Process shipping type price and apply discounts
     */
    protected function applyShippingDiscounts()
    {
        if (empty($this->obShippingType)) {
            return;
        }

        $fPrice = $this->obShippingType->price_value;

        $this->obShippingPriceData->price_value += $fPrice;
        $this->obShippingPriceData->old_price_value += $fPrice;

        if (empty($this->arDiscountShippingPriceList)) {
            return;
        }

        $this->arDiscountShippingPriceList = $this->applySortingByPriority($this->arDiscountShippingPriceList);

        /** @var InterfacePromoMechanism $obMechanism */
        foreach ($this->arDiscountShippingPriceList as $obMechanism) {
            $fNewPrice = $obMechanism->calculate($this->obShippingPriceData->price_value, $this, $this->obShippingType);
            if (!$obMechanism->isApplied()) {
                continue;
            }

            $this->obShippingPriceData->addDiscount($fNewPrice, $obMechanism);
            if ($obMechanism->isFinal()) {
                break;
            }
        }
    }

    /**
     * Fire event and add mechanisms to list
     */
    protected function initMechanismList()
    {
        Event::fire(self::EVENT_GET_MECHANISM_LIST, $this);
    }
}
