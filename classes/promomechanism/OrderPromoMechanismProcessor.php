<?php namespace Lovata\OrdersShopaholic\Classes\PromoMechanism;

use Event;
use Lovata\OrdersShopaholic\Models\Order;
use Lovata\Toolbox\Classes\Helper\PriceHelper;

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
        $this->obPositionPriceData = new PriceContainer(0, 0);
        $this->obShippingPriceData = new PriceContainer(0, 0);
        $this->obTotalPriceData = new PriceContainer(0, 0);

        $this->obOrder = $obOrder;
        $this->obPositionList = $obOrder->order_position;

        $this->calculate();
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

            $obMechanism = new $sClassName($obOrderMechanism->priority, $obOrderMechanism->discount_value, $obOrderMechanism->discount_type, $obOrderMechanism->final_discount, $obOrderMechanism->property);

            if ($obMechanism instanceof AbstractDiscountPosition) {
                $obEventMechanism = Event::fire(self::EVENT_MECHANISM_ADD_CHECK_CALLBACK_METHOD, [$obMechanism, $obOrderMechanism->element_id, $obOrderMechanism->element_type, $obOrderMechanism->element_data], true);
                if (!empty($obEventMechanism) && $obEventMechanism instanceof AbstractDiscountPosition) {
                    $obMechanism = $obEventMechanism;
                }
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
            $fPrice = PriceHelper::round($fPrice * $obOrderPosition->quantity);

            $obPriceData = new PriceContainer($fPrice, $fPrice);

            if (!empty($this->arDiscountPositionList)) {
                foreach ($this->arDiscountPositionList as $obMechanism) {
                    $fNewPrice = $obMechanism->calculate($obOrderPosition, $obPriceData->price_value);
                    if (!$obMechanism->isApplied()) {
                        continue;
                    }

                    $obPriceData->addDiscount($fNewPrice, $obMechanism);
                    if ($obMechanism->isFinal()) {
                        break;
                    }
                }
            }

            $this->arPositionPrice[$obOrderPosition->id] = $obPriceData;

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
        $fPrice = $this->obOrder->getShippingPriceValue();

        $this->obShippingPriceData->price_value += $fPrice;
        $this->obShippingPriceData->old_price_value += $fPrice;

        if (empty($this->arDiscountShippingPriceList)) {
            return;
        }

        $this->arDiscountShippingPriceList = $this->applySortingByPriority($this->arDiscountShippingPriceList);

        /** @var AbstractDiscountTotalPrice $obMechanism */
        foreach ($this->arDiscountShippingPriceList as $obMechanism) {
            $fNewPrice = $obMechanism->calculate($this->obShippingPriceData->price_value);
            if (!$obMechanism->isApplied()) {
                continue;
            }

            $this->obShippingPriceData->addDiscount($fNewPrice, $obMechanism);
            if ($obMechanism->isFinal()) {
                break;
            }
        }
    }
}
