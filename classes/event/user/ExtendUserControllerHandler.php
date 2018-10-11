<?php namespace Lovata\OrdersShopaholic\Classes\Event\User;

use Lovata\Toolbox\Classes\Helper\UserHelper;
use Lovata\Toolbox\Classes\Event\AbstractExtendRelationConfigHandler;

/**
 * Class ExtendUserControllerHandler
 * @package Lovata\OrdersShopaholic\Classes\Event\User
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class ExtendUserControllerHandler extends AbstractExtendRelationConfigHandler
{
    /**
     * Add listeners
     */
    public function subscribe()
    {
        $sUserPluginName = UserHelper::instance()->getPluginName();
        if (empty($sUserPluginName)) {
            return;
        }

        parent::subscribe();
    }

    /**
     * Get controller class name
     * @return string
     */
    protected function getControllerClass() : string
    {
        return UserHelper::instance()->getUserController();
    }

    /**
     * Get path to config file
     * @return string
     */
    protected function getConfigPath() : string
    {
        return '$/lovata/ordersshopaholic/config/user_config_relation.yaml';
    }
}
