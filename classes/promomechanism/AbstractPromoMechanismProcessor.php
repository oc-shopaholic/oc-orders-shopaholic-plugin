<?php namespace Lovata\OrdersShopaholic\Classes\PromoMechanism;

/**
 * Class AbstractPromoMechanismProcessor
 * @package Lovata\OrdersShopaholic\Classes\PromoMechanism
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
abstract class AbstractPromoMechanismProcessor
{
    /**
     * @var array|ItemPriceContainer[]
     */
    protected $arPositionPrice = [];

    protected $obPositionList;

    /** @var TotalPriceContainer */
    protected $obPositionPriceData;

    /** @var ItemPriceContainer */
    protected $obShippingPriceData;

    /** @var TotalPriceContainer */
    protected $obTotalPriceData;

    /** @var array|InterfacePromoMechanism[] */
    protected $arDiscountPositionList = [];

    /** @var array|InterfacePromoMechanism[] */
    protected $arDiscountPositionMinPriceList = [];

    /** @var array|InterfacePromoMechanism */
    protected $arDiscountTotalPositionList = [];

    /** @var array|InterfacePromoMechanism */
    protected $arDiscountShippingPriceList = [];

    /** @var array|InterfacePromoMechanism */
    protected $arDiscountTotalPriceList = [];

    /** @var \Lovata\OrdersShopaholic\Models\ShippingType|\Lovata\OrdersShopaholic\Classes\Item\ShippingTypeItem */
    protected $obShippingType;

    /** @var \Lovata\OrdersShopaholic\Models\PaymentMethod|\Lovata\OrdersShopaholic\Classes\Item\PaymentMethodItem */
    protected $obPaymentMethod;

    /**
     * Get position list
     * @return mixed
     */
    abstract public function getPositionList();

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
        } elseif ($sMechanismType == AbstractPromoMechanism::TYPE_POSITION_MIN_PRICE) {
            $this->arDiscountPositionMinPriceList[] = $obMechanism;
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
     * @return ItemPriceContainer
     */
    public function getPositionPrice($iPositionID) : ItemPriceContainer
    {
        if (empty($this->arPositionPrice) || !isset($this->arPositionPrice[$iPositionID])) {
            return ItemPriceContainer::makeEmpty();
        }

        $obPriceData = $this->arPositionPrice[$iPositionID];

        return $obPriceData;
    }

    /**
     * Get position total price with discounts
     * @return TotalPriceContainer
     */
    public function getPositionTotalPrice() : TotalPriceContainer
    {
        return $this->obPositionPriceData;
    }

    /**
     * Get shipping price with discounts
     * @return ItemPriceContainer
     */
    public function getShippingPrice() : ItemPriceContainer
    {
        return $this->obShippingPriceData;
    }

    /**
     * Get total price with discounts
     * @return TotalPriceContainer
     */
    public function getTotalPrice() : TotalPriceContainer
    {
        return $this->obTotalPriceData;
    }

    /**
     * Get active shipping type
     * @return \Lovata\OrdersShopaholic\Models\ShippingType|\Lovata\OrdersShopaholic\Classes\Item\ShippingTypeItem
     */
    public function getShippingType()
    {
        return $this->obShippingType;
    }

    /**
     * Get active payment method
     * @return \Lovata\OrdersShopaholic\Models\PaymentMethod|\Lovata\OrdersShopaholic\Classes\Item\PaymentMethodItem
     */
    public function getPaymentMethod()
    {
        return $this->obPaymentMethod;
    }

    /**
     * Init mechanism list and calculate position prices, shipping price, total price
     */
    protected function calculate()
    {
        $this->initMechanismList();

        $this->applyPositionDiscounts();
        $this->applyPositionMinPriceDiscounts();
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

        /** @var InterfacePromoMechanism $obMechanism */
        foreach ($this->arDiscountTotalPositionList as $obMechanism) {
            $this->obPositionPriceData = $obMechanism->calculateTotalDiscount($this->obPositionPriceData, $this);
            if (!$obMechanism->isApplied()) {
                continue;
            }

            if ($obMechanism->isFinal()) {
                break;
            }
        }
    }

    /**
     * Find position with min price and apply discounts for position price
     */
    protected function applyPositionMinPriceDiscounts()
    {
        if (empty($this->arDiscountPositionMinPriceList)) {
            return;
        }

        $this->arDiscountPositionMinPriceList = $this->applySortingByPriority($this->arDiscountPositionMinPriceList);

        //Find position with min price
        $fMinPrice = null;
        $obPositionMinPrice = null;
        foreach ($this->obPositionList as $obPosition) {
            //Get price for position
            $obPriceData = $this->getPositionPrice($obPosition->id);
            if ($obPriceData->price_value == 0 || ($fMinPrice !== null && $obPriceData->price_per_unit_value > $fMinPrice)) {
                continue;
            }

            $fMinPrice = $obPriceData->price_per_unit_value;
            $obPositionMinPrice = $obPosition;
        }

        if (empty($obPositionMinPrice)) {
            return;
        }

        /** @var InterfacePromoMechanism $obMechanism */
        foreach ($this->arDiscountPositionMinPriceList as $obMechanism) {
            $obPriceData = clone $this->getPositionPrice($obPositionMinPrice->id);
            $obPriceData = $obMechanism->calculateItemDiscount($obPriceData, $this, $obPositionMinPrice);
            if (!$obMechanism->isApplied()) {
                continue;
            }

            $obOldPriceData = $this->getPositionPrice($obPositionMinPrice->id);
            $this->obPositionPriceData->subPriceContainer($obOldPriceData);
            $this->obPositionPriceData->addPriceContainer($obPriceData);

            $this->arPositionPrice[$obPositionMinPrice->id] = $obPriceData;
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
        $this->obTotalPriceData->addPriceContainer($this->obShippingPriceData);
        $this->obTotalPriceData->addPriceContainer($this->obPositionPriceData);

        if (empty($this->arDiscountTotalPriceList)) {
            return;
        }

        $this->arDiscountTotalPriceList = $this->applySortingByPriority($this->arDiscountTotalPriceList);

        /** @var InterfacePromoMechanism $obMechanism */
        foreach ($this->arDiscountTotalPriceList as $obMechanism) {
            $this->obTotalPriceData = $obMechanism->calculateTotalDiscount($this->obTotalPriceData, $this);
            if (!$obMechanism->isApplied()) {
                continue;
            }

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
