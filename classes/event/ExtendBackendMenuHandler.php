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
        $arMenuItemList = [
            'orders-shopaholic-menu-promo-mechanism' => [
                'label' => 'lovata.ordersshopaholic::lang.menu.promo_mechanism',
                'url'           => Backend::url('lovata/ordersshopaholic/promomechanisms'),
                'icon'          => 'oc-icon-percent',
                'permissions'   => ['shopaholic-promo-mechanism'],
                'order'         => 1000,
            ],
            'orders-shopaholic-menu-increase-price-mechanism' => [
                'label' => 'lovata.ordersshopaholic::lang.menu.increase_price_mechanism',
                'url'           => Backend::url('lovata/ordersshopaholic/increasepricemechanisms'),
                'icon'          => 'oc-icon-percent',
                'permissions'   => ['shopaholic-promo-mechanism'],
                'order'         => 1100,
            ],
        ];

        $obManager->addSideMenuItems('Lovata.Shopaholic', 'shopaholic-menu-promo', $arMenuItemList);
    }
}
