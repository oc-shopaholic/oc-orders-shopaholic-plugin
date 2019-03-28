<?php namespace Lovata\OrdersShopaholic\Classes\Event\Order;

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
        });
    }

    /**
     * Extend products controller
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
}
