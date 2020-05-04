<?php namespace Lovata\OrdersShopaholic\Models;

use Backend\Models\ExportModel;

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
    const ORDER_FIELDS          = [
        'id',
        'order_number',
        'created_at',
        'total_price',
        'updated_at',
        'shipping_price',
        'position_total_price',
    ];
    const ORDER_RELATION_FIELDS = [
        'status',
        'shipping_type',
        'payment_method',
        'currency',
    ];

    const PREFIX_RELATION = 'relation_';
    const PREFIX_PROPERTY = 'property_';

    /** @var string */
    public $table = 'lovata_orders_shopaholic_orders';
    /** @var array */
    public $belongsTo = ['status' => [Status::class, 'order' => 'sort_order asc']];
    /** @var array */
    protected $arOrderFields = [];
    /** @var array */
    protected $arOrderRelationsFields = [];
    /** @var array */
    protected $arOrderPropertiesFields = [];
    /** @var null|Order|\October\Rain\Database\Collection */
    protected $obOrderList;

    /**
     * Export data.
     * @param array|null $arColumns
     * @param string|null $sSessionKey
     * @return array
     */
    public function exportData($arColumns, $sSessionKey = null) : array
    {
        $this->init($arColumns);

        if ($this->obOrderList === null || $this->obOrderList->isEmpty()) {
            return [];
        }

        $arList = [];

        foreach ($this->obOrderList as $obOrder) {
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
        $this->initColumnList($arColumns);
        $this->initOrderList();
    }

    /**
     * Init column list.
     * @param array $arColumns
     * @return void
     */
    protected function initColumnList($arColumns)
    {
        if (empty($arColumns) || !is_array($arColumns)) {
            return;
        }

        // Init order fields.
        $this->arOrderFields = (array) array_intersect(self::ORDER_FIELDS, $arColumns);

        // Init relations.
        foreach ($arColumns as $sColumn) {
            $sColumn = preg_replace("/^".self::PREFIX_RELATION."/", '', $sColumn);
            if (in_array($sColumn, self::ORDER_RELATION_FIELDS)) {
                $this->arOrderRelationsFields[] = $sColumn;
            }
        }
    }

    /**
     * Init order list.
     * @return void
     */
    protected function initOrderList()
    {
        $arWith = $this->arOrderRelationsFields;

        // Init order_position query.
        if (in_array('total_price', $this->arOrderFields)
            || in_array('position_total_price', $this->arOrderFields))
        {
            $arWith[] = 'order_position';
        }

        $obQuery = Order::with($arWith);

        $this->obOrderList = $obQuery->get();
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
        if (empty($this->arOrderFields)) {
            return [];
        }

        $arResult = [];

        foreach ($this->arOrderFields as $sField) {
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
        if (empty($this->arOrderRelationsFields)) {
            return [];
        }

        $arResult = [];

        // Init status relation.
        if (in_array('status', $this->arOrderRelationsFields)) {
            $sStatusName = !empty($obOrder->status) ? $obOrder->status->name : '';
            $arResult[self::PREFIX_RELATION.'status'] = $sStatusName;
        }
        // Init shipping_type relation.
        if (in_array('shipping_type', $this->arOrderRelationsFields)) {
            $sShippingTypeNmae = !empty($obOrder->shipping_type) ? $obOrder->shipping_type->name : '';
            $arResult[self::PREFIX_RELATION.'shipping_type'] = $sShippingTypeNmae;
        }
        // Init payment_method relation.
        if (in_array('payment_method', $this->arOrderRelationsFields)) {
            $sShippingTypeNmae = !empty($obOrder->payment_method) ? $obOrder->payment_method->name : '';
            $arResult[self::PREFIX_RELATION.'payment_method'] = $sShippingTypeNmae;
        }
        // Init currency relation.
        if (in_array('currency', $this->arOrderRelationsFields)) {
            $sShippingTypeNmae = !empty($obOrder->currency) ? $obOrder->currency->symbol : '';
            $arResult[self::PREFIX_RELATION.'currency'] = $sShippingTypeNmae;
        }

        return $arResult;
    }

    /**
     * Prepare order data.
     * @param Order $obOrder
     * @return array
     */
    protected function prepareOrderPropertiesData(Order $obOrder) : array
    {
        return [];
    }
}
