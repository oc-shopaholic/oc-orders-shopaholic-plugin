<?php namespace Lovata\OrdersShopaholic\Models;

use Backend\Models\ExportModel;
use DB;
use Input;

/**
 * Class OrderExport
 *
 * @package Lovata\OrdersShopaholic\Models
 * @author  Sergey Zakharevich, s.zakharevich@lovata.com, LOVATA Group
 *
 * @mixin \October\Rain\Database\Builder
 * @mixin \Eloquent
 *
 * @property Status $status
 * @method static Status|\October\Rain\Database\Relations\BelongsTo status()
 */
class OrderExport extends ExportModel
{
    const FIELD_TOTAL_PRICE          = 'total_price';
    const FIELD_POSITION_TOTAL_PRICE = 'position_total_price';

    const RELATION_STATUS         = 'status';
    const RELATION_SHIPPING_TYPE  = 'shipping_type';
    const RELATION_PAYMENT_METHOD = 'payment_method';
    const RELATION_CURRENCY       = 'currency';
    const RELATION_ORDER_POSITION = 'order_position';

    const RELATION_LIST = [
        self::RELATION_STATUS,
        self::RELATION_SHIPPING_TYPE,
        self::RELATION_PAYMENT_METHOD,
        self::RELATION_CURRENCY,
        self::RELATION_ORDER_POSITION,
    ];

    /** @var string */
    public $table = 'lovata_orders_shopaholic_orders';
    /** @var array */
    public $belongsTo = ['status' => [Status::class, 'order' => 'sort_order asc']];
    /** @var array */
    protected $arOrderColumnList = [];
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

        $iStatusId = Input::get('status_id');

        $obQuery = Order::with($this->arRelationColumnList);

        if (!empty($iStatusId)) {
            $obQuery->getByStatus($iStatusId);
        }

        $obOrderList = $obQuery->get();

        if ($obOrderList->isEmpty()) {
            return $arList;
        }

        foreach ($obOrderList as $obOrder) {
            $arRow = $this->prepareRow($obOrder);

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

        $arPropertyList = (array) DB::table('lovata_orders_shopaholic_addition_properties')
            ->where('active', true)
            ->lists('code');

        foreach ($arColumns as $sColumn) {
            if (in_array($sColumn, self::RELATION_LIST)) {
                $this->arRelationColumnList[] = $sColumn;
            } elseif (in_array($sColumn, $arPropertyList)) {
                $this->arPropertyColumnList[] = $sColumn;
            } else {
                if ($sColumn == self::FIELD_TOTAL_PRICE || $sColumn == self::FIELD_POSITION_TOTAL_PRICE) {
                    $this->arRelationColumnList[] = self::RELATION_ORDER_POSITION;
                }
                $this->arOrderColumnList[] = $sColumn;
            }
        }
    }

    /**
     * Prepare row.
     * @param Order $obOrder
     * @return array
     */
    protected function prepareRow(Order $obOrder) : array
    {
        $arOrderData           = $this->prepareOrderData($obOrder);
        $arOrderRelationsData  = $this->prepareOrderRelationsData($obOrder);
        $arOrderPropertiesData = $this->prepareOrderPropertiesData($obOrder);

        return array_merge($arOrderData, $arOrderRelationsData, $arOrderPropertiesData);
    }

    /**
     * Prepare order data.
     * @param Order $obOrder
     * @return array
     */
    protected function prepareOrderData(Order $obOrder) : array
    {
        $arResult = [];

        if (empty($this->arOrderColumnList)) {
            return $arResult;
        }

        foreach ($this->arOrderColumnList as $sField) {
            $arResult[$sField] = $obOrder->$sField;
        }

        return $arResult;
    }

    /**
     * Prepare order relations data.
     * @param Order $obOrder
     * @return array
     */
    protected function prepareOrderRelationsData(Order $obOrder) : array
    {
        $arResult = [];

        if (empty($this->arRelationColumnList)) {
            return $arResult;
        }

        if (!empty($obOrder->status) && in_array(self::RELATION_STATUS, $this->arRelationColumnList)) {
            $arResult[self::RELATION_STATUS] = $obOrder->status->name;
        }
        if (!empty($obOrder->shipping_type)
            && in_array(self::RELATION_SHIPPING_TYPE, $this->arRelationColumnList)) {
            $arResult[self::RELATION_SHIPPING_TYPE] = $obOrder->shipping_type->name;
        }
        if (!empty($obOrder->payment_method)
            && in_array(self::RELATION_PAYMENT_METHOD, $this->arRelationColumnList)) {
            $arResult[self::RELATION_PAYMENT_METHOD] = $obOrder->payment_method->name;
        }
        if (!empty($obOrder->currency) && in_array(self::RELATION_CURRENCY, $this->arRelationColumnList)) {
            $arResult[self::RELATION_CURRENCY] = $obOrder->currency->symbol;
        }

        return $arResult;
    }

    /**
     * Prepare order property data.
     * @param Order $obOrder
     * @return array
     */
    protected function prepareOrderPropertiesData(Order $obOrder) : array
    {
        $arResult = [];

        if (empty($obOrder->property) || empty($this->arPropertyColumnList)) {
            return $arResult;
        }

        foreach ($this->arPropertyColumnList as $sField) {
            $arResult[$sField] = array_get($obOrder->property, $sField);
        }

        return $arResult;
    }
}
