<?php namespace Lovata\OrdersShopaholic\Classes\Event\Order;

use DB;
use Lovata\OrdersShopaholic\Models\OrderExport;
use Lovata\Toolbox\Classes\Helper\UserHelper;

use Lovata\OrdersShopaholic\Controllers\Orders;

/**
 * Class OrdersControllerHandler
 * @package Lovata\OrdersShopaholic\Classes\Event\Order
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class OrdersControllerHandler
{
    /**
     * Add listeners
     */
    public function subscribe()
    {
        Orders::extend(function($obController) {
            $this->extendConfig($obController);
            $this->extendExportConfig($obController);
        });
    }

    /**
     * Extend config.
     * @param Orders $obController
     */
    protected function extendConfig($obController)
    {
        $sUserPluginName = UserHelper::instance()->getPluginName();
        if (empty($sUserPluginName )) {
            return;
        }

        if ($sUserPluginName == 'Lovata.Buddies') {
           $sConfigPath = '$/lovata/ordersshopaholic/config/buddies_user_relation.yaml';
        } else {
            $sConfigPath = '$/lovata/ordersshopaholic/config/rainlab_user_relation.yaml';
        }

        $obController->relationConfig = $obController->mergeConfig($obController->relationConfig, $sConfigPath);
    }

    /**
     * Extend export config.
     * @param Orders $obController
     * @throws \SystemException
     */
    protected function extendExportConfig($obController)
    {
        /** @var Orders $obController */
        if (is_array($obController->importExportConfig)) {
            $arConfig = $obController->importExportConfig;
        } else {
            $arConfig = (array)$obController->makeConfig('$/lovata/ordersshopaholic/controllers/orders/'.$obController->importExportConfig);
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
            OrderExport::RELATION_STATUS         => ['label' => 'lovata.toolbox::lang.field.status'],
            'order_number'                       => ['label' => 'lovata.ordersshopaholic::lang.field.order_number'],
            OrderExport::RELATION_CURRENCY       => ['label' => 'lovata.shopaholic::lang.field.currency'],
            'total_price'                        => ['label' => 'lovata.ordersshopaholic::lang.field.total_price'],
            'shipping_price'                     => ['label' => 'lovata.ordersshopaholic::lang.field.shipping_price'],
            'position_total_price'               => ['label' => 'lovata.ordersshopaholic::lang.field.position_total_price'],
            OrderExport::RELATION_SHIPPING_TYPE  => ['label' => 'lovata.ordersshopaholic::lang.field.shipping_type'],
            OrderExport::RELATION_PAYMENT_METHOD => ['label' => 'lovata.ordersshopaholic::lang.field.payment_method'],
            'created_at'                         => ['label' => 'lovata.toolbox::lang.field.created_at'],
            'updated_at'                         => ['label' => 'lovata.toolbox::lang.field.updated_at'],
        ];

        $arPropertyList = (array) DB::table('lovata_orders_shopaholic_addition_properties')
            ->where('active', true)
            ->lists('name', 'code');

        if (empty($arPropertyList)) {
            return $arFieldList;
        }

        foreach ($arPropertyList as $sField => $sLabel) {
            $sField = 'property.'.$sField;
            $arFieldList[$sField] = ['label' => $sLabel];
        }

        return $arFieldList;
    }
}
