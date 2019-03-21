<?php namespace Lovata\OrdersShopaholic\Classes\Event\Restriction;

class ByPrice {

    /**
     * Add restriction fields
     * @param \Backend\Widgets\Form $obWidget
     */
    public function getFields()
    {
        return [
            'property[price_min]' => [
                'label' => 'lovata.ordersshopaholic::lang.restriction.property.price_min',
                'tab' => 'lovata.toolbox::lang.tab.settings',
                'span' => 'left',
                'type' => 'number',
                'size' => 'small',
                'context' => ['update', 'preview']
            ],
            'property[price_max]' => [
                'label' => 'lovata.ordersshopaholic::lang.restriction.property.price_max',
                'tab' => 'lovata.toolbox::lang.tab.settings',
                'span' => 'right',
                'type' => 'number',
                'size' => 'small',
                'context' => ['update', 'preview']
            ],
        ];
    }

    public function run($restriction, $cart) {

        $total_price = $cart["position_total_price"]["price_value"] ?? 0;

        $price_min = $restriction->property['price_min'] ?? 0;
        $price_max = $restriction->property['price_max'] ?? 0;

        if($total_price >= $price_min  && ( $total_price <=  $price_max || $price_max == 0)) {

            return true;
        }

        return false;
    }
}