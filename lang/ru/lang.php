<?php return [
    'plugin'         => [
        'name'        => 'Orders for Shopaholic',
        'description' => 'Корзина и оформление заказа',
    ],
    'component' => [
        'cart_name'                       => 'Корзина',
        'cart_description'                => '',
        'make_order_name'                 => 'Создание заказа',
        'make_order_description'          => '',
        'payment_method_list_name'        => 'Методы оплаты',
        'payment_method_list_description' => '',
        'shipping_type_list_name'         => 'Способы доставки',
        'shipping_type_list_description'  => '',
        'order_page_name'                 => 'Страница заказа',
        'order_page_description'          => '',
    ],
    'tab'            => [
        'info'        => 'Данные заказа',
        'offers_info' => 'Список товаров',
        'order_settings' => 'Корзина и заказы',
    ],
    'message'        => [
        'empty_cart'          => 'Корзина пуста',
        'offer_not_found'     => 'Товар не найден',
        'insufficient_amount' => 'Товара нет в наличии',
    ],
    'field'          => [
        'status'       => 'Статус',
        'order_number' => 'Номер заказа',
        'user'         => 'Покупатель',

        'new'         => 'Новый',
        'canceled'    => 'Отменен',
        'complete'    => 'Завершен',
        'in_progress' => 'Выполняется',

        'total_price'        => 'Сумма заказа',
        'shipping_price'     => 'Стоимость доставки',
        'catalog_price'      => 'Текущая цена',
        'offer_list'         => 'Список товаров в заказе',
        'offers_total_price' => 'Стоимость товаров',
        'shipping_type'      => 'Способ доставки',
        'payment_method'     => 'Способ оплаты',
    ],
    'settings' => [
        'cart_cookie_lifetime'     => 'Время жизни ID корзины в cookie (мин.)',
        'check_offer_quantity'     => 'Проверять доступное количество товара при создании заказа',
        'decrement_offer_quantity' => 'Автоматически уменьшать доступное количетсво товара при создании заказа',
        'create_new_user'          => 'Автоматически создавать нового пользователя при создании заказа',

        'order_create_email' => 'Email для отправки уведомлений при создании заказа',
    ],
    'menu'           => [
        'orders'          => 'Заказы',
        'statuses'        => 'Статусы',
        'payment_methods' => 'Методы оплаты',
        'shipping_types'  => 'Типы доставки',
    ],
    'order'         => [
        'name'          => 'заказа',
        'list_title'    => 'Список заказов',
    ],
    'status'         => [
        'name'          => 'статуса',
        'list_title'    => 'Список статусов',
    ],
    'payment_method' => [
        'name'          => 'метода оплаты',
        'list_title'    => 'Методы оплаты',
    ],
    'shipping_type'  => [
        'name'          => 'способа доставки',
        'list_title'    => 'Способы доставки',
    ],
    'permission'     => [
        'order'         => 'Управление заказами',
        'status'        => 'Управление статусами заказа',
        'payment_type'  => 'Управление методами оплаты',
        'delivery_type' => 'Управление типами доставки',
    ],
];