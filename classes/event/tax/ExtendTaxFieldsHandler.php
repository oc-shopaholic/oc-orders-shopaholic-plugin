<?php namespace Lovata\OrdersShopaholic\Classes\Event\Tax;

use Lovata\Toolbox\Classes\Event\AbstractBackendFieldHandler;

use Lovata\Shopaholic\Models\Tax;
use Lovata\Shopaholic\Controllers\Taxes;

/**
 * Class ExtendTaxFieldsHandler
 * @package Lovata\OrdersShopaholic\Classes\Event\Tax
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class ExtendTaxFieldsHandler extends AbstractBackendFieldHandler
{
    /**
     * Extend form fields
     * @param \Backend\Widgets\Form $obWidget
     */
    protected function extendFields($obWidget)
    {
        $arAdditionFields = [
            'applied_to_shipping_price' => [
                'type'  => 'checkbox',
                'label' => 'lovata.ordersshopaholic::lang.field.applied_to_shipping_price',
                'tab'   => 'lovata.toolbox::lang.tab.settings',
            ],
        ];

        $obWidget->addTabFields($arAdditionFields);
    }

    /**
     * Get model class name
     * @return string
     */
    protected function getModelClass() : string
    {
        return Tax::class;
    }

    /**
     * Get controller class name
     * @return string
     */
    protected function getControllerClass() : string
    {
        return Taxes::class;
    }
}
