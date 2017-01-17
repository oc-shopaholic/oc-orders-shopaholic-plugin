<?php namespace Lovata\OrdersShopaholic\Models;

use Lang;
use Carbon\Carbon;
use October\Rain\Database\Collection;
use RainLab\User\Models\User as UserModel;

/**
 * Class User
 * @package Lovata\OrdersShopaholic\Models
 * @author Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 * @author Denis Plisko, d.plisko@lovata.com, LOVATA Group
 * 
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $password
 * @property bool $is_activated
 * @property Carbon $activated_at
 * @property Carbon $last_login
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property string $username
 * @property string $surname
 * @property string $phone
 * @property Carbon $deleted_at
 * @property Collection $phones
 * @property Collection $order_address
 * @property Collection $orders
 * @method static $this email(int $sEmail)
 * @method static $this phone(int $sPhone)
 * @method static $this getByNameLike(int $sName)
 * @method static $this getBySurnameLike(int $sName)
 */
class User1 extends UserModel
{
    const SEARCH_COUNT_NUMBER = 5;

    /**
     * Get user by contact phone
     * @param \Illuminate\Database\Eloquent\Builder $obQuery
     * @param $sPhone
     * @return mixed
     */
    public function scopePhone($obQuery, $sPhone)
    {
        return $obQuery->whereHas('phones', function ($obQuery) use ($sPhone) {
            $obQuery->phone($sPhone);
        });
    }

    /**
     * Get user by name (LIKE)
     * @param \Illuminate\Database\Eloquent\Builder $obQuery
     * @param string $sData
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeGetByNameLike($obQuery, $sData) {

        if(!empty($sData)) {
            return $obQuery->where('name', 'like', '%'.$sData.'%');
        }

        return $obQuery;
    }

    /**
     * Get user by surname (LIKE)
     * @param \Illuminate\Database\Eloquent\Builder $obQuery
     * @param string $sData
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeGetBySurnameLike($obQuery, $sData) {

        if(!empty($sData)) {
            return $obQuery->where('surname', 'like', '%'.$sData.'%');
        }

        return $obQuery;
    }

    public function setPermissionsAttribute($permissions) {
        $this->attributes['permissions'] = !empty($permissions) ? json_encode($permissions) : '';
    }

    /**
     * Create user from order data
     * @param array $arOrderData
     * @return null|User
     */
    public static function createFromOrderData($arOrderData) {
    
        
    }

    //------------------------ User search block ------------------------
    
    /**
     * Search user by string
     * @param string $sSearch
     * @return \Illuminate\Http\JsonResponse|\Symfony\Component\HttpFoundation\Response
     */
    public static function searchByString($sSearch) {

        $arResultData = [];
        if(empty($sSearch)) {
            return ['value' => $sSearch, 'data' => $arResultData];
        }

        $sSearchNumber = preg_replace('%\D%', '', $sSearch);
        if(mb_strlen($sSearchNumber) >= self::SEARCH_COUNT_NUMBER) {
            $arResultData = self::getSearchResult($sSearchNumber, 'getSearchResultByPhone');
        } else {
            $arResultData = self::getSearchResult($sSearch, 'getSearchResultByName');
        }

        return ['value' => $sSearch, 'data' => $arResultData];
    }

    /**
     * Search user by name
     * @param string $sSearch
     * @return \Illuminate\Http\JsonResponse|\Symfony\Component\HttpFoundation\Response
     */
    public static function searchByName($sSearch) {

        $arResultData = [];
        if(empty($sSearch)) {
            return ['value' => $sSearch, 'data' => $arResultData];
        }

        $arResultData = self::getSearchResult($sSearch, 'getSearchResultByName');
        return ['value' => $sSearch, 'data' => $arResultData];
    }

    /**
     * Search user by phone
     * @param string $sSearch
     * @return \Illuminate\Http\JsonResponse|\Symfony\Component\HttpFoundation\Response
     */
    public static function searchByPhone($sSearch) {

        $arResultData = [];
        if(empty($sSearch)) {
            return ['value' => $sSearch, 'data' => $arResultData];
        }

        $arResultData = self::getSearchResult($sSearch, 'getSearchResultByPhone');
        return ['value' => $sSearch, 'data' => $arResultData];
    }

    /**
     * Get search result
     * @param string $sSearch
     * @param string $sMethodName
     * @return \Illuminate\Http\JsonResponse
     */
    public static function getSearchResult($sSearch, $sMethodName) {

        $arResultData = [];
        if(empty($sSearch) || empty($sMethodName)) {
            return $arResultData;
        }

        //Get elements ID list
        if(method_exists('\Lovata\OrdersShopaholic\Models\User', $sMethodName)) {
            $arResult = self::$sMethodName($sSearch);
        }

        if(empty($arResult)) {
            return $arResultData;
        }

        //Get first 10 variants
        $arResult = array_slice($arResult, 0, 10);

        //Get user data
        $arElementList = self::with('phones')->whereIn('id', $arResult)->get();
        if($arElementList->isEmpty()) {
            return $arResultData;
        }

        /** @var User $obElement */
        foreach($arElementList as $obElement) {
            
            $arElementData = [
                'id' => $obElement->id,
                'name' => $obElement->name,
                'email' => $obElement->email,
                'phone' => [],
            ];
            
            /** @var \October\Rain\Database\Collection $arPhones */
            $arPhones = $obElement->phones;
            if(!$arPhones->isEmpty()) {
                /** @var Phone $obPhone */
                foreach($arPhones as $obPhone) {
                    $arElementData['phone'][] = $obPhone->phone;
                }
            }
            
            $arResultData[] = $arElementData;
        }

        return $arResultData;
    }

    /**
     * Search user by name
     * @param string $sSearch
     * @return array
     */
    protected static function getSearchResultByName($sSearch) {

        $arResult = [];
        if(empty($sSearch)) {
            return $arResult;
        }

        //Splitting a string into words
        $arSearchWords = explode(' ', $sSearch);

        //Processed received word
        foreach($arSearchWords as $sWord) {
            $sWord = rtrim($sWord, ',.');
            if(empty($sWord) || mb_strlen($sWord) < 3) {
                continue;
            }

            //Search by name
            $arVariants = self::getByNameLike($sWord)->lists('id');
            if(!empty($arVariants)) {
                //Renew result array
                if(empty($arResult)) {
                    $arResult = $arVariants;
                } else {
                    $arResult = array_intersect($arResult, $arVariants);
                }
            }

            //Search by surname
            $arVariants = self::getBySurnameLike($sWord)->lists('id');
            if(!empty($arVariants)) {
                //Renew result array
                if(empty($arResult)) {
                    $arResult = $arVariants;
                } else {
                    $arResult = array_intersect($arResult, $arVariants);
                }
            }
        }

        return $arResult;
    }

    /**
     * Search by phone
     * @param string $sSearch
     * @return array
     */
    protected static function getSearchResultByPhone($sSearch) {

        $arResult = [];
        if(empty($sSearch)) {
            return $arResult;
        }

        $sSearch = preg_replace('%\D%', "", $sSearch);
        if(empty($sSearch) || mb_strlen($sSearch) < self::SEARCH_COUNT_NUMBER) {
            return [];
        }

        $arResult = Phone::phoneLike($sSearch)->lists('user_id');
        return $arResult;
    }
}
