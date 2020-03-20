<?php namespace Lovata\OrdersShopaholic\Classes\PromoMechanism;

use Event;
use Lovata\OrdersShopaholic\Classes\Item\OrderItem;

/**
 * Class OrderItemPromoMechanismProcessor
 * @package Lovata\OrdersShopaholic\Classes\PromoMechanism
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class OrderItemPromoMechanismProcessor extends AbstractPromoMechanismProcessor
{
    const EVENT_MECHANISM_ADD_CHECK_CALLBACK_METHOD = 'shopaholic.order.promo_mechanism.order.add_check_callback_method';

    /** @var \Lovata\OrdersShopaholic\Classes\Collection\OrderPositionCollection|\Lovata\OrdersShopaholic\Classes\Item\OrderPositionItem[] */
    protected $obPositionList;

    /** @var OrderItem */
    protected $obOrderItem;

    /**
     * OrderItemPromoMechanismProcessor constructor.
     * @param OrderItem $obOrderItem
     */
    public function __construct(OrderItem $obOrderItem)
    {
        $this->obPositionPriceData = TotalPriceContainer::makeEmpty();
        $this->obShippingPriceData = ItemPriceContainer::makeEmpty();
        $this->obTotalPriceData = TotalPriceContainer::makeEmpty();

        $this->obOrderItem = $obOrderItem;
        $this->obPositionList = $obOrderItem->order_position;
        $this->obShippingType = $obOrderItem->shipping_type;
        $this->obPaymentMethod = $obOrderItem->payment_method;

        $this->calculate();
    }

    /**
     * Get position list
     * @return \Lovata\OrdersShopaholic\Classes\Collection\OrderPositionCollection|\Lovata\OrdersShopaholic\Classes\Item\OrderPositionItem[] */
    public function getPositionList()
    {
        return $this->obPositionList;
    }

    /**
     * Fire event and add mechanisms to list
     */
    protected function initMechanismList()
    {
        $obOrderMechanismList = $this->obOrderItem->order_promo_mechanism;
        if ($obOrderMechanismList->isEmpty()) {
            return;
        }

        foreach ($obOrderMechanismList as $obOrderMechanism) {
            $sClassName = $obOrderMechanism->type;
            if (empty($sClassName) || !class_exists($sClassName)) {
                return null;
            }

            $arPropertyList = (array) $obOrderMechanism->property;
            $arPropertyList['element_id'] = $obOrderMechanism->element_id;
            $arPropertyList['element_type'] = $obOrderMechanism->element_type;
            $arPropertyList['element_data'] = $obOrderMechanism->element_data;

            $obMechanism = new $sClassName($obOrderMechanism->priority, $obOrderMechanism->discount_value, $obOrderMechanism->discount_type, $obOrderMechanism->final_discount, $arPropertyList, $obOrderMechanism->increase);

            $obEventMechanism = Event::fire(self::EVENT_MECHANISM_ADD_CHECK_CALLBACK_METHOD, [$obMechanism, $obOrderMechanism->element_id, $obOrderMechanism->element_type, $obOrderMechanism->element_data], true);
            if (!empty($obEventMechanism) && $obEventMechanism instanceof InterfacePromoMechanism) {
                $obMechanism = $obEventMechanism;
            }

            $obMechanism->setRelatedDescription($obOrderMechanism->description);

            $this->addMechanism($obMechanism);
        }
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

        foreach ($this->obPositionList as $obOrderPosition) {
            $fPrice = $obOrderPosition->price_value;
            $fOldPrice = $obOrderPosition->old_price_value > 0 ? $obOrderPosition->old_price_value : $obOrderPosition->price_value;
            $fTaxPercent = $obOrderPosition->tax_percent;

            $obPriceData = new ItemPriceContainer($fPrice, $fOldPrice, $fTaxPercent, $obOrderPosition->quantity);

            if (!empty($this->arDiscountPositionList)) {
                foreach ($this->arDiscountPositionList as $obMechanism) {
                    $obPriceData = $obMechanism->calculateItemDiscount($obPriceData, $this, $obOrderPosition);
                    if (!$obMechanism->isApplied()) {
                        continue;
                    }

                    if ($obMechanism->isFinal()) {
                        break;
                    }
                }
            }

            $this->arPositionPrice[$obOrderPosition->id] = $obPriceData;

            $this->obPositionPriceData->addPriceContainer($obPriceData);
        }
    }

    /**
     * Process shipping type price and apply discounts
     */
    protected function applyShippingDiscounts()
    {
        $fPrice = $this->obOrderItem->getShippingPriceValue();
        $fTaxPercent = $this->obOrderItem->shipping_tax_percent;

        $this->obShippingPriceData = new ItemPriceContainer($fPrice, $fPrice, $fTaxPercent);

        if (empty($this->arDiscountShippingPriceList)) {
            return;
        }

        $this->arDiscountShippingPriceList = $this->applySortingByPriority($this->arDiscountShippingPriceList);

        /** @var InterfacePromoMechanism $obMechanism */
        foreach ($this->arDiscountShippingPriceList as $obMechanism) {
            $this->obShippingPriceData = $obMechanism->calculateItemDiscount($this->obShippingPriceData, $this, $this->obOrderItem->shipping_type);
            if (!$obMechanism->isApplied()) {
                continue;
            }

            if ($obMechanism->isFinal()) {
                break;
            }
        }
    }
}
