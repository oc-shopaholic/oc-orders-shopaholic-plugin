<?php namespace Lovata\OrdersShopaholic\Classes\Restriction;

use Lovata\OrdersShopaholic\Models\ShippingType;

use Lovata\OrdersShopaholic\Classes\Processor\CartProcessor;
use Lovata\OrdersShopaholic\Interfaces\CheckRestrictionInterface;

/**
 * Class PaymentRestrictionByShippingType
 * @package Lovata\OrdersShopaholic\Classes\Restriction
 * @author Tsagan Noniev, deploy@rubium.ru, Rubium Web
 */
class PaymentRestrictionByShippingType implements CheckRestrictionInterface
{
    protected $arAvailableShippingType;
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
        $this->arAvailableShippingType = (array) array_get($arProperty, 'shipping_type');

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
                'label'   => 'lovata.ordersshopaholic::lang.field.shipping_type',
                'tab'     => 'lovata.toolbox::lang.tab.settings',
                'span'    => 'full',
                'type'    => 'checkboxlist',
                'options' => ShippingType::lists('name', 'id'),
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
        $bResult = empty($this->arAvailableShippingType) || empty($this->iCurrentShippingType) || in_array($this->iCurrentShippingType, $this->arAvailableShippingType);

        return $bResult;
    }
}