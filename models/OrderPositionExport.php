<?php namespace Lovata\OrdersShopaholic\Models;

use DB;
use Input;

use Lovata\Toolbox\Classes\Helper\AbstractExportModelInCSV;

/**
 * Class OrderPositionExport
 *
 * @package Lovata\OrdersShopaholic\Models
 * @author  Sergey Zakharevich, s.zakharevich@lovata.com, LOVATA Group
 *
 * @mixin \October\Rain\Database\Builder
 * @mixin \Eloquent
 */
class OrderPositionExport extends AbstractExportModelInCSV
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

        $arPropertyList = $this->getPropertyList();

        foreach ($arColumns as $sColumn) {
            if (in_array($sColumn, self::RELATION_LIST)) {
                $this->arRelationColumnList[] = $sColumn;
            } elseif (in_array($sColumn, $arPropertyList)) {
                $this->arPropertyColumnList[] = $sColumn;
            } else {
                if ($sColumn == self::FIELD_CURRENCY_SYMBOL) {
                    $this->arRelationColumnList[] = self::RELATION_ORDER;
                }

                $this->arColumnList[] = $sColumn;
            }
        }
    }

    /**
     * Get property list.
     * @return array
     */
    protected function getPropertyList() : array
    {
        $arPropertyList = (array) DB::table('lovata_orders_shopaholic_position_properties')
            ->where('active', true)
            ->lists('code');

        return $arPropertyList;
    }

    /**
     * Get list.
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|OrderPosition[]
     */
    protected function getItemList()
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
     * Prepare order position relations data.
     * @param OrderPosition $obOrderPosition
     * @return array
     */
    protected function prepareModelRelationsData($obOrderPosition) : array
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
     * Prepare order position properties data.
     * @param OrderPosition $obOrderPosition
     * @return array
     */
    protected function prepareModelPropertiesData($obOrderPosition) : array
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
