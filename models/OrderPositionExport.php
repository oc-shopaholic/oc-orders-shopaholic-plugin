<?php namespace Lovata\OrdersShopaholic\Models;

use Backend\Models\ExportModel;
use DB;
use Illuminate\Database\Query\Builder;
use Input;

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
    const FIELD_CURRENCY_SYMBOL = 'currency_symbol';

    const RELATION_ORDER    = 'order';
    const RELATION_OFFER    = 'offer';
    const RELATION_CURRENCY = 'currency';

    const RELATION_LIST = [
        self::RELATION_ORDER,
        self::RELATION_OFFER,
        self::RELATION_CURRENCY,
    ];

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
        if (empty($arColumns)) {
            return $arList;
        }

        $this->init($arColumns);

        $obOrderPositionList = $this->getList();

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
            if (in_array($sColumn, self::RELATION_LIST)) {
                $this->arRelationColumnList[] = $sColumn;
            } elseif (in_array($sColumn, $arPropertyList)) {
                $this->arPropertyColumnList[] = $sColumn;
            } else {
                if ($sColumn == self::FIELD_CURRENCY_SYMBOL) {
                    $this->arRelationColumnList[] = self::RELATION_ORDER;
                }

                $this->arOrderPositionColumnList[] = $sColumn;
            }
        }

        $this->arRelationColumnList = array_unique($this->arRelationColumnList);
    }

    /**
     * Get list.
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|OrderPosition[]
     */
    protected function getList()
    {
        $iStatusId  = Input::get('status_id');
        $sStartDate = Input::get('start_date');
        $sEndDate   = Input::get('end_date');

        $obQuery = OrderPosition::with($this->arRelationColumnList);
        if (!empty($iStatusId)) {
            $obQuery->has('order', '>', 0, 'and', function ($obQuery) use ($iStatusId) {
                $obQuery->where('status_id', $iStatusId);
            });
        }
        if (!empty($sStartDate)) {
            $obQuery->has('order', '>', 0, 'and', function ($obQuery) use ($sStartDate) {
                $obQuery->whereDate('created_at', '>=', $sStartDate);
            });
        }
        if (!empty($sEndDate)) {
            $obQuery->has('order', '>', 0, 'and', function ($obQuery) use ($sEndDate) {
                $obQuery->whereDate('created_at', '<=', $sEndDate);
            });
        }

        return $obQuery->get();
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
            $arResult[self::RELATION_ORDER] = $obOrderPosition->order->order_number;
        }
        if (!empty($obOrderPosition->offer) && in_array(self::RELATION_OFFER, $this->arRelationColumnList)) {
            $arResult[self::RELATION_OFFER] = $obOrderPosition->offer->name;
        }
        if (!empty($obOrderPosition->currency) && in_array(self::RELATION_CURRENCY, $this->arRelationColumnList)) {
            $arResult[self::RELATION_CURRENCY] = $obOrderPosition->currency_symbol;
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
