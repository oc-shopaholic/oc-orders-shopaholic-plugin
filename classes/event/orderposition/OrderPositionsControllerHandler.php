<?php namespace Lovata\OrdersShopaholic\Classes\Event\OrderPosition;

use DB;
use Lovata\OrdersShopaholic\Controllers\OrderPositions;

/**
 * Class OrderPositionsControllerHandler
 * @package Lovata\OrdersShopaholic\Classes\Event\OrderPosition
 * @author Sergey Zakharevich, <s.v.zakharevich@gmail.com>, LOVATA Group
 */
class OrderPositionsControllerHandler
{
    /**
     * Add listeners
     */
    public function subscribe()
    {
        OrderPositions::extend(function($obController) {
            $this->extendExportConfig($obController);
        });
    }

    /**
     * Extend export config.
     * @param OrderPositions $obController
     * @throws \SystemException
     */
    protected function extendExportConfig($obController)
    {
        /** @var OrderPositions $obController */
        if (is_array($obController->importExportConfig)) {
            $arConfig = $obController->importExportConfig;
        } else {
            $arConfig = (array)$obController->makeConfig('$/lovata/ordersshopaholic/controllers/orderpositions/'.$obController->importExportConfig);
        }

        $arFiledList = (array) array_get($arConfig, 'export.list.columns');
        $arFiledList = array_merge($arFiledList, $this->getExportFieldList());

        array_set($arConfig, 'export.list.columns', $arFiledList);
        $obController->importExportConfig = $arConfig;
    }

    /**
     * Get export field list.
     */
    protected function getExportFieldList() : array
    {
        $arFieldList = [
            'order_number'    => ['label' => 'lovata.ordersshopaholic::lang.field.order_number'],
            'offer_name'      => ['label' => 'lovata.toolbox::lang.field.name'],
            'code'            => ['label' => 'lovata.toolbox::lang.field.code'],
            'price'           => ['label' => 'lovata.shopaholic::lang.field.price'],
            'old_price'       => ['label' => 'lovata.shopaholic::lang.field.old_price'],
            'quantity'        => ['label' => 'lovata.shopaholic::lang.field.quantity'],
            'total_price'     => ['label' => 'lovata.ordersshopaholic::lang.field.position_price'],
            'old_total_price' => ['label' => 'lovata.ordersshopaholic::lang.field.old_position_price'],
        ];

        $arPropertyList = (array) DB::table('lovata_orders_shopaholic_position_properties')
            ->where('active', true)
            ->lists('name', 'code');

        if (empty($arPropertyList)) {
            return $arFieldList;
        }

        foreach ($arPropertyList as $sField => $sLabel) {
            $arFieldList[$sField] = ['label' => $sLabel];
        }

        return $arFieldList;
    }
}
