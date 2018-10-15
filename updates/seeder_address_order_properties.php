<?php namespace Lovata\OrdersShopaholic\Updates;

use Seeder;
use Lovata\OrdersShopaholic\Models\OrderProperty;

/**
 * Class SeederAddressOrderProperties
 * @package Lovata\OrdersShopaholic\Updates
 */
class SeederAddressOrderProperties extends Seeder
{
    public function run()
    {
        $arPropertyList = [
            // Data for billing address
            [
                'active' => true,
                'name' => 'lovata.toolbox::lang.field.country',
                'slug' => 'billing_country',
                'code' => 'billing_country',
                'type' => 'input',
                'settings' => ['tab' => 'lovata.ordersshopaholic::lang.tab.billing_address'],
            ],
            [
                'active' => true,
                'name' => 'lovata.toolbox::lang.field.state',
                'slug' => 'billing_state',
                'code' => 'billing_state',
                'type' => 'input',
                'settings' => ['tab' => 'lovata.ordersshopaholic::lang.tab.billing_address'],
            ],
            [
                'active' => true,
                'name' => 'lovata.toolbox::lang.field.city',
                'slug' => 'billing_city',
                'code' => 'billing_city',
                'type' => 'input',
                'settings' => ['tab' => 'lovata.ordersshopaholic::lang.tab.billing_address'],
            ],
            [
                'active' => true,
                'name' => 'lovata.toolbox::lang.field.street',
                'slug' => 'billing_street',
                'code' => 'billing_street',
                'type' => 'input',
                'settings' => ['tab' => 'lovata.ordersshopaholic::lang.tab.billing_address'],
            ],
            [
                'active' => true,
                'name' => 'lovata.toolbox::lang.field.house',
                'slug' => 'billing_house',
                'code' => 'billing_house',
                'type' => 'input',
                'settings' => ['tab' => 'lovata.ordersshopaholic::lang.tab.billing_address'],
            ],
            [
                'active' => true,
                'name' => 'lovata.toolbox::lang.field.flat',
                'slug' => 'billing_flat',
                'code' => 'billing_flat',
                'type' => 'input',
                'settings' => ['tab' => 'lovata.ordersshopaholic::lang.tab.billing_address'],
            ],
            [
                'active' => true,
                'name' => 'lovata.toolbox::lang.field.address1',
                'slug' => 'billing_address1',
                'code' => 'billing_address1',
                'type' => 'input',
                'settings' => ['tab' => 'lovata.ordersshopaholic::lang.tab.billing_address'],
            ],
            [
                'active' => true,
                'name' => 'lovata.toolbox::lang.field.address2',
                'slug' => 'billing_address2',
                'code' => 'billing_address2',
                'type' => 'input',
                'settings' => ['tab' => 'lovata.ordersshopaholic::lang.tab.billing_address'],
            ],
            [
                'active' => true,
                'name' => 'lovata.toolbox::lang.field.postcode',
                'slug' => 'billing_postcode',
                'code' => 'billing_postcode',
                'type' => 'input',
                'settings' => ['tab' => 'lovata.ordersshopaholic::lang.tab.billing_address'],
            ],
            // Data for shipping address
            [
                'active' => true,
                'name' => 'lovata.toolbox::lang.field.country',
                'slug' => 'shipping_country',
                'code' => 'shipping_country',
                'type' => 'input',
                'settings' => ['tab' => 'lovata.ordersshopaholic::lang.tab.shipping_address'],
            ],
            [
                'active' => true,
                'name' => 'lovata.toolbox::lang.field.state',
                'slug' => 'shipping_state',
                'code' => 'shipping_state',
                'type' => 'input',
                'settings' => ['tab' => 'lovata.ordersshopaholic::lang.tab.shipping_address'],
            ],
            [
                'active' => true,
                'name' => 'lovata.toolbox::lang.field.city',
                'slug' => 'shipping_city',
                'code' => 'shipping_city',
                'type' => 'input',
                'settings' => ['tab' => 'lovata.ordersshopaholic::lang.tab.shipping_address'],
            ],
            [
                'active' => true,
                'name' => 'lovata.toolbox::lang.field.street',
                'slug' => 'shipping_street',
                'code' => 'shipping_street',
                'type' => 'input',
                'settings' => ['tab' => 'lovata.ordersshopaholic::lang.tab.shipping_address'],
            ],
            [
                'active' => true,
                'name' => 'lovata.toolbox::lang.field.house',
                'slug' => 'shipping_house',
                'code' => 'shipping_house',
                'type' => 'input',
                'settings' => ['tab' => 'lovata.ordersshopaholic::lang.tab.shipping_address'],
            ],
            [
                'active' => true,
                'name' => 'lovata.toolbox::lang.field.flat',
                'slug' => 'shipping_flat',
                'code' => 'shipping_flat',
                'type' => 'input',
                'settings' => ['tab' => 'lovata.ordersshopaholic::lang.tab.shipping_address'],
            ],
            [
                'active' => true,
                'name' => 'lovata.toolbox::lang.field.address1',
                'slug' => 'shipping_address1',
                'code' => 'shipping_address1',
                'type' => 'input',
                'settings' => ['tab' => 'lovata.ordersshopaholic::lang.tab.shipping_address'],
            ],
            [
                'active' => true,
                'name' => 'lovata.toolbox::lang.field.address2',
                'slug' => 'shipping_address2',
                'code' => 'shipping_address2',
                'type' => 'input',
                'settings' => ['tab' => 'lovata.ordersshopaholic::lang.tab.shipping_address'],
            ],
            [
                'active' => true,
                'name' => 'lovata.toolbox::lang.field.postcode',
                'slug' => 'shipping_postcode',
                'code' => 'shipping_postcode',
                'type' => 'input',
                'settings' => ['tab' => 'lovata.ordersshopaholic::lang.tab.shipping_address'],
            ],
        ];

        foreach ($arPropertyList as $arPropertyData) {
            //Find property by code
            $obProperty = OrderProperty::getByCode($arPropertyData['code'])->first();
            if (!empty($obProperty)) {
                continue;
            }

            OrderProperty::create($arPropertyData);
        }
    }
}
