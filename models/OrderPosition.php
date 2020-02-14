<?php namespace Lovata\OrdersShopaholic\Models;

use Model;
use October\Rain\Database\Traits\Validation;

use Lovata\Toolbox\Traits\Helpers\TraitCached;
use Lovata\Toolbox\Traits\Helpers\PriceHelperTrait;
use Lovata\Toolbox\Traits\Models\SetPropertyAttributeTrait;
use Lovata\Toolbox\Classes\Helper\PriceHelper;

use Lovata\Shopaholic\Models\Offer;
use Lovata\Shopaholic\Models\Category;
use Lovata\Shopaholic\Models\Product;
use Lovata\Shopaholic\Classes\Helper\TaxHelper;
use Lovata\Shopaholic\Classes\Helper\CurrencyHelper;
use Lovata\Shopaholic\Classes\Helper\PriceTypeHelper;

use Lovata\OrdersShopaholic\Classes\PromoMechanism\ItemPriceContainer;
use Lovata\OrdersShopaholic\Classes\PromoMechanism\OrderPromoMechanismProcessor;

/**
 * Class OrderPosition
 * @package Lovata\PropertiesShopaholic\Models
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 *
 * @mixin \October\Rain\Database\Builder
 * @mixin \Eloquent
 *
 * @property int                                                       $id
 * @property int                                                       $order_id
 * @property int                                                       $item_id
 * @property int                                                       $offer_id
 * @property string                                                    $item_type
 * @property string                                                    $currency_symbol
 * @property string                                                    $currency_code
 *
 * @property string                                                    $price
 * @property float                                                     $price_value
 * @property string                                                    $tax_price
 * @property float                                                     $tax_price_value
 * @property string                                                    $price_without_tax
 * @property float                                                     $price_without_tax_value
 * @property string                                                    $price_with_tax
 * @property float                                                     $price_with_tax_value
 *
 * @property string                                                    $old_price
 * @property float                                                     $old_price_value
 * @property string                                                    $tax_old_price
 * @property float                                                     $tax_old_price_value
 * @property string                                                    $old_price_without_tax
 * @property float                                                     $old_price_without_tax_value
 * @property string                                                    $old_price_with_tax
 * @property float                                                     $old_price_with_tax_value
 *
 * @property string                                                    $total_price
 * @property float                                                     $total_price_value
 * @property string                                                    $old_total_price
 * @property float                                                     $old_total_price_value
 * @property string                                                    $discount_price
 * @property float                                                     $discount_price_value
 * @property ItemPriceContainer                                        $price_data
 * @property float                                                     $tax_percent
 * @property int                                                       $quantity
 * @property double                                                    $weight
 * @property double                                                    $height
 * @property double                                                    $length
 * @property double                                                    $width
 * @property string                                                    $code
 * @property array                                                     $property
 *
 * @property mixed                                                     $item
 * @method \October\Rain\Database\Relations\MorphTo item()
 *
 * @property Order                                                     $order
 * @method \October\Rain\Database\Relations\BelongsTo|Order order()
 *
 * @property Offer                                                     $offer
 * @method \October\Rain\Database\Relations\BelongsTo|Offer offer()
 *
 * @method static $this getByItemID(int $iItemID)
 * @method static $this getByItemType(string $sItemType)
 *
 * Subscriptions for Shopaholic
 * @property bool                                                      $is_subscription
 * @property int                                                       $subscription_period_id
 * @property \Lovata\SubscriptionsShopaholic\Models\SubscriptionPeriod $subscription_period
 * @method static \October\Rain\Database\Relations\BelongsTo|\Lovata\SubscriptionsShopaholic\Models\SubscriptionPeriod subscription_period()
 * @property int                                                       $subscription_access_id
 * @property \Lovata\SubscriptionsShopaholic\Models\SubscriptionAccess $subscription_access
 * @method static \October\Rain\Database\Relations\BelongsTo|\Lovata\SubscriptionsShopaholic\Models\SubscriptionAccess subscription_access()
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
        'tax_percent',
        'property',
        'weight',
        'height',
        'length',
        'width',
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
        'tax_percent',
        'property',
        'weight',
        'height',
        'length',
        'width',
    ];

    public $arPriceField = [
        'price',
        'old_price',
        'tax_price',
        'tax_old_price',
        'price_with_tax',
        'old_price_with_tax',
        'price_without_tax',
        'old_price_without_tax',
        'total_price',
        'old_total_price',
        'discount_price'
    ];

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
        $obPriceData = $this->price_data;

        return $obPriceData->price_value;
    }

    /**
     * Get total price value
     * @return float
     */
    public function getOldTotalPriceValueAttribute()
    {
        $obPriceData = $this->price_data;

        return $obPriceData->old_price_value;
    }

    /**
     * Get total price value
     * @return float
     */
    public function getDiscountPriceValueAttribute()
    {
        $obPriceData = $this->price_data;

        return $obPriceData->discount_price_value;
    }

    /**
     * Get total price value
     * @return \Lovata\OrdersShopaholic\Classes\PromoMechanism\ItemPriceContainer
     */
    public function getPriceDataAttribute()
    {
        $obOrder = $this->order;
        if (empty($obOrder)) {
            return ItemPriceContainer::makeEmpty();
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
            $obCategory = Category::first();
            $iCategoryID = !empty($obCategory) ? $obCategory->id : null;
        } else {
            $iCategoryID = $this->_category;
        }

        if (empty($iCategoryID)) {
            return [];
        }

        $arResult = (array) Product::active()->getByCategory($iCategoryID)->orderBy('name')->lists('name', 'id');

        return $arResult;
    }

    /**
     * Get offer list
     * @return array
     */
    public function getOfferIdOptions()
    {
        if (empty($this->_product)) {
            $arProductList = $this->getProductOptions();
            $arProductIDList = array_keys($arProductList);
            $iProductID = array_shift($arProductIDList);
        } else {
            $iProductID = $this->_product;
        }

        if (empty($iProductID)) {
            return [];
        }

        $arResult = (array) Offer::active()->getByProduct($iProductID)->orderBy('name')->lists('name', 'id');

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
     * Get tax_price_value attribute value
     * @return float
     */
    protected function getTaxPriceValueAttribute()
    {
        $fPrice = PriceHelper::round($this->price_with_tax_value - $this->price_without_tax_value);

        return $fPrice;
    }

    /**
     * Get tax_old_price_value attribute value
     * @return float
     */
    protected function getTaxOldPriceValueAttribute()
    {
        $fPrice = PriceHelper::round($this->old_price_with_tax_value - $this->old_price_without_tax_value);

        return $fPrice;
    }

    /**
     * Get price_with_tax_value attribute value
     * @return float
     */
    protected function getPriceWithTaxValueAttribute()
    {
        $fPrice = TaxHelper::instance()->getPriceWithTax($this->price_value, $this->tax_percent);

        return $fPrice;
    }

    /**
     * Get old_price_with_tax_value attribute value
     * @return float
     */
    protected function getOldPriceWithTaxValueAttribute()
    {
        $fPrice = TaxHelper::instance()->getPriceWithTax($this->old_price_value, $this->tax_percent);

        return $fPrice;
    }

    /**
     * Get price_without_tax_value attribute value
     * @return float
     */
    protected function getPriceWithoutTaxValueAttribute()
    {
        $fPrice = TaxHelper::instance()->getPriceWithoutTax($this->price_value, $this->tax_percent);

        return $fPrice;
    }

    /**
     * Get old_price_without_tax_value attribute value
     * @return float
     */
    protected function getOldPriceWithoutTaxValueAttribute()
    {
        $fPrice = TaxHelper::instance()->getPriceWithoutTax($this->old_price_value, $this->tax_percent);

        return $fPrice;
    }

    /**
     * Get currency_symbol attribute value
     * @return null|string
     */
    protected function getCurrencySymbolAttribute()
    {
        //Get order  object
        $obOrder = $this->order;
        if (empty($obOrder)) {
            return null;
        }

        return $obOrder->currency_symbol;
    }

    /**
     * Get currency_code attribute value
     * @return null|string
     */
    protected function getCurrencyCodeAttribute()
    {
        //Get order  object
        $obOrder = $this->order;
        if (empty($obOrder)) {
            return null;
        }

        return $obOrder->currency_code;
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


        $obOrder = $this->order;
        if (empty($obOrder)) {
            $sCurrencyCode = CurrencyHelper::instance()->getActiveCurrencyCode();
        } else {
            $sCurrencyCode = $obOrder->currency_code;
        }

        $iActivePriceType = PriceTypeHelper::instance()->getActivePriceTypeID();

        $this->price = $obItem->setActivePriceType($iActivePriceType)->setActiveCurrency($sCurrencyCode)->price_value;
        $this->old_price = $obItem->setActivePriceType($iActivePriceType)->setActiveCurrency($sCurrencyCode)->old_price_value;
        $this->tax_percent = $obItem->tax_percent;
        $this->code = $obItem->code;
        $this->weight = $obItem->weight;
        $this->height = $obItem->height;
        $this->length = $obItem->length;
        $this->width = $obItem->width;
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
