<?php namespace Lovata\OrdersShopaholic\Models;

use Kharanenka\Helper\CustomValidationMessage;
use Model;
use Carbon\Carbon;
use Kharanenka\Helper\CCache;
use October\Rain\Database\Builder;
use October\Rain\Database\Collection;
use Lovata\Shopaholic\Plugin;
use October\Rain\Database\Relations\HasMany;
use October\Rain\Database\Traits\Sortable;
use October\Rain\Database\Traits\Validation;

/**
 * Class PaymentMethod
 * @package Lovata\Shopaholic\Models
 * @author Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 * 
 * @mixin Builder
 * @mixin \Eloquent
 *
 * @property $id
 * @property string $code
 * @property string $name
 * @property string $description
 * @property int $sort_order
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Collection|Order[] $order
 *
 * @method static $this|HasMany order()
 */
class PaymentMethod extends Model
{
    use Validation;
    use Sortable;
    use CustomValidationMessage;

    const CACHE_TAG_ELEMENT = 'shopaholic-order-payment-method-element';
    const CACHE_TAG_LIST = 'shopaholic-order-payment-method-list';
    
    public $table = 'lovata_ordersshopaholic_payment_methods';

    /** Validation */
    public $rules = [
        'name' => 'required',
        'code' => 'required|unique:lovata_ordersshopaholic_statuses',
    ];
    public $customMessages = [];
    public $attributeNames = [];

    protected $fillable = [
        'code',
        'name',
        'description',
    ];

    protected $dates = ['created_at', 'updated_at'];

    public $hasMany = [
        'order' => 'Lovata\OrdersShopaholic\Models\Order'
    ];

    public function afterSave()
    {
        $this->clearCache();
    }

    public function afterDelete()
    {
        $this->clearCache();
    }

    /**
     * Clear cache data
     */
    protected function clearCache()
    {
        CCache::clear([Plugin::CACHE_TAG, self::CACHE_TAG_LIST], self::CACHE_TAG_LIST);
    }

    /**
     * @return array
     */
    public function getData()
    {
        $arResult = [
            'id' => $this->id,
            'name' => $this->name,
            'code' => $this->code,
            'description' => $this->description,
        ];

        return $arResult;
    }

    /**
     * Get element list
     * @return array|null
     */
    public static function getAll()
    {
        //Get cache data
        $arCacheTags = [Plugin::CACHE_TAG, self::CACHE_TAG_LIST];
        $sCacheKey = self::CACHE_TAG_LIST;

        $arResult = CCache::get($arCacheTags, $sCacheKey);
        if(!empty($arResult)) {
            return $arResult;
        }

        $arResult = [];

        $obElementList = self::orderBy('sort_order', 'ASC')->get();
        if($obElementList->isEmpty()) {
            return $arResult;
        }

        /** @var $this $obElement */
        foreach ($obElementList as $obElement) {
            $arResult[$obElement->id] = $obElement->getData();
        }

        //Set cache data
        CCache::forever($arCacheTags, $sCacheKey, $arResult);

        return $arResult;
    }
}