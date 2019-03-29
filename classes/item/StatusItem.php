<?php namespace Lovata\OrdersShopaholic\Classes\Item;

use Lang;
use Lovata\Toolbox\Classes\Item\ElementItem;

use Lovata\OrdersShopaholic\Models\Status;

/**
 * Class StatusItem
 * @package Lovata\Shopaholic\Classes\Item
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 *
 * @property        $id
 * @property string $name
 * @property string $name_for_user
 * @property string $code
 * @property string $preview_text
 * @property bool $is_user_show
 * @property int $user_status_id
 *
 * @property StatusItem $user_status
 */
class StatusItem extends ElementItem
{
    const MODEL_CLASS = Status::class;

    /** @var Status */
    protected $obElement = null;

    public $arRelationList = [
        'user_status' => [
            'class' => StatusItem::class,
            'field' => 'user_status_id',
        ],
    ];

    /**
     * Get name_for_user attribute value
     * @return string
     */
    protected function getNameForUserAttribute()
    {
        if ($this->user_status->isEmpty()) {
            return $this->name;
        }

        return $this->user_status->name;
    }
}
