<?php namespace Lovata\OrdersShopaholic\Classes\PromoMechanism;

/**
 * Class AbstractPromoMechanismProcessor
 * @package Lovata\OrdersShopaholic\Classes\PromoMechanism
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
abstract class AbstractPromoMechanismProcessor
{
    /**
     * @var array|PriceContainer[]
     */
    protected $arPositionPrice = [];

    /** @var PriceContainer */
    protected $obPositionPriceData;

    /** @var PriceContainer */
    protected $obShippingPriceData;

    /** @var PriceContainer */
    protected $obTotalPriceData;

    /** @var array|AbstractDiscountPosition[] */
    protected $arDiscountPositionList = [];

    /** @var array|AbstractDiscountPositionTotalPrice */
    protected $arDiscountTotalPositionList = [];

    /** @var array|AbstractDiscountShippingPrice */
    protected $arDiscountShippingPriceList = [];

    /** @var array|AbstractDiscountTotalPrice */
    protected $arDiscountTotalPriceList = [];

    /**
     * Add mechanism class with params
     * @param InterfacePromoMechanism $obMechanism
     */
    public function addMechanism($obMechanism)
    {
        if (empty($obMechanism) || !$obMechanism instanceof InterfacePromoMechanism) {
            return;
        }

        $sMechanismType = $obMechanism::getType();
        if ($sMechanismType == AbstractPromoMechanism::TYPE_POSITION) {
            $this->arDiscountPositionList[] = $obMechanism;
        } elseif ($sMechanismType == AbstractPromoMechanism::TYPE_TOTAL_POSITION) {
            $this->arDiscountTotalPositionList[] = $obMechanism;
        } elseif ($sMechanismType == AbstractPromoMechanism::TYPE_SHIPPING) {
            $this->arDiscountShippingPriceList[] = $obMechanism;
        } elseif ($sMechanismType == AbstractPromoMechanism::TYPE_TOTAL_PRICE) {
            $this->arDiscountTotalPriceList[] = $obMechanism;
        }
    }

    /**
     * Get position price with discounts
     * @param int $iPositionID
     * @return PriceContainer
     */
    public function getPositionPrice($iPositionID) : PriceContainer
    {
        if (empty($this->arPositionPrice) || !isset($this->arPositionPrice[$iPositionID])) {
            return new PriceContainer(0, 0);
        }

        $obPriceData = $this->arPositionPrice[$iPositionID];

        return $obPriceData;
    }

    /**
     * Get position total price with discounts
     * @return PriceContainer
     */
    public function getPositionTotalPrice() : PriceContainer
    {
        return $this->obPositionPriceData;
    }

    /**
     * Get shipping price with discounts
     * @return PriceContainer
     */
    public function getShippingPrice() : PriceContainer
    {
        return $this->obShippingPriceData;
    }

    /**
     * Get total price with discounts
     * @return PriceContainer
     */
    public function getTotalPrice() : PriceContainer
    {
        return $this->obTotalPriceData;
    }

    /**
     * Init mechanism list and calculate position prices, shipping price, total price
     */
    protected function calculate()
    {
        $this->initMechanismList();

        $this->applyPositionDiscounts();
        $this->applyPositionTotalPriceDiscounts();
        $this->applyShippingDiscounts();
        $this->applyTotalPriceDiscounts();
    }

    /**
     * Process position total price and apply discounts for positions total price
     */
    protected function applyPositionTotalPriceDiscounts()
    {
        if (empty($this->arDiscountTotalPositionList)) {
            return;
        }

        $this->arDiscountTotalPositionList = $this->applySortingByPriority($this->arDiscountTotalPositionList);

        /** @var AbstractDiscountPositionTotalPrice $obMechanism */
        foreach ($this->arDiscountTotalPositionList as $obMechanism) {
            $fNewPrice = $obMechanism->calculate($this->obPositionPriceData->price_value);
            if (!$obMechanism->isApplied()) {
                continue;
            }

            $this->obPositionPriceData->addDiscount($fNewPrice, $obMechanism);
            if ($obMechanism->isFinal()) {
                break;
            }
        }
    }

    /**
     * Process position total price and apply discounts for positions total price
     */
    protected function applyTotalPriceDiscounts()
    {
        $this->obTotalPriceData->price_value = $this->obPositionPriceData->price_value + $this->obShippingPriceData->price_value;
        $this->obTotalPriceData->old_price_value = $this->obPositionPriceData->old_price_value + $this->obShippingPriceData->old_price_value;
        $this->obTotalPriceData->discount_price_value = $this->obPositionPriceData->discount_price_value + $this->obShippingPriceData->discount_price_value;

        if (empty($this->arDiscountTotalPriceList)) {
            return;
        }

        $this->arDiscountTotalPriceList = $this->applySortingByPriority($this->arDiscountTotalPriceList);

        /** @var AbstractDiscountTotalPrice $obMechanism */
        foreach ($this->arDiscountTotalPriceList as $obMechanism) {
            $fNewPrice = $obMechanism->calculate($this->obTotalPriceData->price_value);
            if (!$obMechanism->isApplied()) {
                continue;
            }

            $this->obTotalPriceData->addDiscount($fNewPrice, $obMechanism);
            if ($obMechanism->isFinal()) {
                break;
            }
        }
    }

    /**
     * Apply sorting by priority for promo mechanism list
     * @param array|AbstractPromoMechanism[] $arMechanismList
     * @return array|AbstractPromoMechanism[]
     */
    protected function applySortingByPriority($arMechanismList)
    {
        if (empty($arMechanismList)) {
            return $arMechanismList;
        }

        usort($arMechanismList, function ($obPrevMechanism, $obNextMechanism) {
            /** @var AbstractPromoMechanism $obPrevMechanism */
            /** @var AbstractPromoMechanism $obNextMechanism */
            if ($obPrevMechanism->getPriority() == $obNextMechanism->getPriority()) {
                return 0;
            }

            return $obPrevMechanism->getPriority() < $obNextMechanism->getPriority() ? 1 : -1;
        });

        return $arMechanismList;
    }

    /**
     * Fire event and add mechanisms to list
     */
    abstract protected function initMechanismList();

    /**
     * Process position list and apply discounts for positions
     */
    abstract protected function applyPositionDiscounts();

    /**
     * Process shipping type and apply discounts
     */
    abstract protected function applyShippingDiscounts();
}
