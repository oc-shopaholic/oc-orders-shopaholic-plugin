<?php namespace Lovata\OrdersShopaholic\Classes\PromoMechanism;

use Event;
use October\Rain\Support\Traits\Singleton;

use Lovata\OrdersShopaholic\Classes\PromoMechanism\WithoutCondition\WithoutConditionDiscountOrderPosition;
use Lovata\OrdersShopaholic\Classes\PromoMechanism\WithoutCondition\WithoutConditionDiscountShippingPrice;
use Lovata\OrdersShopaholic\Classes\PromoMechanism\WithoutCondition\WithoutConditionDiscountTotalPrice;

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
        foreach ($this->arMechanismList as $sClassName) {
            $arResult[$sClassName] = $sClassName::getName();
        }

        return $arResult;
    }

    /**
     * Init mechanism list
     */
    protected function init()
    {
        $this->addMechanism(WithoutConditionDiscountOrderPosition::class);
        $this->addMechanism(WithoutConditionDiscountShippingPrice::class);
        $this->addMechanism(WithoutConditionDiscountTotalPrice::class);

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