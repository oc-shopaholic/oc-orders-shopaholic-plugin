<?php namespace Lovata\OrdersShopaholic\Classes\PromoMechanism;

use Event;
use Lovata\OrdersShopaholic\Models\Order;

/**
 * Class OrderPromoMechanismProcessor
 * @package Lovata\OrdersShopaholic\Classes\PromoMechanism
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class OrderPromoMechanismProcessor extends AbstractPromoMechanismProcessor
{
    const EVENT_MECHANISM_ADD_CHECK_CALLBACK_METHOD = 'shopaholic.order.promo_mechanism.order.add_check_callback_method';

    /** @var \October\Rain\Database\Collection|\Lovata\OrdersShopaholic\Models\OrderPosition[] */
    protected $obPositionList;

    /** @var Order */
    protected $obOrder;

    /** @var array */
    protected static $arProcessorStore = [];

    /**
     * OrderPromoMechanismProcessor constructor.
     * @param Order $obOrder
     */
    public function __construct(Order $obOrder)
    {
        $this->obPositionPriceData = TotalPriceContainer::makeEmpty();
        $this->obShippingPriceData = ItemPriceContainer::makeEmpty();
        $this->obTotalPriceData = TotalPriceContainer::makeEmpty();

        $this->obOrder = $obOrder;
        $this->obPositionList = $obOrder->order_position;
        $this->obShippingType = $obOrder->shipping_type;
        $this->obPaymentMethod = $obOrder->payment_method;

        $this->calculate();
    }

    /**
     * Get position list
     * @return \Lovata\OrdersShopaholic\Models\OrderPosition[]|\October\Rain\Database\Collection
     */
    public function getPositionList()
    {
        return $this->obPositionList;
    }
    
    /**
     * Get order
     * @return Order
     */
    public function getOrder()
    {
        return $this->obOrder;
    }

    /**
     * Get promo mechanism from static store
     * @param Order $obOrder
     * @return OrderPromoMechanismProcessor
     */
    public static function get(Order $obOrder)
    {
        if (isset(self::$arProcessorStore[$obOrder->id])) {
            return self::$arProcessorStore[$obOrder->id];
        }

        self::$arProcessorStore[$obOrder->id] = new OrderPromoMechanismProcessor($obOrder);

        return self::$arProcessorStore[$obOrder->id];
    }

    /**
     * Clear promo mechanism from
     * @param Order $obOrder
     * @return OrderPromoMechanismProcessor
     */
    public static function update(Order $obOrder)
    {
        if (isset(self::$arProcessorStore[$obOrder->id])) {
            unset(self::$arProcessorStore[$obOrder->id]);
        }

        return self::get($obOrder);
    }

    /**
     * Fire event and add mechanisms to list
     */
    protected function initMechanismList()
    {
        $obOrderMechanismList = $this->obOrder->order_promo_mechanism;
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
        $fPrice = $this->obOrder->getShippingPriceValue();
        $fTaxPercent = $this->obOrder->shipping_tax_percent;

        $this->obShippingPriceData = new ItemPriceContainer($fPrice, $fPrice, $fTaxPercent);
        if (empty($this->arDiscountShippingPriceList)) {
            return;
        }

        $this->arDiscountShippingPriceList = $this->applySortingByPriority($this->arDiscountShippingPriceList);

        /** @var InterfacePromoMechanism $obMechanism */
        foreach ($this->arDiscountShippingPriceList as $obMechanism) {
            $this->obShippingPriceData = $obMechanism->calculateItemDiscount($this->obShippingPriceData, $this, $this->obOrder->shipping_type);
            if (!$obMechanism->isApplied()) {
                continue;
            }

            if ($obMechanism->isFinal()) {
                break;
            }
        }
    }
}
