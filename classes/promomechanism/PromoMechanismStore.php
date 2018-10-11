<?php namespace Lovata\OrdersShopaholic\Classes\PromoMechanism;

use Event;
use October\Rain\Support\Traits\Singleton;

//Without condition
use Lovata\OrdersShopaholic\Classes\PromoMechanism\WithoutCondition\WithoutConditionDiscountPosition;
use Lovata\OrdersShopaholic\Classes\PromoMechanism\WithoutCondition\WithoutConditionDiscountMinPrice;
use Lovata\OrdersShopaholic\Classes\PromoMechanism\WithoutCondition\WithoutConditionDiscountPositionTotalPrice;
use Lovata\OrdersShopaholic\Classes\PromoMechanism\WithoutCondition\WithoutConditionDiscountShippingPrice;
use Lovata\OrdersShopaholic\Classes\PromoMechanism\WithoutCondition\WithoutConditionDiscountTotalPrice;
//Discount, if total price of positions greater then te set value
use Lovata\OrdersShopaholic\Classes\PromoMechanism\PositionTotalPriceGreater\PositionTotalPriceGreaterDiscountShippingPrice;
use Lovata\OrdersShopaholic\Classes\PromoMechanism\PositionTotalPriceGreater\PositionTotalPriceGreaterDiscountTotalPrice;
//Discount, if the total quantity of one offer in the order is greater than
use Lovata\OrdersShopaholic\Classes\PromoMechanism\OfferQuantityGreater\OfferQuantityGreaterDiscountPosition;
use Lovata\OrdersShopaholic\Classes\PromoMechanism\OfferQuantityGreater\OfferQuantityGreaterDiscountMinPrice;
use Lovata\OrdersShopaholic\Classes\PromoMechanism\OfferQuantityGreater\OfferQuantityGreaterDiscountPositionTotalPrice;
use Lovata\OrdersShopaholic\Classes\PromoMechanism\OfferQuantityGreater\OfferQuantityGreaterDiscountShippingPrice;
use Lovata\OrdersShopaholic\Classes\PromoMechanism\OfferQuantityGreater\OfferQuantityGreaterDiscountTotalPrice;
//Discount, if the offer total quantity in the order is greater than
use Lovata\OrdersShopaholic\Classes\PromoMechanism\OfferTotalQuantityGreater\OfferTotalQuantityGreaterDiscountPosition;
use Lovata\OrdersShopaholic\Classes\PromoMechanism\OfferTotalQuantityGreater\OfferTotalQuantityGreaterDiscountMinPrice;
use Lovata\OrdersShopaholic\Classes\PromoMechanism\OfferTotalQuantityGreater\OfferTotalQuantityGreaterDiscountPositionTotalPrice;
use Lovata\OrdersShopaholic\Classes\PromoMechanism\OfferTotalQuantityGreater\OfferTotalQuantityGreaterDiscountShippingPrice;
use Lovata\OrdersShopaholic\Classes\PromoMechanism\OfferTotalQuantityGreater\OfferTotalQuantityGreaterDiscountTotalPrice;
//Discount, if the position count in the order is greater than
use Lovata\OrdersShopaholic\Classes\PromoMechanism\PositionCountGreater\PositionCountGreaterDiscountPosition;
use Lovata\OrdersShopaholic\Classes\PromoMechanism\PositionCountGreater\PositionCountGreaterDiscountMinPrice;
use Lovata\OrdersShopaholic\Classes\PromoMechanism\PositionCountGreater\PositionCountGreaterDiscountPositionTotalPrice;
use Lovata\OrdersShopaholic\Classes\PromoMechanism\PositionCountGreater\PositionCountGreaterDiscountShippingPrice;
use Lovata\OrdersShopaholic\Classes\PromoMechanism\PositionCountGreater\PositionCountGreaterDiscountTotalPrice;

/**
 * Class PromoMechanismStore
 * @package Lovata\OrdersShopaholic\Classes\PromoMechanism
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class PromoMechanismStore
{
    use Singleton;

    const EVENT_ADD_PROMO_MECHANISM_CLASS = 'shopaholic.order.promo_mechanism.add_class';

    protected $arMechanismList = [];

    /**
     * Get promo mechanism options for backend
     * @return array
     */
    public function getMechanismOptions() : array
    {
        if (empty($this->arMechanismList)) {
            return [];
        }

        $arResult = [];
        foreach ($this->arMechanismList as $iKey => $sClassName) {
            $arResult[$sClassName] = ($iKey+ 1) . ' - ' . $sClassName::getName();
        }

        return $arResult;
    }

    /**
     * Init mechanism list
     */
    protected function init()
    {
        $this->addMechanism(WithoutConditionDiscountPosition::class);
        $this->addMechanism(WithoutConditionDiscountMinPrice::class);
        $this->addMechanism(WithoutConditionDiscountPositionTotalPrice::class);
        $this->addMechanism(WithoutConditionDiscountShippingPrice::class);
        $this->addMechanism(WithoutConditionDiscountTotalPrice::class);

        $this->addMechanism(PositionTotalPriceGreaterDiscountShippingPrice::class);
        $this->addMechanism(PositionTotalPriceGreaterDiscountTotalPrice::class);

        $this->addMechanism(OfferQuantityGreaterDiscountPosition::class);
        $this->addMechanism(OfferQuantityGreaterDiscountMinPrice::class);
        $this->addMechanism(OfferQuantityGreaterDiscountPositionTotalPrice::class);
        $this->addMechanism(OfferQuantityGreaterDiscountShippingPrice::class);
        $this->addMechanism(OfferQuantityGreaterDiscountTotalPrice::class);

        $this->addMechanism(OfferTotalQuantityGreaterDiscountPosition::class);
        $this->addMechanism(OfferTotalQuantityGreaterDiscountMinPrice::class);
        $this->addMechanism(OfferTotalQuantityGreaterDiscountPositionTotalPrice::class);
        $this->addMechanism(OfferTotalQuantityGreaterDiscountShippingPrice::class);
        $this->addMechanism(OfferTotalQuantityGreaterDiscountTotalPrice::class);

        $this->addMechanism(PositionCountGreaterDiscountPosition::class);
        $this->addMechanism(PositionCountGreaterDiscountMinPrice::class);
        $this->addMechanism(PositionCountGreaterDiscountPositionTotalPrice::class);
        $this->addMechanism(PositionCountGreaterDiscountShippingPrice::class);
        $this->addMechanism(PositionCountGreaterDiscountTotalPrice::class);

        //Fire event
        $arEventDataList = Event::fire(self::EVENT_ADD_PROMO_MECHANISM_CLASS);
        if (empty($arEventDataList)) {
            return;
        }

        //Add classes from event data
        foreach ($arEventDataList as $arEventData) {
            if (empty($arEventData)) {
                continue;
            }

            if (!is_array($arEventData)) {
                $arEventData = [$arEventData];
            }

            foreach ($arEventData as $sClassName) {
                $this->addMechanism($sClassName);
            }
        }
    }

    /**
     * Add promo mechanism to list
     * @param string $sClass
     */
    protected function addMechanism($sClass)
    {
        if (empty($sClass) || !class_exists($sClass) || $sClass instanceof InterfacePromoMechanism) {
            return;
        }

        $this->arMechanismList[] = $sClass;
    }
}