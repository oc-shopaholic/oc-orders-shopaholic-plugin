<?php namespace Lovata\OrdersShopaholic\Classes\Item;

use Lovata\Toolbox\Classes\Item\ElementItem;

use Lovata\Shopaholic\Models\Settings;
use Lovata\OrdersShopaholic\Models\ShippingType;
use Lovata\Toolbox\Traits\Helpers\PriceHelperTrait;

/**
 * Class ShippingTypeItem
 * @package Lovata\Shopaholic\Classes\Item
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 *
 * @property        $id
 * @property string $name
 * @property string $code
 * @property string $preview_text
 *
 * @property string $price
 * @property float  $price_value
 * @property string $currency
 */
class ShippingTypeItem extends ElementItem
{
    use PriceHelperTrait;

    const MODEL_CLASS = ShippingType::class;

    public $arPriceField = ['price'];

    /** @var ShippingType */
    protected $obElement = null;

    /**
     * Get currency value
     * @return null|string
     */
    protected function getCurrencyAttribute()
    {
        return Settings::getValue('currency');
    }
}