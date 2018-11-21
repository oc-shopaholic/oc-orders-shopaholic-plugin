<?php namespace Lovata\OrdersShopaholic\Models;

use Lang;
use Model;
use \Backend\Models\User as BackendUser;
use Backend\Facades\BackendAuth;
use System\Models\MailTemplate;

use Kharanenka\Scope\UserBelongsTo;
use Lovata\Toolbox\Classes\Helper\UserHelper;

/**
 * Class Task
 * @package Lovata\OrdersShopaholic\Models
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 *
 * @mixin \October\Rain\Database\Builder
 * @mixin \Eloquent
 *
 * @property int                         $id
 * @property bool                        $sent
 * @property \October\Rain\Argon\Argon   $date
 * @property string                      $status
 * @property string                      $title
 * @property string                      $description
 * @property int                         $order_id
 * @property int                         $user_id
 * @property int                         $manager_id
 * @property int                         $author_id
 * @property string                      $mail_template
 * @property \October\Rain\Argon\Argon   $created_at
 * @property \October\Rain\Argon\Argon   $updated_at
 *
 * @property Order                       $order
 * @method Order||\October\Rain\Database\Relations\BelongsTo order()
 * @property \Lovata\Buddies\Models\User $user
 * @method static \Lovata\Buddies\Models\User|\October\Rain\Database\Relations\BelongsTo user()
 * @property BackendUser $manager
 * @method static BackendUser|\October\Rain\Database\Relations\BelongsTo manager()
 * @property BackendUser $author
 * @method static BackendUser|\October\Rain\Database\Relations\BelongsTo author()
 *
 * @method static $this getByOrder(int $iOrderID)
 * @method static $this getByManager(int $iManagerID)
 * @method static $this getByStatus(int $iStatus)
 * @method static $this getActiveTask()
 * @method static $this getCompletedTask()
 */
class Task extends Model
{
    use UserBelongsTo;

    const STATUS_NEW = 'new';
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_CANCEL = 'canceled';
    const STATUS_COMPLETE = 'complete';

    const EVENT_EXTEND_EMAIL_NOTIFICATION_DATA = 'shopaholic.order.task.extend_email_notification';

    public $table = 'lovata_orders_shopaholic_tasks';

    public $appends = [];

    public $belongsTo = [
        'order'   => [Order::class],
        'manager' => [BackendUser::class],
        'author'  => [BackendUser::class],
    ];
    public $hasMany = [];
    public $belongsToMany = [];
    public $dates = ['date', 'created_at', 'updated_at'];

    public $fillable = [
        'sent',
        'date',
        'status',
        'title',
        'description',
        'order_id',
        'user_id',
        'manager_id',
        'author_id',
        'mail_template',
    ];

    /**
     * Order constructor.
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        $sUserModelClass = UserHelper::instance()->getUserModel();
        if (!empty($sUserModelClass)) {
            $this->belongsTo['user'] = [$sUserModelClass];
        }

        parent::__construct($attributes);
    }

    /**
     * Before create event handler
     */
    public function beforeCreate()
    {
        $this->status = self::STATUS_NEW;
        $obAuthUser = BackendAuth::getUser();
        if (!empty($obAuthUser)) {
            $this->author_id = $obAuthUser->id;
            $this->manager_id = $obAuthUser->id;
        }

        $obOrder = $this->order;
        if (!empty($obOrder)) {
            $this->manager_id = !empty($obOrder->manager_id) ? $obOrder->manager_id : $this->manager_id;
            $this->user_id = $obOrder->user_id;
        }
    }

    /**
     * Get status list
     * @return array
     */
    public function getStatusOptions()
    {
        return self::getStatusList();
    }

    /**
     * Get mail template list
     * @return array
     */
    public function getMailTemplateOptions()
    {
        $arTemplateList = MailTemplate::listAllTemplates();
        if (empty($arTemplateList)) {
            return [];
        }

        $arResult = [];
        foreach ($arTemplateList as $sTemplateCode) {
            $obTemplate = MailTemplate::findOrMakeTemplate($sTemplateCode);
            if (empty($obTemplate)) {
                continue;
            }

            $arResult[$sTemplateCode] = $obTemplate->subject." ({$obTemplate->code})";
        }

        return $arResult;
    }

    /**
     * Get status list
     * @return array
     */
    public static function getStatusList()
    {
        return [
            self::STATUS_NEW         => Lang::get('lovata.ordersshopaholic::lang.field.'.self::STATUS_NEW),
            self::STATUS_IN_PROGRESS => Lang::get('lovata.ordersshopaholic::lang.field.'.self::STATUS_IN_PROGRESS),
            self::STATUS_CANCEL      => Lang::get('lovata.ordersshopaholic::lang.field.'.self::STATUS_CANCEL),
            self::STATUS_COMPLETE    => Lang::get('lovata.ordersshopaholic::lang.field.'.self::STATUS_COMPLETE),
        ];
    }

    /**
     * Get by order ID
     * @param Task   $obQuery
     * @param string $sData
     * @return Task
     */
    public function scopeGetByOrder($obQuery, $sData)
    {
        if (!empty($sData)) {
            $obQuery->where('order_id', $sData);
        }

        return $obQuery;
    }

    /**
     * Get by manager ID
     * @param Task   $obQuery
     * @param string $sData
     * @return Task
     */
    public function scopeGetByManager($obQuery, $sData)
    {
        if (!empty($sData)) {
            $obQuery->where('manager_id', $sData);
        }

        return $obQuery;
    }

    /**
     * Get by status
     * @param Task   $obQuery
     * @param string $sData
     * @return Task
     */
    public function scopeGetByStatus($obQuery, $sData)
    {
        if (!empty($sData)) {
            $obQuery->where('status', $sData);
        }

        return $obQuery;
    }

    /**
     * Get tasks with status new + in_progress
     * @param Task $obQuery
     * @return Task
     */
    public function scopeGetActiveTask($obQuery)
    {
        $obQuery->whereIn('status', [self::STATUS_NEW, self::STATUS_IN_PROGRESS]);

        return $obQuery;
    }

    /**
     * Get tasks with status cancel + complete
     * @param Task $obQuery
     * @return Task
     */
    public function scopeGetCompletedTask($obQuery)
    {
        $obQuery->whereIn('status', [self::STATUS_CANCEL, self::STATUS_COMPLETE]);

        return $obQuery;
    }

    /**
     * Get status name
     * @return string
     */
    protected function getStatusNameAttribute()
    {
        return Lang::get('lovata.ordersshopaholic::lang.field.'.$this->status);
    }
}