<?php return [
    'plugin'                   => [
        'name'        => 'Orders for Shopaholic',
        'description' => 'User cart and order creating',
    ],
    'component'                => [
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
        'user_address_name'               => 'User address',
        'user_address_description'        => 'Create/update/remove',
    ],
    'tab'                      => [
        'info'                       => 'Order data',
        'offers_info'                => 'Offer list',
        'order_settings'             => 'Order and cart',
        'gateway'                    => 'Gateway',
        'payment_data'               => 'Payment data',
        'discount_info'              => 'Discount info',
        'billing_address'            => 'Billing address',
        'shipping_address'           => 'Shipping address',
        'tasks'                      => 'Tasks',
        'shipping_restrictions_info' => 'Restrictions',
        'payment_restrictions_info'  => 'Restrictions',
    ],
    'message'                  => [
        'empty_cart'           => 'Cart is empty',
        'offer_not_found'      => 'Offer not found',
        'insufficient_amount'  => 'Offer is not available',
        'order_id_required'    => 'Relation between position and order is required',
        'cart_id_required'     => 'Relation between position and cart is required',
        'item_required'        => 'Relation between position and item is required',
        'without_condition'    => 'Without condition',
        'discount_not_applied' => 'Mechanism for changing prices has not been applied applied',
        'e_address_exists'     => 'This address already exists',
    ],
    'field'                    => [
        'status'       => 'Status',
        'order_number' => 'Order number',
        'user'         => 'Buyer',

        'new'         => 'New',
        'canceled'    => 'Canceled',
        'complete'    => 'Complete',
        'in_progress' => 'In progress',

        'total_price'                    => 'Total price',
        'old_total_price'                => 'Old total price',
        'position_price'                 => 'Position price',
        'old_position_price'             => 'old position price',
        'shipping_price'                 => 'Shipping price',
        'catalog_price'                  => 'Current price',
        'offer_list'                     => 'Offer list',
        'position_total_price'           => 'Total price of order positions',
        'shipping_type'                  => 'Shipping type',
        'payment_method'                 => 'Payment method',
        'is_user_show'                   => 'Show status to user',
        'user_status'                    => 'For user, status will be shown as',
        'color'                          => 'Color',
        'gateway_id'                     => 'Payment gateway',
        'gateway_currency'               => 'Gateway currency',
        'before_status_id'               => 'Order status before payment',
        'after_status_id'                => 'Order status after payment success',
        'cancel_status_id'               => 'Order status after payment cancel',
        'fail_status_id'                 => 'Order status after payment fail/error',
        'transaction_id'                 => 'Transaction ID',
        'payment_token'                  => 'Payment token',
        'payment_data'                   => 'Data that was sent to payment gateway',
        'payment_response'               => 'Data that was received from payment gateway',
        'send_purchase_request'          => 'Send request to payment gateway when creating order',
        'restore_cart'                   => 'Restore cart positions if payment has been canceled or failed',
        'gateway_field_value'            => 'Get value of ":field" field from value of order property field',
        'promo_mechanism'                => 'Promo mechanism',
        'increase_price_mechanism'       => 'Increase price mechanism',
        'priority_description'           => 'The <strong>higher</strong> the priority, the <strong>earlier</strong> the discount or price increase will be applied.',
        'discount_value'                 => 'Discount value',
        'discount_type'                  => 'Discount type',
        'increase_price_value'           => 'Value of price increase',
        'increase_price_type'            => 'Type of price increase',
        'price_change_value'             => 'Value of price change',
        'price_change_type'              => 'Type of price change',
        'discount_type_fixed'            => 'Fixed',
        'discount_type_percent'          => 'Percent',
        'final_discount'                 => 'Final discount',
        'final_discount_description'     => 'The final discount / price increase <strong>blocks the effect</strong> of other discounts / price increases with a lower priority. Otherwise, discounts / price increases with <strong>lower</strong> priority <strong>will be applied</strong>.',
        'final_increase_price'           => 'Final price increase',
        'auto_add'                       => 'Automatically apply mechanism for changing prices',
        'auto_add_description'           => 'The mechanism for changing prices will <strong>always be automatically</strong> applied to the user cart and added to the order.',
        'order_discount_list'            => 'List of available discounts and increase price mechanisms',
        'manager'                        => 'Manager',
        'task_date'                      => 'Notification date',
        'active_task'                    => 'Active tasks',
        'completed_task'                 => 'Completed tasks',
        'task_mail_template'             => 'Mail template for notification',
        'task_mail_template_description' => 'If mail template is not selected, the notification will not be sent.',
        'notification_sent'              => 'Notification sent',
        'applied_to_shipping_price'      => 'Applied to shipping price',
        'restriction'                    => 'Restriction',
        'shipping_type_api_class'        => 'API method',
        'decrease_price'                 => 'Discount',
        'increase_price'                 => 'Increase price',

        'order_discount_log_position_total_price' => 'List applied of mechanism for changing prices (position total price)',
        'order_discount_log_sipping_price'        => 'List applied of mechanism for changing prices (sipping price)',
        'order_discount_log_total_price'          => 'List applied of mechanism for changing prices (order total price)',

        'name'        => 'Name',
        'last_name'   => 'Last name',
        'middle_name' => 'Middle name',
    ],
    'settings'                 => [
        'cart_cookie_lifetime'                 => 'Life time of cart ID in cookie (min.)',
        'check_offer_quantity'                 => 'Check the available quantity of the product when creating an order',
        'decrement_offer_quantity'             => 'Automatic reduction of the available quantity of offers when creating an order',
        'create_new_user'                      => 'Automatically create a new user when creating an order',
        'generate_fake_email'                  => 'When creating a new user, generate a fake email, if the email field is empty',
        'send_email_after_creating_order'      => 'Send email after creating an order',
        'creating_order_mail_template'         => 'Mail template of creating orders (for users)',
        'creating_order_manager_mail_template' => 'Mail template of creating orders (for managers)',
        'creating_order_manager_email_list'    => 'Managers email list',
        'creating_order_manager_group'             => 'Send emails after creating an order to the backend user group',
        'creating_order_manager_group_description' => 'The list of email addresses of managers will be ignored if filled',
        'creating_order_manager_group_selected'    => 'Back-end administrators user group',

        'order_create_email' => 'Email for sending mail when creating an order',
    ],
    'menu'                     => [
        'orders'                              => 'Orders',
        'statuses'                            => 'Order statuses',
        'statuses_description'                => 'Manage order statuses',
        'payment_methods'                     => 'Payment methods',
        'payment_methods_description'         => 'Manage payment methods',
        'shipping_types'                      => 'Shipping types',
        'shipping_types_description'          => 'Manage shipping types',
        'order_property'                      => 'Additional order properties',
        'order_property_description'          => 'Manage additional order properties',
        'order_position_property'             => 'Additional order position properties',
        'order_position_property_description' => 'Manage additional order position properties',
        'promo_mechanism'                     => 'Promo mechanism',
        'increase_price_mechanism'            => 'Increase price mechanism',
    ],
    'order'                    => [
        'name'       => 'order',
        'list_title' => 'Order list',
    ],
    'buyer'                    => [
        'name'       => 'buyer',
        'list_title' => 'Buyer list',
    ],
    'order_position'           => [
        'name'       => 'position',
        'list_title' => 'Position list',
    ],
    'status'                   => [
        'name'       => 'status',
        'list_title' => 'Status list',
    ],
    'payment_method'           => [
        'name'       => 'payment method',
        'list_title' => 'Payment methods',
    ],
    'shipping_type'            => [
        'name'       => 'shipping type',
        'list_title' => 'Shipping types',
    ],
    'order_property'           => [
        'name'       => 'property',
        'list_title' => 'Property list',
    ],
    'task'                     => [
        'name'       => 'task',
        'list_title' => 'Task list',
    ],
    'promo_mechanism'          => [
        'name'                            => 'promo mechanism',
        'list_title'                      => 'Promo mechanism list',
        'amount_description'              => 'Discount will be applied if the amount is greater than or equal to',
        'offer_limit'                     => 'Offer quantity at which the discount will be applied',
        'offer_limit_description'         => 'Discount will be applied if offer quantity is greater than or equal to',
        'position_limit'                  => 'Position count at which the discount will be applied',
        'position_limit_description'      => 'Discount will be applied if position count is greater than or equal to',
        'quantity_limit'                  => 'Quantity limit for which the discount will be applied',
        'quantity_limit_description'      => 'If you set value = 1 and discount value = 100%, than the discount will be applied to one unit. If you set value = 0, than discount will be applied to all units',
        'quantity_limit_from'             => 'Quantity limit at which the discount will be repeated',
        'quantity_limit_from_description' => 'If you set value = 3, value of quantity limit for which the discount will be applied = 1 and discount value = 100%, than the discount will be applied to one unit and will be repeated every 3 offers (3 for the price of 2). If the value is = 0, then the value is ignored.',
        'shipping_type'                   => 'Discount will be applied if active shipping type is',
        'payment_method'                  => 'Discount will be applied if active payment method is',
    ],
    'increase_price_mechanism' => [
        'name'                            => 'price increase mechanism',
        'list_title'                      => 'Price increase mechanism list',
        'amount_description'              => 'Price increase will be applied if the amount is greater than or equal to',
        'offer_limit'                     => 'Offer quantity at which the price increase will be applied',
        'offer_limit_description'         => 'Price increase will be applied if offer quantity is greater than or equal to',
        'position_limit'                  => 'Position count at which the price increase will be applied',
        'position_limit_description'      => 'Price increase will be applied if position count is greater than or equal to',
        'quantity_limit'                  => 'Quantity limit for which the price increase will be applied',
        'quantity_limit_description'      => 'If you set value = 1 and price increase value = 100%, than the price increase will be applied to one unit. If you set value = 0, than price increase will be applied to all units',
        'quantity_limit_from'             => 'Quantity limit at which the price increase will be repeated',
        'quantity_limit_from_description' => 'If you set value = 3, value of quantity limit for which the price increase will be applied = 1 and price increase value = 100%, than the price increase will be applied to one unit and will be repeated every 3 offers (3 for the price of 2). If the value is = 0, then the value is ignored.',
        'shipping_type'                   => 'Price increase will be applied if active shipping type is',
        'payment_method'                  => 'Price increase will be applied if active payment method is',
    ],
    'restriction'              => [
        'name'       => 'restriction',
        'list_title' => 'Restriction list',
        'property'   => [
            'price_min' => 'Price min',
            'price_max' => 'Price max',
        ],
        'handler'    => [
            'by_total_price'    => 'By total price of cart positions',
            'by_shipping_type'  => 'By shipping type',
            'by_payment_method' => 'By payment method',
        ],
    ],
    'permission'               => [
        'order'           => 'Manage orders',
        'status'          => 'Manage status list',
        'payment_type'    => 'Manage payment methods',
        'delivery_type'   => 'Manage payment methods',
        'property'        => 'Manage additional properties of order',
        'promo_mechanism' => 'Manage promo mechanisms',
    ],
    'label'                    => [
        'order'   => 'Order',
        'product' => 'Product',
        'offer'   => 'Offer',
    ],
    'promo_mechanism_type'     => [
        'without_condition_discount_position'                         => 'Apply a mechanism for changing prices to the price of position without any conditions',
        'without_condition_discount_position_description'             => 'The mechanism for changing prices will be applied to the price of the <strong>position</strong> <strong>without checking any conditions</strong>. For example: Can be used when applying a coupon.',
        'without_condition_discount_min_price'                        => 'Apply a mechanism for changing prices to the position price with min price without any conditions',
        'without_condition_discount_min_price_description'            => 'The mechanism for changing prices will be applied to the price of the <strong>position with min price</strong> <strong>without checking any conditions</strong>. For example: Can be used when applying a coupon.',
        'without_condition_discount_position_total_price'             => 'Apply a mechanism for changing prices to the total price of positions without any conditions',
        'without_condition_discount_position_total_price_description' => 'The mechanism for changing prices will be applied to the <strong>total price of positions</strong> list <strong>without checking any conditions</strong>. For example: Can be used when applying a coupon.',
        'without_condition_discount_shipping_price'                   => 'Apply a mechanism for changing prices to the shipping price without any conditions',
        'without_condition_discount_shipping_price_description'       => 'The mechanism for changing prices will be applied to the <strong>shipping price</strong> <strong>without checking any conditions</strong>. For example: Can be used when applying a coupon.',
        'without_condition_discount_total_price'                      => 'Apply a mechanism for changing prices to the total price of order without any conditions',
        'without_condition_discount_total_price_description'          => 'The mechanism for changing prices will be applied to the <strong>total price</strong> of order <strong>without checking any conditions</strong>. For example: Can be used when applying a coupon.',

        'position_total_price_greater_discount_shipping_price'             => 'Apply a mechanism for changing prices to the shipping price if total price of position greater than "XX"',
        'position_total_price_greater_discount_shipping_price_description' => 'The mechanism for changing prices will be applied to the <strong>shipping price</strong> <strong>if total price of the position greater than the set value</strong>. For example: Discount is 5%, if the total price of position is >= 50$.',
        'position_total_price_greater_discount_total_price'                => 'Apply a mechanism for changing prices to the total price of order if total price of position greater than "XX"',
        'position_total_price_greater_discount_total_price_description'    => 'The mechanism for changing prices will be applied to the <strong>total price</strong> of order <strong>if total price of position greater then</strong>. For example: Discount is 5%, if total price of position is >= 50$.',

        'offer_quantity_greater_discount_position'                         => 'Apply a mechanism for changing prices to the position price if the total quantity of one offer in the order is greater than "XX"',
        'offer_quantity_greater_discount_position_description'             => 'The mechanism for changing prices will be applied to the price of the <strong>position</strong> <strong> if the total quantity of one offer in the order is greater than the set value</strong>. For example: Discount is 5%, if total quantity of offer "T-shirt size 52" is >= 3.',
        'offer_quantity_greater_discount_min_price'                        => 'Apply a mechanism for changing prices to the position with min price if the total quantity of one offer in the order is greater than "XX"',
        'offer_quantity_greater_discount_min_price_description'            => 'The mechanism for changing prices will be applied to the price of the <strong>position with min price</strong> <strong> if the total quantity of one offer in the order is greater than the set value</strong>. For example: Discount is 5%, if total quantity of offer "T-shirt size 52" is >= 3.',
        'offer_quantity_greater_discount_position_total_price'             => 'Apply a mechanism for changing prices to the total price of positions if the total quantity of one offer in the order is greater than "XX"',
        'offer_quantity_greater_discount_position_total_price_description' => 'The mechanism for changing prices will be applied to the <strong>total price of positions</strong> list <strong>if the total quantity of one offer in the order is greater than the set value</strong>. For example: Discount is 5%, if total quantity of offer "T-shirt size 52" is >= 3.',
        'offer_quantity_greater_discount_shipping_price'                   => 'Apply a mechanism for changing prices to the shipping price if the total quantity of one offer in the order is greater than "XX"',
        'offer_quantity_greater_discount_shipping_price_description'       => 'The mechanism for changing prices will be applied to the <strong>shipping price</strong> <strong>if the total quantity of one offer in the order is greater than the set value</strong>. For example: Discount is 5%, if total quantity of offer "T-shirt size 52" is >= 3.',
        'offer_quantity_greater_discount_total_price'                      => 'Apply a mechanism for changing prices to the order total price if the total quantity of one offer in the order is greater than "XX"',
        'offer_quantity_greater_discount_total_price_description'          => 'The mechanism for changing prices will be applied to the <strong>total price</strong> of order <strong>if the total quantity of one offer in the order is greater than the set value</strong>. For example: Discount is 5%, if total quantity of offer "T-shirt size 52" is >= 3.',

        'offer_total_quantity_greater_discount_position'                         => 'Apply a mechanism for changing prices to the position price if the total quantity of offers in the order is greater than "XX"',
        'offer_total_quantity_greater_discount_position_description'             => 'The mechanism for changing prices will be applied to the price of the <strong>position</strong> <strong> if the total quantity of offers in the order is greater than the set value</strong>. For example: Discount is 5%, if total quantity of offers "T-shirt size 52" (quantity = 2) + "T-shirt size 56" (quantity = 2) = 4 is >= 3.',
        'offer_total_quantity_greater_discount_min_price'                        => 'Apply a mechanism for changing prices to the position with min price if the total quantity of offers in the order is greater than "XX"',
        'offer_total_quantity_greater_discount_min_price_description'            => 'The mechanism for changing prices will be applied to the price of the <strong>position with min price</strong> <strong> if the total quantity of offers in the order is greater than the set value</strong>. For example: Discount is 5%, if total quantity of offers "T-shirt size 52" (quantity = 2) + "T-shirt size 56" (quantity = 2) = 4 is >= 3.',
        'offer_total_quantity_greater_discount_position_total_price'             => 'Apply a mechanism for changing prices to the total price of positions if the total quantity of offers in the order is greater than "XX"',
        'offer_total_quantity_greater_discount_position_total_price_description' => 'The mechanism for changing prices will be applied to the <strong>total price of positions</strong> list <strong>if the total quantity of offers in the order is greater than the set value</strong>. For example: Discount is 5%, if total quantity of offers "T-shirt size 52" (quantity = 2) + "T-shirt size 56" (quantity = 2) = 4 is >= 3.',
        'offer_total_quantity_greater_discount_shipping_price'                   => 'Apply a mechanism for changing prices to the shipping price if the total quantity of offers in the order is greater than "XX"',
        'offer_total_quantity_greater_discount_shipping_price_description'       => 'The mechanism for changing prices will be applied to the <strong>shipping price</strong> <strong>if the total quantity of offers in the order is greater than the set value</strong>. For example: Discount is 5%, if total quantity of offers "T-shirt size 52" (quantity = 2) + "T-shirt size 56" (quantity = 2) = 4 is >= 3.',
        'offer_total_quantity_greater_discount_total_price'                      => 'Apply a mechanism for changing prices to the order total price if the total quantity of offers in the order is greater than "XX"',
        'offer_total_quantity_greater_discount_total_price_description'          => 'The mechanism for changing prices will be applied to the <strong>total price</strong> of order <strong>if the total quantity of offers in the order is greater than the set value</strong>. For example: Discount is 5%, if total quantity of offers "T-shirt size 52" (quantity = 2) + "T-shirt size 56" (quantity = 2) = 4 is >= 3.',

        'position_count_greater_discount_position'                         => 'Apply a mechanism for changing prices to the position price if the position count in the order is greater than "XX"',
        'position_count_greater_discount_position_description'             => 'The mechanism for changing prices will be applied to the price of the <strong>position</strong> <strong> if the position count in the order is greater than the set value</strong>. For example: Discount is 5%, if position count is >= 3.',
        'position_count_greater_discount_min_price'                        => 'Apply a mechanism for changing prices to the position with min price if the position count in the order is greater than "XX"',
        'position_count_greater_discount_min_price_description'            => 'The mechanism for changing prices will be applied to the price of the <strong>position with min price</strong> <strong> if the position count in the order is greater than the set value</strong>. For example: Discount is 5%, if position count is >= 3.',
        'position_count_greater_discount_position_total_price'             => 'Apply a mechanism for changing prices to the total price of positions if the position count in the order is greater than "XX"',
        'position_count_greater_discount_position_total_price_description' => 'The mechanism for changing prices will be applied to the <strong>total price of positions</strong> list <strong>if the position count in the order is greater than the set value</strong>. For example: Discount is 5%, if position count is >= 3.',
        'position_count_greater_discount_shipping_price'                   => 'Apply a mechanism for changing prices to the shipping price if the position count in the order is greater than "XX"',
        'position_count_greater_discount_shipping_price_description'       => 'The mechanism for changing prices will be applied to the <strong>shipping price</strong> <strong>if the position count in the order is greater than the set value</strong>. For example: Discount is 5%, if position count is >= 3.',
        'position_count_greater_discount_total_price'                      => 'Apply a mechanism for changing prices to the order total price if the position count in the order is greater than "XX"',
        'position_count_greater_discount_total_price_description'          => 'The mechanism for changing prices will be applied to the <strong>total price</strong> of order <strong>if the position count in the order is greater than the set value</strong>. For example: Discount is 5%, if position count is >= 3.',
    ],
];
