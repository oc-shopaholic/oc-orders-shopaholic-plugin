<?php namespace Lovata\OrdersShopaholic\Models;

use Lovata\OrdersShopaholic\Classes\PromoMechanism\OrderPromoMechanismProcessor;
use Lovata\OrdersShopaholic\Classes\PromoMechanism\PriceContainer;
use Model;
use October\Rain\Database\Traits\Validation;

use Lovata\Toolbox\Traits\Helpers\TraitCached;
use Lovata\Toolbox\Traits\Helpers\PriceHelperTrait;
use Lovata\Toolbox\Traits\Models\SetPropertyAttributeTrait;

use Lovata\Shopaholic\Models\Offer;
use Lovata\Shopaholic\Models\Category;
use Lovata\Shopaholic\Models\Product;

/**
 * Class OrderPosition
 * @package Lovata\PropertiesShopaholic\Models
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 *
 * @mixin \October\Rain\Database\Builder
 * @mixin \Eloquent
 *
 * @property int    $id
 * @property int    $order_id
 * @property int    $item_id
 * @property int    $offer_id
 * @property string $item_type
 * @property string $price
 * @property float  $price_value
 * @property string $old_price
 * @property float  $old_price_value
 * @property string $total_price
 * @property float  $total_price_value
 * @property string $old_total_price
 * @property float  $old_total_price_value
 * @property string $discount_price
 * @property float  $discount_price_value
 * @property int    $quantity
 * @property string $code
 * @property array  $property
 *
 * @property mixed  $item
 * @method \October\Rain\Database\Relations\MorphTo item()
 *
 * @property Order $order
 * @method \October\Rain\Database\Relations\BelongsTo|Order order()
 *
 * @property Offer $offer
 * @method \October\Rain\Database\Relations\BelongsTo|Offer offer()
 *
 * @method static $this getByItemID(int $iItemID)
 * @method static $this getByItemType(string $sItemType)
 */
class OrderPosition extends Model
{
    use PriceHelperTrait;
    use TraitCached;
    use Validation;
    use SetPropertyAttributeTrait;

    public $table = 'lovata_orders_shopaholic_order_positions';

    public $rules = [
        'order_id'  => 'required',
        'item_id'   => 'required',
        'item_type' => 'required',
    ];

    public $customMessages = [
        'order_id.required'  => 'lovata.ordersshopaholic::lang.message.order_id_required',
        'item_id.required'   => 'lovata.ordersshopaholic::lang.message.item_required',
        'item_type.required' => 'lovata.ordersshopaholic::lang.message.item_required',
    ];

    public $morphTo = [
        'item' => [],
    ];

    public $belongsTo = [
        'offer' => [
            Offer::class,
            'key'   => 'item_id',
            'scope' => 'withTrashed',
        ],
        'order' => [
            Order::class,
            'key' => 'order_id',
        ],
    ];

    public $jsonable = ['property'];

    public $fillable = [
        'order_id',
        'item_id',
        'item_type',
        'offer_id',
        'price',
        'old_price',
        'quantity',
        'code',
        'property',
    ];

    public $cached = [
        'id',
        'order_id',
        'item_id',
        'item_type',
        'price_value',
        'old_price_value',
        'quantity',
        'code',
        'property',
    ];

    public $arPriceField = ['price', 'old_price', 'total_price', 'old_total_price', 'discount_price'];

    /**
     * Before save model event
     */
    public function beforeSave()
    {
        $this->saveNewPositionData();
    }

    /**
     * After save model event
     */
    public function afterSave()
    {
        $this->updatePromoMechanism();
    }

    /**
     * After delete model event
     */
    public function afterDelete()
    {
        $this->updatePromoMechanism();
    }

    /**
     * Get element by item ID
     * @param CartPosition $obQuery
     * @param string       $sData
     * @return CartPosition
     */
    public function scopeGetByItemID($obQuery, $sData)
    {
        if (!empty($sData)) {
            $obQuery->where('item_id', $sData);
        }

        return $obQuery;
    }

    /**
     * Get element by item type
     * @param CartPosition $obQuery
     * @param string       $sData
     * @return CartPosition
     */
    public function scopeGetByItemType($obQuery, $sData)
    {
        if (!empty($sData)) {
            $obQuery->where('item_type', $sData);
        }

        return $obQuery;
    }

    /**
     * Get total price value
     * @return float
     */
    public function getTotalPriceValueAttribute()
    {
        $obPriceData = $this->getTotalPriceData();

        return $obPriceData->price_value;
    }

    /**
     * Get total price value
     * @return float
     */
    public function getOldTotalPriceValueAttribute()
    {
        $obPriceData = $this->getTotalPriceData();

        return $obPriceData->old_price_value;
    }

    /**
     * Get total price value
     * @return float
     */
    public function getDiscountPriceValueAttribute()
    {
        $obPriceData = $this->getTotalPriceData();

        return $obPriceData->discount_price_value;
    }

    /**
     * Get total price value
     * @return \Lovata\OrdersShopaholic\Classes\PromoMechanism\PriceContainer
     */
    public function getTotalPriceData()
    {
        $obOrder = $this->order;
        if (empty($obOrder)) {
            return new PriceContainer(0, 0);
        }

        $obMechanismProcessor = OrderPromoMechanismProcessor::get($obOrder);
        $obPriceData = $obMechanismProcessor->getPositionPrice($this->id);

        return $obPriceData;
    }

    /**
     * Get category list
     * @return array
     */
    public function getCategoryOptions()
    {
        $arResult = (array) Category::orderBy('nest_left')->lists('name', 'id');

        return $arResult;
    }

    /**
     * Get product list
     * @return array
     */
    public function getProductOptions()
    {
        if (empty($this->_category)) {
            return [];
        }

        $arResult = (array) Product::active()->getByCategory($this->_category)->orderBy('name')->lists('name', 'id');

        return $arResult;
    }

    /**
     * Get offer list
     * @return array
     */
    public function getOfferIdOptions()
    {
        if (empty($this->_product)) {
            return [];
        }

        $arResult = (array) Offer::active()->getByProduct($this->_product)->orderBy('name')->lists('name', 'id');

        return $arResult;
    }

    /**
     * Get order property value
     * @param string $sField
     * @return mixed
     */
    public function getProperty($sField)
    {
        $arPropertyList = $this->property;
        if (empty($arPropertyList) || empty($sField)) {
            return null;
        }

        return array_get($arPropertyList, $sField);
    }

    /**
     * Set offer ID attribute, save item_id + item_type fields
     * @param int $iOfferID
     */
    protected function setOfferIdAttribute($iOfferID)
    {
        $this->item_id = $iOfferID;
        $this->item_type = Offer::class;
    }

    /**
     * Get offer ID value, if order position has type "offer"
     */
    protected function getOfferIdAttribute()
    {
        if ($this->item_type != Offer::class) {
            return null;
        }

        return $this->item_id;
    }

    /**
     * If item ID was changed, then save new price, old_price, code values from new item object
     */
    protected function saveNewPositionData()
    {
        if ($this->item_id == $this->getOriginal('item_id')) {
            return;
        }

        //Get item object
        /** @var Offer $obItem */
        $obItem = $this->item;
        if (empty($obItem)) {
            return;
        }

        $this->price = $obItem->price_value;
        $this->old_price = $obItem->old_price_value;
        $this->code = $obItem->code;
    }

    /**
     * Update promo mechanism, after create/update/remove
     */
    protected function updatePromoMechanism()
    {
        $obOrder = $this->order;
        if (empty($obOrder)) {
            return;
        }

        OrderPromoMechanismProcessor::update($obOrder);
    }
}
