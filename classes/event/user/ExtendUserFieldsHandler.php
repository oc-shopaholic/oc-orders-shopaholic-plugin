<?php namespace Lovata\OrdersShopaholic\Classes\Event\User;

use Lovata\Toolbox\Classes\Helper\UserHelper;
use Lovata\Toolbox\Classes\Event\AbstractBackendFieldHandler;

/**
 * Class ExtendUserFieldsHandler
 * @package Lovata\OrdersShopaholic\Classes\Event\User
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class ExtendUserFieldsHandler extends AbstractBackendFieldHandler
{
    /**
     * Add listeners
     * @param \Illuminate\Events\Dispatcher $obEvent
     */
    public function subscribe($obEvent)
    {
        $sUserPluginName = UserHelper::instance()->getPluginName();
        if (empty($sUserPluginName)) {
            return;
        }

        parent::subscribe($obEvent);
    }

    /**
     * Extend form fields
     * @param \Backend\Widgets\Form $obWidget
     */
    protected function extendFields($obWidget)
    {
        $arAdditionFields = [
            'order'          => [
                'type'    => 'partial',
                'tab'     => 'lovata.ordersshopaholic::lang.menu.orders',
                'path'    => '$/lovata/ordersshopaholic/views/order.htm',
                'context' => ['update'],
            ],
            'active_task'    => [
                'type'    => 'partial',
                'label'   => 'lovata.ordersshopaholic::lang.field.active_task',
                'tab'     => 'lovata.ordersshopaholic::lang.tab.tasks',
                'path'    => '$/lovata/ordersshopaholic/views/active_task.htm',
                'context' => ['update'],
            ],
            'completed_task' => [
                'type'    => 'partial',
                'label'   => 'lovata.ordersshopaholic::lang.field.completed_task',
                'tab'     => 'lovata.ordersshopaholic::lang.tab.tasks',
                'path'    => '$/lovata/ordersshopaholic/views/completed_task.htm',
                'context' => ['update'],
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
        return UserHelper::instance()->getUserModel();
    }

    /**
     * Get controller class name
     * @return string
     */
    protected function getControllerClass() : string
    {
        return UserHelper::instance()->getUserController();
    }
}
