<?php namespace Lovata\OrdersShopaholic\Models;

use DB;
use Input;

use Lovata\Toolbox\Classes\Helper\AbstractExportModelInCSV;

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
class OrderExport extends AbstractExportModelInCSV
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
                if ($sColumn == self::FIELD_TOTAL_PRICE || $sColumn == self::FIELD_POSITION_TOTAL_PRICE) {
                    $this->arRelationColumnList[] = self::RELATION_ORDER_POSITION;
                }
                $this->arColumnList[] = $sColumn;
            }
        }

        $this->initPropertyColumnList();
    }

    /**
     * Get property list.
     * @return array
     */
    protected function getPropertyList() : array
    {
        $arPropertyList = (array) DB::table('lovata_orders_shopaholic_addition_properties')
            ->where('active', true)
            ->lists('code');

        foreach ($arPropertyList as $iKey => $sProperty) {
            $arPropertyList[$iKey] = 'property.'.$sProperty;
        }

        return $arPropertyList;
    }

    /**
     * Get list.
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|Order[]
     */
    protected function getItemList()
    {
        $iStatusId  = Input::get('status_id');
        $sStartDate = Input::get('start_date');
        $sEndDate   = Input::get('end_date');

        $obQuery = Order::with($this->arRelationColumnList);

        if (!empty($iStatusId)) {
            $obQuery->getByStatus($iStatusId);
        }
        if (!empty($sStartDate)) {
            $obQuery->whereDate('created_at', '>=', $sStartDate);
        }
        if (!empty($sEndDate)) {
            $obQuery->whereDate('created_at', '<=', $sEndDate);
        }

        return $obQuery->get();
    }

    /**
     * Prepare order relations data.
     * @param Order $obOrder
     * @return array
     */
    protected function prepareModelRelationsData($obOrder) : array
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
}
