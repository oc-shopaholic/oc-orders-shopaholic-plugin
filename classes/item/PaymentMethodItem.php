<?php namespace Lovata\OrdersShopaholic\Classes\Item;

use Lovata\Toolbox\Classes\Item\ElementItem;

use Lovata\OrdersShopaholic\Models\PaymentMethod;

/**
 * Class PaymentMethodItem
 * @package Lovata\Shopaholic\Classes\Item
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 *
 * @property        $id
 * @property string $name
 * @property string $code
 * @property string $preview_text
 * @property array  $restriction
 */
class PaymentMethodItem extends ElementItem
{
    const MODEL_CLASS = PaymentMethod::class;

    /** @var PaymentMethod */
    protected $obElement = null;

    /**
     * Set element data from model object
     *
     * @return array
     */
    protected function getElementData()
    {
        $arRestrictionList = [];
        $obRestrictionList = $this->obElement->payment_restriction;
        foreach ($obRestrictionList as $obRestriction) {
            $arRestrictionList[] = [
                'restriction' => $obRestriction->restriction,
                'property'    => $obRestriction->property,
                'code'        => $obRestriction->code,
            ];
        }

        $arResult = [
            'restriction' => $arRestrictionList,
        ];

        return $arResult;
    }

    /**
     * Return true, if payment method is available
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
}
