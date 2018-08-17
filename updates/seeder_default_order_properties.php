<?php namespace Lovata\OrdersShopaholic\Updates;

use Seeder;
use Lovata\OrdersShopaholic\Models\OrderProperty;

/**
 * Class SeederDefaultOrderProperties
 * @package Lovata\OrdersShopaholic\Updates
 */
class SeederDefaultOrderProperties extends Seeder
{
    public function run()
    {
        $arPropertyList = [
            [
                'active' => true,
                'name' => 'lovata.toolbox::lang.field.email',
                'code' => 'email',
                'slug' => 'email',
                'type' => 'input',
                'settings' => ['tab' => 'lovata.ordersshopaholic::lang.field.user'],
                'sort_order' => 1
            ], [
                'active' => true,
                'name' => 'lovata.ordersshopaholic::lang.field.name',
                'code' => 'name',
                'slug' => 'name',
                'type' => 'input',
                'settings' => ['tab' => 'lovata.ordersshopaholic::lang.field.user'],
                'sort_order' => 2
            ], [
                'active' => true,
                'name' => 'lovata.ordersshopaholic::lang.field.last_name',
                'code' => 'last_name',
                'slug' => 'last_name',
                'type' => 'input',
                'settings' => ['tab' => 'lovata.ordersshopaholic::lang.field.user'],
                'sort_order' => 3
            ], [
                'active' => true,
                'name' => 'lovata.toolbox::lang.field.phone',
                'code' => 'phone',
                'slug' => 'phone',
                'type' => 'input',
                'settings' => ['tab' => 'lovata.ordersshopaholic::lang.field.user'],
                'sort_order' => 4
            ],
        ];

        foreach ($arPropertyList as $arPropertyData) {
            OrderProperty::create($arPropertyData);
        }
    }
}