<?php namespace Lovata\OrdersShopaholic\Classes\Item;

use Event;
use Lovata\Toolbox\Classes\Item\ElementItem;
use Lovata\Toolbox\Classes\Helper\PriceHelper;

use Lovata\Shopaholic\Classes\Helper\TaxHelper;
use Lovata\Shopaholic\Classes\Helper\CurrencyHelper;

use Lovata\OrdersShopaholic\Models\ShippingType;
use Lovata\OrdersShopaholic\Classes\Processor\CartProcessor;
use Lovata\OrdersShopaholic\Classes\PromoMechanism\ItemPriceContainer;
use Lovata\OrdersShopaholic\Interfaces\ShippingPriceProcessorInterface;
use Lovata\OrdersShopaholic\Interfaces\CheckRestrictionInterface;

/**
 * Class ShippingTypeItem
 * @package Lovata\Shopaholic\Classes\Item
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 *
 * @property                                                                                               $id
 * @property string                                                                                        $name
 * @property string                                                                                        $code
 * @property string                                                                                        $preview_text
 * @property array                                                                                         $property
 * @property array                                                                                         $restriction
 * @property string                                                                                        $api_class
 * @property \Lovata\OrdersShopaholic\Interfaces\ShippingPriceProcessorInterface                           $api
 * @property float                                                                                         $price_full
 *
 * @property string                                                                                        $price
 * @property float                                                                                         $price_value
 * @property string                                                                                        $tax_price
 * @property float                                                                                         $tax_price_value
 * @property string                                                                                        $price_without_tax
 * @property float                                                                                         $price_without_tax_value
 * @property string                                                                                        $price_with_tax
 * @property float                                                                                         $price_with_tax_value
 *
 * @property string                                                                                        $old_price
 * @property float                                                                                         $old_price_value
 * @property string                                                                                        $tax_old_price
 * @property float                                                                                         $tax_old_price_value
 * @property string                                                                                        $old_price_without_tax
 * @property float                                                                                         $old_price_without_tax_value
 * @property string                                                                                        $old_price_with_tax
 * @property float                                                                                         $old_price_with_tax_value
 *
 * @property string                                                                                        $discount_price
 * @property float                                                                                         $discount_price_value
 * @property string                                                                                        $tax_discount_price
 * @property float                                                                                         $tax_discount_price_value
 * @property string                                                                                        $discount_price_without_tax
 * @property float                                                                                         $discount_price_without_tax_value
 * @property string                                                                                        $discount_price_with_tax
 * @property float                                                                                         $discount_price_with_tax_value
 *
 * @property string                                                                                        $increase_price
 * @property float                                                                                         $increase_price_value
 * @property string                                                                                        $tax_increase_price
 * @property float                                                                                         $tax_increase_price_value
 * @property string                                                                                        $increase_price_without_tax
 * @property float                                                                                         $increase_price_without_tax_value
 * @property string                                                                                        $increase_price_with_tax
 * @property float                                                                                         $increase_price_with_tax_value
 *
 * @property \Lovata\OrdersShopaholic\Classes\PromoMechanism\ItemPriceContainer                            $price_data
 * @property string                                                                                        $currency
 * @property string                                                                                        $currency_code
 *
 * @property float                                                                                         $tax_percent
 * @property \Lovata\Shopaholic\Classes\Collection\TaxCollection|\Lovata\Shopaholic\Classes\Item\TaxItem[] $tax_list
 */
class ShippingTypeItem extends ElementItem
{
    const MODEL_CLASS = ShippingType::class;

    /** @var ShippingType */
    protected $obElement = null;

    protected $sActiveCurrency = null;
    /** @var \Lovata\OrdersShopaholic\Interfaces\ShippingPriceProcessorInterface */
    protected $obApiClass;
    protected $arFieldList = [
        'id',
        'name',
        'code',
        'preview_text',
        'property',
        'restriction',
        'api_class',
        'api',
        'price_full',
    ];

    /**
     * Get param from model data
     * @param string $sName
     * @return mixed|null
     */
    public function __get($sName)
    {
        $sValue = parent::__get($sName);
        if ($sValue !== null || $this->isEmpty() || in_array($sName, $this->arFieldList)) {
            return $sValue;
        }

        return $this->price_data->$sName;
    }

    /**
     * Get order property value
     * @param string $sField
     * @return mixed
     */
    public function getProperty($sField)
    {
        $arPropertyList = $this->property;
        if (empty($arPropertyList) || empty($sField)) {
            return null;
        }

        return array_get($arPropertyList, $sField);
    }

    /**
     * Set active currency code
     * @param string $sActiveCurrencyCode
     * @return ShippingTypeItem
     */
    public function setActiveCurrency($sActiveCurrencyCode)
    {
        $this->sActiveCurrency = $sActiveCurrencyCode;

        return $this;
    }

    /**
     * Get active currency code
     * @return int|null
     */
    public function getActiveCurrency()
    {
        if (!empty($this->sActiveCurrency)) {
            return $this->sActiveCurrency;
        }

        return CurrencyHelper::instance()->getActiveCurrencyCode();
    }

    /**
     * Get full price value
     * @return float
     */
    public function getFullPriceValue()
    {
        $fShippingPrice = Event::fire(ShippingType::EVENT_GET_SHIPPING_PRICE, [$this], true);
        if ($fShippingPrice !== null) {
            $fShippingPrice = PriceHelper::round((float) $fShippingPrice);
            $fShippingPrice = CurrencyHelper::instance()->convert($fShippingPrice, $this->getActiveCurrency());

            return $fShippingPrice;
        }

        $obApiClass = $this->api;
        if (!empty($obApiClass)) {
            $fShippingPrice = $obApiClass->getPrice();
        } else {
            $fShippingPrice = $this->price_full;
        }

        $fShippingPrice = CurrencyHelper::instance()->convert($fShippingPrice, $this->getActiveCurrency());
        $this->setActiveCurrency(null);

        return $fShippingPrice;
    }

