<?php namespace Lovata\OrdersShopaholic\Models;

use Carbon\Carbon;
use Kharanenka\Helper\CustomValidationMessage;
use Model;
use Kharanenka\Helper\CCache;
use Lovata\Shopaholic\Plugin;
use Lovata\Toolbox\Plugin as ToolboxPlugin;
use October\Rain\Database\Builder;
use October\Rain\Database\Collection;
use October\Rain\Database\Relations\HasMany;
use October\Rain\Database\Traits\Sortable;
use October\Rain\Database\Traits\Validation;

/**
 * Class Status
 * @package Lovata\OrdersShopaholic\Models
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
class Status extends Model
{
    use Validation;
    use Sortable;
    use CustomValidationMessage;
    
    const STATUS_NEW = 'new';
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_COMPETE = 'complete';
    const STATUS_CANCELED = 'canceled';

    const CACHE_TAG_ELEMENT = 'shopaholic-order-status-element';
    const CACHE_TAG_LIST = 'shopaholic-order-status-list';
    
    public $table = 'lovata_ordersshopaholic_statuses';

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

    /**
     * Status constructor.
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        $this->setCustomMessage(ToolboxPlugin::NAME, ['required', 'unique']);
        $this->setCustomAttributeName(ToolboxPlugin::NAME, ['name', 'code']);
        parent::__construct($attributes);
    }

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

    /**
     * Get status data by code
     * @param string $sCode
     * @return array
     */
    public static function getStatusByCode($sCode) {

        $arStatusList = self::getAll();
        if(empty($arStatusList) || empty($sCode)) {
            return [];
        }

        foreach($arStatusList as $arStatusData) {
            if($arStatusData['code'] == $sCode) {
                return $arStatusData;
            }
        }

        return [];
    }

    /**
     * Get status ID by code
     * @param string $sCode
     * @return int
     */
    public static function getStatusIDByCode($sCode) {

        $arStatusList = self::getAll();
        if(empty($arStatusList) || empty($sCode)) {
            return 0;
        }

        foreach($arStatusList as $arStatusData) {
            if($arStatusData['code'] == $sCode) {
                return $arStatusData['id'];
            }
        }

        return 0;
    }

    /**
     * Get blocked status ID list
     * @return array
     */
    public static function getBlockedStatusList() {

        $arStatusList = self::getAll();
        if(empty($arStatusList)) {
            return [];
        }

        $arResult = [];
        foreach($arStatusList as $arStatusData) {
            if(in_array($arStatusData['code'], [self::STATUS_COMPETE, self::STATUS_CANCELED])) {
                $arResult[] = $arStatusData['id'];
            }
        }

        return $arResult;
    }
}