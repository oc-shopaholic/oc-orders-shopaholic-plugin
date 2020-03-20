<?php namespace Lovata\OrdersShopaholic\Classes\PromoMechanism;

use Event;

use Lovata\OrdersShopaholic\Models\Cart;
use Lovata\OrdersShopaholic\Classes\Collection\CartPositionCollection;
use Lovata\OrdersShopaholic\Models\PromoMechanism;

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

    /**
     * CartPromoMechanismProcessor constructor.
     * @param Cart                                         $obCart
     * @param CartPositionCollection                       $obCartPositionList
     * @param \Lovata\OrdersShopaholic\Models\ShippingType|\Lovata\OrdersShopaholic\Classes\Item\ShippingTypeItem $obShippingType
     * @param \Lovata\OrdersShopaholic\Models\PaymentMethod|\Lovata\OrdersShopaholic\Classes\Item\PaymentMethodItem $obPaymentMethod
     */
    public function __construct(Cart $obCart, CartPositionCollection $obCartPositionList, $obShippingType, $obPaymentMethod)
    {
        $this->obPositionPriceData = TotalPriceContainer::makeEmpty();
        $this->obShippingPriceData = ItemPriceContainer::makeEmpty();
        $this->obTotalPriceData = TotalPriceContainer::makeEmpty();

        $this->obCart = $obCart;
        $this->obPositionList = $obCartPositionList;
        $this->obShippingType = $obShippingType;
        $this->obPaymentMethod = $obPaymentMethod;

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
     * Recalculate shipping price and total price
     * @param \Lovata\OrdersShopaholic\Models\ShippingType|\Lovata\OrdersShopaholic\Classes\Item\ShippingTypeItem $obShippingType
     */
    public function recalculateShippingPrice($obShippingType)
    {
        $this->obShippingPriceData = ItemPriceContainer::makeEmpty();
        $this->obTotalPriceData = TotalPriceContainer::makeEmpty();

        $this->obShippingType = $obShippingType;

        $this->applyShippingDiscounts();
        $this->applyTotalPriceDiscounts();
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
            $fPricePerUnit = $obCartPositionItem->item->price_value;
            $fOldPricePerUnit = $obCartPositionItem->item->old_price_value > 0 ? $obCartPositionItem->item->old_price_value : $obCartPositionItem->item->price_value;
            $fTaxPercent = $obCartPositionItem->item->tax_percent;

            $obPriceData = new ItemPriceContainer($fPricePerUnit, $fOldPricePerUnit, $fTaxPercent, $obCartPositionItem->quantity);

            if (!empty($this->arDiscountPositionList)) {
                foreach ($this->arDiscountPositionList as $obMechanism) {
                    $obPriceData = $obMechanism->calculateItemDiscount($obPriceData, $this, $obCartPositionItem);
                    if (!$obMechanism->isApplied()) {
                        continue;
                    }

                    if ($obMechanism->isFinal()) {
                        break;
                    }
                }
            }

            $this->arPositionPrice[$obCartPositionItem->id] = $obPriceData;

            $this->obPositionPriceData->addPriceContainer($obPriceData);
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

        $fPrice = $this->obShippingType->getFullPriceValue();
        $fTaxPercent = $this->obShippingType->tax_percent;

        $this->obShippingPriceData = new ItemPriceContainer($fPrice, $fPrice, $fTaxPercent);

        if (empty($this->arDiscountShippingPriceList)) {
            return;
        }

        $this->arDiscountShippingPriceList = $this->applySortingByPriority($this->arDiscountShippingPriceList);

        /** @var InterfacePromoMechanism $obMechanism */
        foreach ($this->arDiscountShippingPriceList as $obMechanism) {
            $this->obShippingPriceData = $obMechanism->calculateItemDiscount($this->obShippingPriceData, $this, $this->obShippingType);
            if (!$obMechanism->isApplied()) {
                continue;
            }

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

        $obPromoMechanismList = PromoMechanism::getAutoAdd()->get();
        if ($obPromoMechanismList->isEmpty()) {
            return;
        }

        /** @var PromoMechanism $obPromoMechanism */
        foreach ($obPromoMechanismList as $obPromoMechanism) {
            $obMechanism = $obPromoMechanism->getTypeObject();
            $obMechanism->setCheckPositionCallback(function ($obPosition) {
                return true;
            });

            $obMechanism->setCheckShippingTypeCallback(function ($obShippingType) {
                return true;
            });

            $obMechanism->setRelatedDescription($obPromoMechanism->name);

            $this->addMechanism($obMechanism);
        }
    }
}
