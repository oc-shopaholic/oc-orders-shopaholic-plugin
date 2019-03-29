<?php namespace Lovata\OrdersShopaholic\Classes\Restriction;

use Lovata\OrdersShopaholic\Classes\Processor\CartProcessor;
use Lovata\OrdersShopaholic\Interfaces\CheckRestrictionInterface;

/**
 * Class RestrictionByTotalPrice
 * @package Lovata\OrdersShopaholic\Classes\Restriction
 * @author Tsagan Noniev, deploy@rubium.ru, Rubium Web
 */
class RestrictionByTotalPrice implements CheckRestrictionInterface
{
    protected $fMinPrice;
    protected $fMaxPrice;
    protected $fTotalPrice;

    /**
     * CheckRestrictionInterface constructor.
     * @param \Lovata\OrdersShopaholic\Classes\Item\ShippingTypeItem $obShippingTypeItem
     * @param array                                                  $arData
     * @param array                                                  $arProperty
     * @param string                                                 $sCode
     */
    public function __construct($obShippingTypeItem, $arData, $arProperty, $sCode)
    {
        $this->fMinPrice = (float) array_get($arProperty, 'price_min');
        $this->fMaxPrice = (float) array_get($arProperty, 'price_max');

        $this->fTotalPrice = CartProcessor::instance()->getCartPositionTotalPriceData()->price_value;
    }

    /**
     * Get backend fields for restriction settings
     * @return array
     */
    public static function getFields() : array
    {
        return [
            'property[price_min]' => [
                'label'   => 'lovata.ordersshopaholic::lang.restriction.property.price_min',
                'tab'     => 'lovata.toolbox::lang.tab.settings',
                'span'    => 'left',
                'type'    => 'number',
                'context' => ['update', 'preview']
            ],
            'property[price_max]' => [
                'label'   => 'lovata.ordersshopaholic::lang.restriction.property.price_max',
                'tab'     => 'lovata.toolbox::lang.tab.settings',
                'span'    => 'right',
                'type'    => 'number',
                'context' => ['update', 'preview']
            ],
        ];
    }

    /**
     * Check restriction of shipping type
     * @return bool
     */
    public function check() : bool
    {
        $bResult = $this->fTotalPrice >= $this->fMinPrice && ($this->fMaxPrice == 0 || $this->fTotalPrice <= $this->fMaxPrice);

        return $bResult;
    }
}