<?php namespace Lovata\OrdersShopaholic\Classes\Restriction;

use Lovata\OrdersShopaholic\Classes\Processor\CartProcessor;
use Lovata\OrdersShopaholic\Interfaces\CheckRestrictionInterface;
use Lovata\OrdersShopaholic\Models\ShippingType;

/**
 * Class PaymentRestrictionByShippingType
 * @package Lovata\OrdersShopaholic\Classes\Restriction
 * @author Tsagan Noniev, deploy@rubium.ru, Rubium Web
 */
class PaymentRestrictionByShippingType implements CheckRestrictionInterface
{
    protected $iShippingType;
    protected $iCurrentShippingType;

    /**
     * CheckRestrictionInterface constructor.
     * @param \Lovata\OrdersShopaholic\Classes\Item\PaymentMethodItem $obPaymentMethodItem
     * @param array                                                  $arData
     * @param array                                                  $arProperty
     * @param string                                                 $sCode
     */
    public function __construct($obPaymentMethodItem, $arData, $arProperty, $sCode)
    {
        $this->iShippingType = (float) array_get($arProperty, 'shipping_type');

        $this->iCurrentShippingType = CartProcessor::instance()->getCartObject()->shipping_type_id;
    }

    /**
     * Get backend fields for restriction settings
     * @return array
     */
    public static function getFields() : array
    {
        return [
            'property[shipping_type]' => [
                'label'   => 'lovata.ordersshopaholic::lang.restriction.property.shipping_type',
                'tab'     => 'lovata.toolbox::lang.tab.settings',
                'span'    => 'right',
                'type'    => 'dropdown',
                'options' => ShippingType::pluck('name', 'id'),
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
        $bResult = $this->iShippingType > 0 && $this->iShippingType == $this->iCurrentShippingType;

        return $bResult;
    }
}