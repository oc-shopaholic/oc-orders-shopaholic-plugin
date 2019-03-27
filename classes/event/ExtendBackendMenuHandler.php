<?php namespace Lovata\OrdersShopaholic\Classes\Event;

use Backend;

use Lovata\Toolbox\Classes\Event\AbstractBackendMenuHandler;

/**
 * Class ExtendBackendMenuHandler
 * @package Lovata\OrdersShopaholic\Classes\Event
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class ExtendBackendMenuHandler extends AbstractBackendMenuHandler
{
    /**
     * Add menu items
     * @param \Backend\Classes\NavigationManager $obManager
     */
    protected function addMenuItems($obManager)
    {
        $arMenuItemData = [
            'label' => 'lovata.ordersshopaholic::lang.menu.promo_mechanism',
            'url'           => Backend::url('lovata/ordersshopaholic/promomechanisms'),
            'icon'          => 'oc-icon-percent',
            'permissions'   => ['shopaholic-promo-mechanism'],
            'order'         => 1000,
        ];

        $obManager->addSideMenuItem('Lovata.Shopaholic', 'shopaholic-menu-promo', 'orders-shopaholic-menu-promo-mechanism', $arMenuItemData);
    }
}