    /**
     * Return true, if shipping type is available
     * @param null $arData
     * @return bool
     */
    public function isAvailable($arData = null)
    {
        $arRestrictionList = (array) $this->restriction;
        if (empty($arRestrictionList)) {
            return true;
        }

        foreach ($arRestrictionList as $arRestrictionData) {
            $sRestrictionClass = array_get($arRestrictionData, 'restriction');
            if (empty($sRestrictionClass) || !class_exists($sRestrictionClass)) {
                continue;
            }

            $arProperty = array_get($arRestrictionData, 'property');
            $sCode = array_get($arRestrictionData, 'code');

            /** @var \Lovata\OrdersShopaholic\Interfaces\CheckRestrictionInterface $obRestrictionCheck */
            $obRestrictionCheck = new $sRestrictionClass($this, $arData, $arProperty, $sCode);
            if (!$obRestrictionCheck->check()) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get currency value
     * @return null|string
     */
    protected function getCurrencyAttribute()
    {
        if (empty($this->sActiveCurrency)) {
            return CurrencyHelper::instance()->getActiveCurrencySymbol();
        }

        $sResult = CurrencyHelper::instance()->getCurrencySymbol($this->sActiveCurrency);
        $this->sActiveCurrency = null;

        return $sResult;
    }

    /**
     * Get currency code value
     * @return null|string
     */
    protected function getCurrencyCodeAttribute()
    {
        if (empty($this->sActiveCurrency)) {
            return CurrencyHelper::instance()->getActiveCurrencyCode();
        }

        $sResult = CurrencyHelper::instance()->getCurrencyCode($this->sActiveCurrency);
        $this->sActiveCurrency = null;

        return $sResult;
    }

    /**
     * Get price data for position
     * @return \Lovata\OrdersShopaholic\Classes\PromoMechanism\ItemPriceContainer
     */
    protected function getPriceDataAttribute()
    {
        $obPriceData = $this->getAttribute('price_data');
        if (!empty($obPriceData) && $obPriceData instanceof ItemPriceContainer) {
            return $obPriceData;
        }

        CartProcessor::instance()->setActiveShippingType($this);
        $obPriceData = CartProcessor::instance()->getShippingPriceData();

        $this->setAttribute('price_data', $obPriceData);

        return $obPriceData;
    }

    /**
     * Get tax_percent attribute value
     * @return float
     */
    protected function getTaxPercentAttribute()
    {
        $fTaxPercent = $this->getAttribute('tax_percent');
        if ($fTaxPercent === null) {
            $fTaxPercent = TaxHelper::instance()->getTaxPercent($this->tax_list);
            $this->setAttribute('tax_percent', $fTaxPercent);
        }

        return $fTaxPercent;
    }

    /**
     * Get tax_list attribute value
     * @return \Lovata\Shopaholic\Classes\Collection\TaxCollection|\Lovata\Shopaholic\Classes\Item\TaxItem[]
     */
    protected function getTaxListAttribute()
    {
        $obTaxList = $this->getAttribute('tax_list');
        if ($obTaxList === null) {
            $obTaxList = $this->getAppliedTaxList();
            $this->setAttribute('tax_list', $obTaxList);
        }

        return $obTaxList;
    }

    /**
     * Get applied tax list
     * @return \Lovata\Shopaholic\Classes\Collection\TaxCollection|\Lovata\Shopaholic\Classes\Item\TaxItem[]
     */
    protected function getAppliedTaxList()
    {
        $obResultTaxList = TaxHelper::instance()->getTaxList();
        if ($obResultTaxList->isEmpty()) {
            return $obResultTaxList;
        }

        foreach ($obResultTaxList as $obTaxItem) {
            $bSkipTax = !$obTaxItem->applied_to_shipping_price
                && !$obTaxItem->isAvailableForCountry(TaxHelper::instance()->getActiveCountry())
                && !$obTaxItem->isAvailableForState(TaxHelper::instance()->getActiveState());

            if ($bSkipTax) {
                $obResultTaxList->exclude($obTaxItem->id);
            }
        }

        return $obResultTaxList;
    }

    /**
     * Get api object
     * @return ShippingPriceProcessorInterface|null
     */
    protected function getApiAttribute()
    {
        if (!empty($this->obApiClass) && $this->obApiClass instanceof ShippingPriceProcessorInterface) {
            return $this->obApiClass;
        }

        $sApiClass = $this->api_class;
        if (!empty($sApiClass) && class_exists($sApiClass)) {
            $this->obApiClass = new $sApiClass($this);
        }

        return $this->obApiClass;
    }

    /**
     * Set element data from model object
     *
     * @return array
     */
    protected function getElementData()
    {
        $arRestrictionList = [];
        $obRestrictionList = $this->obElement->shipping_restriction;
        foreach ($obRestrictionList as $obRestriction) {
            $arRestrictionList[] = [
                'restriction' => $obRestriction->restriction,
                'property'    => $obRestriction->property,
                'code'        => $obRestriction->code,
            ];
        }

        $arResult = [
            'price_full'  => $this->obElement->price_value,
            'restriction' => $arRestrictionList,
        ];

        return $arResult;
    }
}
