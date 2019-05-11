<?php namespace Lovata\OrdersShopaholic\Classes\Restriction;

use Lovata\OrdersShopaholic\Models\PaymentMethod;

use Lovata\OrdersShopaholic\Classes\Processor\CartProcessor;
use Lovata\OrdersShopaholic\Interfaces\CheckRestrictionInterface;

/**
 * Class ShippingRestrictionByPaymentMethod
 * @package Lovata\OrdersShopaholic\Classes\Restriction
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class ShippingRestrictionByPaymentMethod implements CheckRestrictionInterface
{
    protected $arAvailablePaymentMethod;
    protected $iCurrentPaymentMethod;

    /**
     * CheckRestrictionInterface constructor.
     * @param \Lovata\OrdersShopaholic\Classes\Item\ShippingTypeItem $obShippingTypeItem
     * @param array                                                  $arData
     * @param array                                                  $arProperty
     * @param string                                                 $sCode
     */
    public function __construct($obShippingTypeItem, $arData, $arProperty, $sCode)
    {
        $this->arAvailablePaymentMethod = (array) array_get($arProperty, 'payment_method');

        $this->iCurrentPaymentMethod = CartProcessor::instance()->getCartObject()->payment_method_id;
    }

    /**
     * Get backend fields for restriction settings
     * @return array
     */
    public static function getFields() : array
    {
        return [
            'property[payment_method]' => [
                'label'   => 'lovata.ordersshopaholic::lang.field.payment_method',
                'tab'     => 'lovata.toolbox::lang.tab.settings',
                'span'    => 'full',
                'type'    => 'checkboxlist',
                'options' => PaymentMethod::lists('name', 'id'),
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
        $bResult = empty($this->arAvailablePaymentMethod) || empty($this->iCurrentPaymentMethod) || in_array($this->iCurrentPaymentMethod, $this->arAvailablePaymentMethod);

        return $bResult;
    }
}