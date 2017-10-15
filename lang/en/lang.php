<?php return [
    'plugin'         => [
        'name'        => 'Orders for Shopaholic',
        'description' => 'User cart and order creating',
    ],
    'component'      => [
        'cart_name'                       => 'Cart',
        'cart_description'                => '',
        'ordering_name'                   => 'Ordering',
        'ordering_description'            => 'Create new order',
        'payment_method_list_name'        => 'Payment methods',
        'payment_method_list_description' => '',
        'shipping_type_list_name'         => 'Shipping types',
        'shipping_type_list_description'  => '',
    ],
    'tab'            => [
        'info'        => 'Данные заказа',
        'client_info' => 'Данные клиента',
        'offers_info' => 'Список товаров',
        'order_settings' => 'Корзина и заказы',
    ],
    'message'        => [
        'empty_cart'          => 'Cart is empty',
        'offer_not_found'     => 'Offer not found',
        'insufficient_amount' => 'The offer is out of stock',
    ],
    'field'          => [
        'status'       => 'Status',
        'order_number' => 'Order number',
        'user'         => 'Buyer',

        'new'         => 'New',
        'canceled'    => 'Canceled',
        'complete'    => 'Complete',
        'in_progress' => 'In progress',

        'total_price'        => 'Total price',
        'shipping_price'     => 'Shipping price',
        'catalog_price'      => 'Current price',
        'offer_list'         => 'Offer list',
        'offers_total_price' => 'Total price of offers',
        'shipping_type'      => 'Shipping type',
        'payment_method'     => 'Payment method',
    ],
    'settings'       => [
        'cart_cookie_lifetime'           => 'Life time of cart ID in cookie (min.)',
        'check_offer_quantity'        => 'When checking the available quantity of offers when creating an order',
        'decrement_offer_quantity' => 'Automatic reduction of the available quantity of offers when creating an order',
        'create_new_user'                => 'Automatically create a new user when creating an order',

        'order_create_email' => 'Email for sending mail when creating an order',
    ],
    'menu'           => [
        'orders'          => 'Orders',
        'statuses'        => 'Statuses',
        'payment_methods' => 'Payment methods',
        'shipping_types'  => 'Shipping types',
    ],
    'order'         => [
        'name'          => 'order',
        'list_title'    => 'Order list',
    ],
    'status'         => [
        'name'          => 'status',
        'list_title'    => 'Status list',
    ],
    'payment_method' => [
        'name'          => 'payment method',
        'list_title'    => 'Payment methods',
    ],
    'shipping_type'  => [
        'name'          => 'shipping type',
        'list_title'    => 'Shipping types',
    ],
    'permission'     => [
        'order'         => 'Manage orders',
        'status'        => 'Manage status list',
        'payment_type'  => 'Manage payment methods',
        'delivery_type' => 'Manage payment methods',
    ],
];