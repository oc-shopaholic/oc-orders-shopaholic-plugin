<?php namespace Lovata\OrdersShopaholic\Classes\Event\OrderPosition;

use Lang;
use Lovata\Toolbox\Classes\Event\AbstractBackendFieldHandler;

use Lovata\Shopaholic\Models\Measure;
use Lovata\Shopaholic\Models\Settings;
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
        //Get widget data for properties
        $arAdditionPropertyData = [
            'weight' => [
                'label'    => $this->getWeightFieldLabel(),
                'type'     => 'number',
                'span'     => 'left',
                'disabled' => true,
                'tab'      => 'lovata.shopaholic::lang.tab.dimensions',
            ],
            'height' => [
                'label' => $this->getDimensionsFieldLabel('lovata.toolbox::lang.field.height'),
                'type'  => 'number',
                'span'  => 'left',
                'disabled' => true,
                'tab'   => 'lovata.shopaholic::lang.tab.dimensions',
            ],
            'length' => [
                'label' => $this->getDimensionsFieldLabel('lovata.toolbox::lang.field.length'),
                'type'  => 'number',
                'span'  => 'left',
                'disabled' => true,
                'tab'   => 'lovata.shopaholic::lang.tab.dimensions',
            ],
            'width'  => [
                'label' => $this->getDimensionsFieldLabel('lovata.toolbox::lang.field.width'),
                'type'  => 'number',
                'span'  => 'left',
                'disabled' => true,
                'tab'   => 'lovata.shopaholic::lang.tab.dimensions',
            ],
        ];

        $obWidget->addTabFields($arAdditionPropertyData);
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
     * Get weight field label
     * @return string
     */
    protected function getWeightFieldLabel()
    {
        $sLabel = Lang::get('lovata.toolbox::lang.field.weight');
        $iMeasureID = Settings::getValue('weight_measure');
        if (empty($iMeasureID)) {
            return $sLabel;
        }

        $obMeasure = Measure::find($iMeasureID);
        if (empty($obMeasure)) {
            return $sLabel;
        }

        $sLabel .= " ({$obMeasure->name})";

        return $sLabel;
    }

    /**
     * Get dimensions field label
     * @param string $sLangPath
     * @return string
     */
    protected function getDimensionsFieldLabel($sLangPath)
    {
        $sLabel = Lang::get($sLangPath);
        $iMeasureID = Settings::getValue('dimensions_measure');
        if (empty($iMeasureID)) {
            return $sLabel;
        }

        $obMeasure = Measure::find($iMeasureID);
        if (empty($obMeasure)) {
            return $sLabel;
        }

        $sLabel .= " ({$obMeasure->name})";

        return $sLabel;
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
