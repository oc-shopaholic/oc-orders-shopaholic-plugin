<?php namespace Lovata\OrdersShopaholic\Models;

use Kharanenka\Helper\CustomValidationMessage;
use Lovata\Buddies\Models\User;
use Lovata\OrdersShopaholic\Plugin;
use Model;
use Carbon\Carbon;
use October\Rain\Database\Builder;
use October\Rain\Database\Relations\BelongsTo;
use October\Rain\Database\Traits\Validation;
use Lovata\Toolbox\Plugin as ToolboxPlugin;

/**
 * Class Phone
 * @package Lovata\OrdersShopaholic\Models
 * @author Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 *
 * @mixin Builder
 * @mixin \Eloquent
 * 
 * @property int $id
 * @property int $user_id
 * @property string $formatted_phone
 * @property string $phone
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property User $user
 * 
 * @method static $this|BelongsTo user()
 * @method static $this phone(string $sPhone)
 * @method static $this phoneLike(string $sPhone)
 */
class Phone extends Model
{
    use Validation;
    use CustomValidationMessage;
    
    public $table = 'lovata_ordersshopaholic_phones';

    /** Validation */
    public $rules = [
        'phone' => 'required',
        'formatted_phone' => 'unique:lovata_ordersshopaholic_phones'
    ];
    public $customMessages = [];
    public $attributeNames = [];
    
    protected $fillable = [
        'user_id',
        'formatted_phone',
        'phone',
    ];

    protected $dates = ['created_at', 'updated_at'];

    public $belongsTo = [
        'user' => ['Lovata\OrdersShopaholic\Models\User'],
    ];

    /**
     * Phone constructor.
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        $this->setCustomMessage(ToolboxPlugin::NAME, ['required', 'unique']);
        $this->setCustomAttributeName(Plugin::NAME, ['phone', 'formatted_phone']);
        parent::__construct($attributes);
    }
    
    public function beforeValidate() {
        $this->formatted_phone = $this->phone;
    }
    
    /**
     * Set formatted phone value
     * @param string $sValue
     */
    public function setFormattedPhoneAttribute($sValue)
    {
        $this->attributes['formatted_phone'] = preg_replace("/[^0-9\+]/", "", $sValue);
    }

    /**
     * Get by phone
     * @param \Illuminate\Database\Eloquent\Builder $obQuery
     * @param string $sPhone
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePhone($obQuery, $sPhone)
    {
        $sPhone = preg_replace("/[^0-9\+]/", "", $sPhone);
        if(!empty($sPhone)) {
            $obQuery->where('formatted_phone', $sPhone);
        }
        
        return $obQuery;
    }

    /**
     * Get by phone (like)
     * @param \Illuminate\Database\Eloquent\Builder $obQuery
     * @param string $sPhone
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePhoneLike($obQuery, $sPhone)
    {
        $sPhone = preg_replace("/[^0-9\+]/", "", $sPhone);
        if(!empty($sPhone)) {
            $obQuery->where('formatted_phone', 'like', '%'.$sPhone.'%');
        }

        return $obQuery;
    }
}