<?php namespace Lovata\OrdersShopaholic\Classes\Event\OrderPosition;

use Lovata\Toolbox\Classes\Event\AbstractBackendFieldHandler;

use Lovata\OrdersShopaholic\Controllers\OrderPositions;
use Lovata\OrdersShopaholic\Models\OrderPosition;
use Lovata\OrdersShopaholic\Models\OrderPositionProperty;
use Lovata\OrdersShopaholic\Models\OrderProperty;

/**
 * Class ExtendOrderPositionFieldsHandler
 * @package Lovata\OrdersShopaholic\Classes\Event\OrderPosition
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class ExtendOrderPositionFieldsHandler extends AbstractBackendFieldHandler
{
    /**
     * Extend form fields
     * @param \Backend\Widgets\Form $obWidget
     */
    protected function extendFields($obWidget)
    {
        $obPropertyList = OrderPositionProperty::active()->orderBy('sort_order', 'asc')->get();

        $this->addOrderPropertyField($obWidget, $obPropertyList);
    }

    /**
     * Add additional order properties
     * @param \Backend\Widgets\Form                                                     $obWidget
     * @param \October\Rain\Database\Collection|OrderProperty[]|OrderPositionProperty[] $obPropertyList
     */
    protected function addOrderPropertyField($obWidget, $obPropertyList)
    {
        if ($obPropertyList->isEmpty()) {
            return;
        }

        //Get widget data for properties
        $arAdditionPropertyData = [];
        /** @var OrderProperty $obProperty */
        foreach ($obPropertyList as $obProperty) {
            $arPropertyData = $obProperty->getWidgetData();
            if (!empty($arPropertyData)) {
                $arAdditionPropertyData[OrderProperty::NAME.'['.$obProperty->code.']'] = $arPropertyData;
            }
        }

        // Add fields
        if (!empty($arAdditionPropertyData)) {
            $obWidget->addTabFields($arAdditionPropertyData);
        }
    }

    /**
     * Get model class name
     * @return string
     */
    protected function getModelClass() : string
    {
        return OrderPosition::class;
    }

    /**
     * Get controller class name
     * @return string
     */
    protected function getControllerClass() : string
    {
        return OrderPositions::class;
    }
}
