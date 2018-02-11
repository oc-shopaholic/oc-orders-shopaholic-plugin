<?php namespace Lovata\OrdersShopaholic\Models;

use Model;
use October\Rain\Database\Traits\Sortable;
use October\Rain\Database\Traits\Validation;

use Kharanenka\Scope\ActiveField;
use Kharanenka\Scope\CodeField;

use Lovata\Shopaholic\Classes\Helper\PriceHelper;

/**
 * Class ShippingType
 * @package Lovata\Shopaholic\Models
 * @author Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 *
 * @mixin \October\Rain\Database\Builder
 * @mixin \Eloquent
 * 
 * @property $id
 * @property bool $active
 * @property string $code
 * @property string $name
 * @property string $preview_text
 * @property float $price
 * @property int $sort_order
 * @property \October\Rain\Argon\Argon $created_at
 * @property \October\Rain\Argon\Argon $updated_at
 *
 * @property \October\Rain\Database\Collection|Order[] $order
 * @method static Order|\October\Rain\Database\Relations\HasMany order()
 */
class ShippingType extends Model
{
    use ActiveField;
    use Validation;
    use Sortable;
    use CodeField;
    
    public $table = 'lovata_orders_shopaholic_shipping_types';

    public $implement = [
        '@RainLab.Translate.Behaviors.TranslatableModel',
    ];

    public $translatable = ['name', 'preview_text'];

    /** Validation */
    public $rules = [
        'name' => 'required',
        'code' => 'required|unique:lovata_orders_shopaholic_shipping_types',
    ];

    public $attributeNames = [
        'lovata.toolbox::lang.field.name',
        'lovata.toolbox::lang.field.code',
    ];

    protected $fillable = [
        'active',
        'code',
        'name',
        'sort_order',
        'price',
        'preview_text',
    ];

    protected $dates = ['created_at', 'updated_at'];

    public $hasMany = ['order' => Order::class];

    /**
     * Get price value
     * @return float
     */
    public function getPriceValue()
    {
        return $this->getAttributeFromArray('price');
    }

    /**
     * Accessor for price custom format
     * @param  string $dPrice
     * @return string
     */
    public function getPriceAttribute($dPrice)
    {
        /** @var PriceHelper $obPriceHelper */
        $obPriceHelper = app()->make(PriceHelper::class);

        return $obPriceHelper->get($dPrice);
    }

    /**
     * Format price to decimal format
     * @param  string $sPrice
     */
    public function setPriceAttribute($sPrice)
    {
        $sPrice = str_replace(',', '.', $sPrice);
        $sPrice = (float) preg_replace("/[^0-9\.]/", "", $sPrice);
        $this->attributes['price'] = $sPrice;
    }
}