<?php namespace Lovata\OrdersShopaholic\Classes\Item;

use Lovata\Toolbox\Classes\Item\ElementItem;

use Lovata\OrdersShopaholic\Models\PaymentMethod;

/**
 * Class PaymentMethodItem
 * @package Lovata\Shopaholic\Classes\Item
 * @author Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 *
 * @property        $id
 * @property string $name
 * @property string $code
 * @property string $preview_text
 */
class PaymentMethodItem extends ElementItem
{
    const MODEL_CLASS = PaymentMethod::class;

    /** @var PaymentMethod */
    protected $obElement = null;
}
