<?php namespace Lovata\OrdersShopaholic\Classes\Item;

use Lovata\Toolbox\Classes\Item\ElementItem;

use Lovata\Shopaholic\Models\Offer;
use Lovata\Shopaholic\Classes\Item\OfferItem;
use Lovata\OrdersShopaholic\Classes\Processor\OfferOrderPositionProcessor;

/**
 * Class AbstractPositionItem
 * @package Lovata\OrdersShopaholic\Classes\Item
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 *
 * @property             $id
 * @property int         $item_id
 * @property string      $item_type
 * @property string      $type
 * @property int         $quantity
 * @property string      $currency
 * @property string      $currency_code
 * @property array       $property
 * @property ElementItem $item
 */
abstract class AbstractPositionItem extends ElementItem
{
    /** @var array */
    protected $arItemTypeList = [
        Offer::class => [
            'name' => 'offer',
            'item' => OfferItem::class,
            'order_processor' => OfferOrderPositionProcessor::class,
        ],
    ];

    /**
     * Add item type
     * @param string $sName
     * @param string $sModelClass
     * @param string $sItemClass
     * @param string $sOrderProcessorClass
     */
    public function addItemType($sName, $sModelClass, $sItemClass,  $sOrderProcessorClass)
    {
        if (empty($sName) || empty($sModelClass) || empty($sItemClass) || empty($sOrderProcessorClass)) {
            return;
        }

        if (!class_exists($sItemClass) || !class_exists($sOrderProcessorClass) || isset($this->arItemTypeList[$sModelClass])) {
            return;
        }

        $this->arItemTypeList[$sModelClass] = [
            'name'            => $sName,
            'item'            => $sItemClass,
            'order_processor' => $sOrderProcessorClass,
        ];
    }

    /**
     * Get type field value
     * @return null|string
     */
    protected function getTypeAttribute()
    {
        $sModelClass = $this->item_type;
        if (!isset($this->arItemTypeList[$sModelClass])) {
            return null;
        }

        return $this->arItemTypeList[$sModelClass]['name'];
    }

    /**
     * Get item field value
     * @return ElementItem
     */
    protected function getItemAttribute()
    {
        $sModelClass = $this->item_type;
        if (!isset($this->arItemTypeList[$sModelClass])) {
            return OfferItem::make(null);
        }

        $sItemClass = $this->arItemTypeList[$sModelClass]['item'];
        $obItem = $sItemClass::make($this->item_id);

        return $obItem;
    }
}
