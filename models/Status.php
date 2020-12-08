<?php namespace Lovata\OrdersShopaholic\Models;

use Model;
use October\Rain\Database\Traits\Sortable;
use October\Rain\Database\Traits\Validation;

use Kharanenka\Scope\CodeField;
use Lovata\Toolbox\Traits\Helpers\TraitCached;

/**
 * Class Status
 * @package Lovata\OrdersShopaholic\Models
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 *
 * @mixin \October\Rain\Database\Builder
 * @mixin \Eloquent
 *
 * @property                                           $id
 * @property string                                    $code
 * @property string                                    $name
 * @property string                                    $color
 * @property string                                    $preview_text
 * @property bool                                      $is_user_show
 * @property int                                       $user_status_id
 * @property int                                       $sort_order
 * @property \October\Rain\Argon\Argon                 $created_at
 * @property \October\Rain\Argon\Argon                 $updated_at
 *
 * @property \October\Rain\Database\Collection|Order[] $order
 * @method static Order|\October\Rain\Database\Relations\HasMany order()
 *
 * @property Status                                    $user_status
 * @method static Status|\October\Rain\Database\Relations\BelongsTo user_status()
 *
 * @method static $this isUserShow()
 */
class Status extends Model
{
    use CodeField;
    use Validation;
    use Sortable;
    use TraitCached;

    const STATUS_NEW = 'new';
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_COMPETE = 'complete';
    const STATUS_CANCELED = 'canceled';

    const STATUS_COLOR_NEW = '#f1c40f';
    const STATUS_COLOR_IN_PROGRESS = '#2980b9';
    const STATUS_COLOR_COMPLETE = '#27ae60';
    const STATUS_COLOR_CANCELED = '#c0392b';

    public $table = 'lovata_orders_shopaholic_statuses';
    
    public $implement = [
        '@RainLab.Translate.Behaviors.TranslatableModel',
    ];
    public $translatable = ['name', 'preview_text'];

    /** Validation */
    public $rules = [
        'name' => 'required',
        'code' => 'required|unique:lovata_orders_shopaholic_statuses',
    ];

    public $attributeNames = [
        'name' => 'lovata.toolbox::lang.field.name',
        'code' => 'lovata.toolbox::lang.field.code',
    ];

    public $fillable = [
        'code',
        'name',
        'sort_order',
        'is_user_show',
        'user_status_id',
        'preview_text',
    ];

    public $cached = [
        'id',
        'code',
        'name',
        'is_user_show',
        'user_status_id',
        'preview_text',
    ];

    public $dates = ['created_at', 'updated_at'];

    public $hasMany = ['order' => Order::class];

    public $belongsTo = [
        'user_status' => [Status::class, 'order' => 'sort_order asc'],
    ];

    /**
     * Get element with is_user_show flag = true
     * @param CartPosition $obQuery
     * @return CartPosition
     */
    public function scopeIsUserShow($obQuery)
    {
        return $obQuery->where('is_user_show', true);
    }

    /**
     * Find element by code and return element object
     * @param string $sCode
     * @return $this
     */
    public static function getFirstByCode($sCode)
    {
        if (empty($sCode)) {
            return null;
        }

        $obStatus = self::getByCode($sCode)->first();

        return $obStatus;
    }
}
