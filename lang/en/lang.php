<?php return [
    'plugin'         => [
        'name'        => 'Orders for Shopaholic',
        'description' => 'User cart and order creating',
    ],
    'component'      => [
        'cart_name'                       => 'Cart',
        'cart_description'                => '',
        'make_order_name'                 => 'Make order',
        'make_order_description'          => 'Create new order',
        'payment_method_list_name'        => 'Payment methods',
        'payment_method_list_description' => '',
        'shipping_type_list_name'         => 'Shipping types',
        'shipping_type_list_description'  => '',
        'status_list_name'                => 'Order statuses',
        'status_list_description'         => '',
        'order_page_name'                 => 'Order page',
        'order_page_description'          => '',
        'send_payment_purchase'           => 'Send payment purchase after creating order',
    ],
    'tab'            => [
        'info'             => 'Order data',
        'offers_info'      => 'Offer list',
        'order_settings'   => 'Order and cart',
        'gateway'          => 'Gateway',
        'payment_data'     => 'Payment data',
    ],
    'message'        => [
        'empty_cart'          => 'Cart is empty',
        'offer_not_found'     => 'Offer not found',
        'insufficient_amount' => 'Offer is not available',
        'order_id_required'   => 'Relation between position and order is required',
        'cart_id_required'    => 'Relation between position and cart is required',
        'item_required'       => 'Relation between position and item is required',
    ],
    'field'          => [
        'status'       => 'Status',
        'order_number' => 'Order number',
        'user'         => 'Buyer',

        'new'         => 'New',
        'canceled'    => 'Canceled',
        'complete'    => 'Complete',
        'in_progress' => 'In progress',

        'total_price'          => 'Total price',
        'shipping_price'       => 'Shipping price',
        'catalog_price'        => 'Current price',
        'offer_list'           => 'Offer list',
        'position_total_price' => 'Total price of order positions',
        'shipping_type'        => 'Shipping type',
        'payment_method'       => 'Payment method',
        'is_user_show'         => 'Show status to user',
        'user_status'          => 'For user, status will be shown as',
        'gateway_id'           => 'Payment gateway',
        'gateway_currency'     => 'Gateway currency',
        'before_status_id'     => 'Order status before payment',
        'after_status_id'      => 'Order status after payment success',
        'cancel_status_id'     => 'Order status after payment cancel',
        'fail_status_id'       => 'Order status after payment fail/error',
        'transaction_id'       => 'Transaction ID',
        'payment_data'         => 'Data that was sent to payment gateway',
        'payment_response'     => 'Data that was received from payment gateway',
    ],
    'settings'       => [
        'cart_cookie_lifetime'                 => 'Life time of cart ID in cookie (min.)',
        'check_offer_quantity'                 => 'Check the available quantity of the product when creating an order',
        'decrement_offer_quantity'             => 'Automatic reduction of the available quantity of offers when creating an order',
        'create_new_user'                      => 'Automatically create a new user when creating an order',
        'generate_fake_email'                  => 'When creating a new user, generate a fake email, if the email field is empty',
        'send_email_after_creating_order'      => 'Send email after creating an order',
        'creating_order_mail_template'         => 'Mail template of creating orders (for users)',
        'creating_order_manager_mail_template' => 'Mail template of creating orders (for managers)',
        'creating_order_manager_email_list'    => 'Managers email list',

        'order_create_email' => 'Email for sending mail when creating an order',
    ],
    'menu'           => [
        'orders'                  => 'Orders',
        'statuses'                => 'Statuses',
        'payment_methods'         => 'Payment methods',
        'shipping_types'          => 'Shipping types',
        'order_property'          => 'Additional order properties',
        'order_position_property' => 'Additional order position properties',
    ],
    'order'          => [
        'name'       => 'order',
        'list_title' => 'Order list',
    ],
    'buyer'          => [
        'name'       => 'buyer',
        'list_title' => 'Buyer list',
    ],
    'order_position' => [
        'name'       => 'position',
        'list_title' => 'Position list',
    ],
    'status'         => [
        'name'       => 'status',
        'list_title' => 'Status list',
    ],
    'payment_method' => [
        'name'       => 'payment method',
        'list_title' => 'Payment methods',
    ],
    'shipping_type'  => [
        'name'       => 'shipping type',
        'list_title' => 'Shipping types',
    ],
    'order_property' => [
        'name'       => 'property',
        'list_title' => 'Property list',
    ],
    'permission'     => [
        'order'         => 'Manage orders',
        'status'        => 'Manage status list',
        'payment_type'  => 'Manage payment methods',
        'delivery_type' => 'Manage payment methods',
        'property'      => 'Manage additional properties of order',
    ],
    'label'          => [
        'order'   => 'Order',
        'product' => 'Product',
        'offer'   => 'Offer',
    ],
];