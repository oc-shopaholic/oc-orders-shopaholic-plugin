<?php namespace Lovata\OrdersShopaholic\Models;

use Backend\Models\ExportModel;
use DB;

/**
 * Class OrderPositionExport
 *
 * @package Lovata\OrdersShopaholic\Models
 * @author  Sergey Zakharevich, s.zakharevich@lovata.com, LOVATA Group
 *
 * @mixin \October\Rain\Database\Builder
 * @mixin \Eloquent
 */
class OrderPositionExport extends ExportModel
{
    const FIELD_ORDER_NUMBER = 'order_number';
    const FIELD_OFFER_NAME   = 'offer_name';

    const RELATION_ORDER = 'order';
    const RELATION_OFFER = 'offer';

    /** @var string */
    public $table = 'lovata_orders_shopaholic_order_positions';
    /** @var array */
    protected $arOrderPositionColumnList = [];
    /** @var array */
    protected $arRelationColumnList = [];
    /** @var array */
    protected $arPropertyColumnList = [];

    /**
     * Export data.
     * @param array|null $arColumns
     * @param string|null $sSessionKey
     * @return array
     */
    public function exportData($arColumns, $sSessionKey = null) : array
    {
        $arList = [];

        $this->init($arColumns);

        $obOrderPositionList = OrderPosition::with($this->arRelationColumnList)->get();

        if ($obOrderPositionList->isEmpty()) {
            return $arList;
        }

        foreach ($obOrderPositionList as $obOrderPosition) {
            $arRow = $this->prepareRow($obOrderPosition);

            if (empty($arRow)) {
                continue;
            }

            $arList[] = $arRow;
        }

        return $arList;
    }

    /**
     * Init.
     * @param array|null $arColumns
     * @return void
     */
    protected function init($arColumns)
    {
        if (empty($arColumns) || !is_array($arColumns)) {
            return;
        }

        $arPropertyList = (array) DB::table('lovata_orders_shopaholic_position_properties')
            ->where('active', true)
            ->lists('code');

        foreach ($arColumns as $sColumn) {
            if (self::FIELD_ORDER_NUMBER == $sColumn) {
                $this->arRelationColumnList[] = self::RELATION_ORDER;
            } elseif (self::FIELD_OFFER_NAME == $sColumn) {
                $this->arRelationColumnList[] = self::RELATION_OFFER;
            } elseif (in_array($sColumn, $arPropertyList)) {
                $this->arPropertyColumnList[] = $sColumn;
            } else {
                $this->arOrderPositionColumnList[] = $sColumn;
            }
        }

    }

    /**
     * Prepare row.
     * @param OrderPosition $obOrderPosition
     * @return array
     */
    protected function prepareRow(OrderPosition $obOrderPosition) : array
    {
        $arOrderData           = $this->prepareOrderPositionData($obOrderPosition);
        $arOrderRelationsData  = $this->prepareOrderRelationsData($obOrderPosition);
        $arOrderPropertiesData = $this->prepareOrderPositionPropertiesData($obOrderPosition);

        return array_merge($arOrderData, $arOrderRelationsData, $arOrderPropertiesData);
    }

    /**
     * Prepare order position data.
     * @param OrderPosition $obOrderPosition
     * @return array
     */
    protected function prepareOrderPositionData(OrderPosition $obOrderPosition) : array
    {
        $arResult = [];

        if (empty($this->arOrderPositionColumnList)) {
            return $arResult;
        }

        foreach ($this->arOrderPositionColumnList as $sField) {
            $arResult[$sField] = $obOrderPosition->$sField;
        }

        return $arResult;
    }

    /**
     * Prepare order position relations data.
     * @param OrderPosition $obOrderPosition
     * @return array
     */
    protected function prepareOrderRelationsData(OrderPosition $obOrderPosition) : array
    {
        $arResult = [];

        if (empty($this->arRelationColumnList)) {
            return $arResult;
        }

        if (!empty($obOrderPosition->order) && in_array(self::RELATION_ORDER, $this->arRelationColumnList)) {
            $arResult[self::FIELD_ORDER_NUMBER] = $obOrderPosition->order->order_number;
        }
        if (!empty($obOrderPosition->offer) && in_array(self::RELATION_OFFER, $this->arRelationColumnList)) {
            $arResult[self::FIELD_OFFER_NAME] = $obOrderPosition->offer->name;
        }

        return $arResult;
    }

    /**
     * Prepare order position data.
     * @param OrderPosition $obOrderPosition
     * @return array
     */
    protected function prepareOrderPositionPropertiesData(OrderPosition $obOrderPosition) : array
    {
        $arResult = [];

        if (empty($obOrderPosition->property) || empty($this->arPropertyColumnList)) {
            return $arResult;
        }

        foreach ($this->arPropertyColumnList as $sField) {
            $arResult[$sField] = array_get($obOrderPosition->property, $sField);
        }

        return $arResult;
    }
}
