<?php namespace Lovata\OrdersShopaholic\Classes\Restriction;

use Lovata\OrdersShopaholic\Interfaces\CheckRestrictionInterface;

/**
 * Class RestrictionByTotalPrice
 * @package Lovata\OrdersShopaholic\Classes\Restriction
 * @author Tsagan Noniev, deploy@rubium.ru, Rubium Web
 */
class RestrictionByTotalPrice implements CheckRestrictionInterface
{
    /**
     * Get backend fields for restriction settings
     * @return array
     */
    public static function getFields() : array
    {
        return [
            'property[price_min]' => [
                'label'   => 'lovata.ordersshopaholic::lang.restriction.property.price_min',
                'tab'     => 'lovata.toolbox::lang.tab.settings',
                'span'    => 'left',
                'type'    => 'number',
                'context' => ['update', 'preview']
            ],
            'property[price_max]' => [
                'label'   => 'lovata.ordersshopaholic::lang.restriction.property.price_max',
                'tab'     => 'lovata.toolbox::lang.tab.settings',
                'span'    => 'right',
                'type'    => 'number',
                'context' => ['update', 'preview']
            ],
        ];
    }

    /**
     * Check restriction of shipping type
     * @return bool
     */
    public function check() : bool
    {
        $total_price = $cart["position_total_price"]["price_value"] ?? 0;

        $price_min = $restriction->property['price_min'] ?? 0;
        $price_max = $restriction->property['price_max'] ?? 0;

        if($total_price >= $price_min  && ( $total_price <=  $price_max || $price_max == 0)) {

            return true;
        }

        return false;
    }
}