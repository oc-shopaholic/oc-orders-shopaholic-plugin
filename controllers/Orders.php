<?php namespace Lovata\OrdersShopaholic\Controllers;

use Backend\Classes\Controller;
use BackendMenu;
use Flash;
use Illuminate\Http\Request;
use Lang;
use DB;
use Backend;
use Lovata\OrdersShopaholic\Models\OfferOrder;
use Lovata\OrdersShopaholic\Models\Order;
use Lovata\OrdersShopaholic\Models\Status;
use Lovata\ordersshopaholic\Models\User;
use Lovata\OrdersShopaholic\Models\Phone;
use Lovata\Shopaholic\Classes\CResult;
use Lovata\Shopaholic\Models\Category;
use Lovata\Shopaholic\Models\Offer;
use Lovata\Shopaholic\Models\Product;
use Lovata\Shopaholic\Models\Settings;
use October\Rain\Database\Collection;

class Orders extends Controller
{
    public $implement = ['Backend\Behaviors\ListController','Backend\Behaviors\FormController','Backend\Behaviors\ReorderController','Backend\Behaviors\RelationController'];
    
    public $listConfig = 'config_list.yaml';
    public $formConfig = 'config_form.yaml';
    public $reorderConfig = 'config_reorder.yaml';
    public $relationConfig = 'config_relation.yaml';

    /** @var Request */
    protected $obRequest;

    public function __construct(Request $obRequest)
    {
        $this->getAssets();
        $this->obRequest = $obRequest;

        parent::__construct();
        BackendMenu::setContext('Lovata.OrdersShopaholic', 'ordersshopaholic-menu', 'ordersshopaholic-menu-orders');
    }
    
    protected function getAssets() {
        if(!\Request::ajax()) {
            $this->addJs('/plugins/lovata/ordersshopaholic/assets/js/main.js');
            $this->addCss('/plugins/lovata/ordersshopaholic/assets/css/main.css');
        }
    }

    /**
     * Ajax element list deleting
     * @return mixed
     */
    public function index_onDelete() {

        $arElementsID = $this->obRequest->input('checked');

        if(empty($arElementsID) || !is_array($arElementsID)) {
            return $this->listRefresh();
        }

        foreach($arElementsID as $iElementID) {
            if(!$obElement = Order::find($iElementID))
                continue;

            $obElement->delete();
        }

        Flash::success(Lang::get('lovata.ordersshopaholic::lang.message.delete_success'));
        return $this->listRefresh();
    }

    /**
     * Order create
     * @return mixed
     */
    public function create() {

        //Create new order
        $obOrder = new Order();
        if(empty($obOrder)) {
            return redirect(Backend::url('lovata/ordersshopaholic/orders'));
        }

        $obOrder->save();

        return redirect(Backend::url('lovata/ordersshopaholic/orders/update').'/'.$obOrder->id);
    }

    /**
     * Add offer to order
     * @return mixed
     * @throws \SystemException
     */
    public function update_onRelationButtonAddOffer() {

        $arOrderData = $this->obRequest->get('order_data');
        if(empty($arOrderData) || !isset($arOrderData['order_id'])) {
            return null;
        }
        
        $iOrderID = $arOrderData['order_id'];
        if(empty($iOrderID)) {
            return null;
        }

        $arResult = [
            'arCategory' => [],
            'iOrderID' => $iOrderID,
        ];

        //Get category list
        /** @var Collection $arCategories */
        $arCategories = Category::active()->where(function($obQuery) {
            $obQuery->whereNull('parent_id')->orWhere('parent_id', 0);
        })->orderBy('nest_left', 'asc')->get();
        if(!$arCategories->isEmpty()) {
            /** @var Category $obCategory */
            foreach($arCategories as $obCategory) {

                $arResult['arCategory'][$obCategory->id] = [
                    'name' => $obCategory->name,
                ];
            }
        }

        $sContents = $this->makePartial('offer_add', $arResult);
        return $sContents;
    }

    /**
     * Get child category data
     * @return string|null
     */
    public function update_onGetChildCategoryList() {

        $iCategoryID = $this->obRequest->input('category_id');
        $iParentCategoryID = 0;

        //Get category list
        if(empty($iCategoryID)) {
            /** @var Collection $arCategories */
            $arCategories = Category::active()->where(function($obQuery) {
                $obQuery->whereNull('parent_id')->orWhere('parent_id', 0);
            })->orderBy('nest_left', 'asc')->get();
        } else {

            //Get category object
            $obCategory = Category::find($iCategoryID);
            if(empty($obCategory)) {
                return null;
            }

            $iParentCategoryID = $obCategory->parent_id;

            $arCategories = Category::active()->getByParentID($iCategoryID)->get();
        }

        if($arCategories->isEmpty() && empty($iCategoryID)) {
            return null;
        }

        //Get category list partial
        if(!$arCategories->isEmpty()) {
            $arResult = [
                'iCurrentCategoryID' => $iCategoryID,
                'iParentCategoryID' => $iParentCategoryID,
                'arCategory' => [],
            ];

            /** @var Category $obCategory */
            foreach($arCategories as $obCategory) {

                $arResult['arCategory'][$obCategory->id] = [
                    'name' => $obCategory->name,
                ];
            }

            $sContents = $this->makePartial('child_category_list', $arResult);
            return $sContents;
        }

        //Get product list
        /** @var Collection $arProducts */
        $arProducts = Product::active()->getByCategory($iCategoryID)->get();

        $arResult = [
            'iCurrentCategoryID' => $iCategoryID,
            'iParentCategoryID' => $iParentCategoryID,
            'arProduct' => [],
        ];

        if(!$arProducts->isEmpty()) {
            /** @var Product $obProduct */
            foreach($arProducts as $obProduct) {
                $arResult['arProduct'][$obProduct->id] = [
                    'name' => $obProduct->name,
                ];
            }
        }

        $sContents = $this->makePartial('product_list', $arResult);
        return $sContents;
    }

    /**
     * Get product data
     * @return string|null
     */
    public function update_onGetProductData() {

        $iProductID = $this->obRequest->input('product_id');
        $iParentID = $this->obRequest->input('parent_id');
        if(empty($iProductID)) {
            return null;
        }

        $arProductData = Product::getCacheData($iProductID);
        if(!empty($arProductData)) {
            $arProductData = $this->setActiveOffer($arProductData);
        }

        //Get product data
        $arResult = ['arProduct' => $arProductData, 'iParentID' => $iParentID];

        $sContents = $this->makePartial('product_data', $arResult);
        return $sContents;
    }

    /**
     * Add offer to order
     * @return null|void
     */
    public function update_onAddOffer() {

        $obResult = new CResult();
        $iOfferID = $this->obRequest->input('offer_id');
        $iCount = $this->obRequest->input('count');
        $iOrderID = $this->obRequest->input('order_id');

        if(empty($iOrderID) || empty($iOfferID) || empty($iCount)) {
            return $obResult->setFalse(Lang::get('lovata.ordersshopaholic::lang.message.invalid_request'))->get();
        }

        //Get order object
        /** @var Order $obOrder */
        $obOrder = Order::with('offers')->find($iOrderID);
        if(empty($obOrder)) {
            return $obResult->setFalse(Lang::get('lovata.ordersshopaholic::lang.message.e_order_not_found'))->get();
        }

        if(!$this->checkOrderStatus($obOrder)) {
            return $obResult->setFalse(Lang::get('lovata.ordersshopaholic::lang.message.e_order_status_blocked'))->get();
        }

        //Get order offers
        $arOffers = $obOrder->offers;
        if(!$arOffers->isEmpty()) {
            /** @var Offer $obOffer */
            foreach($arOffers as $obOffer) {
                if($iOfferID == $obOffer->id) {
                    return $obResult->setFalse(Lang::get('lovata.ordersshopaholic::lang.message.e_offer_in_order'))->get();
                }
            }
        }

        //Add offer to order
        $obOffer = Offer::find($iOfferID);
        if(empty($obOrder)) {
            return $obResult->setFalse(Lang::get('lovata.ordersshopaholic::lang.message.no_added_offers'))->get();
        }

        $arPivotData = [
            'quantity' => $iCount,
            'code' => $obOffer->code,
            'price' => $obOffer->getPriceValue(),
            'old_price' => $obOffer->getOldPriceValue(),
        ];

        //Check quantity
        if(Settings::getValue('check_quantity_on_order') && $obOffer->quantity < $iCount) {
            return $obResult->setFalse(Lang::get('lovata.ordersshopaholic::lang.message.insufficient_amount'))->get();
        }

        if(Settings::getValue('decrement_quantity_after_order')) {

            //Begin transaction
            DB::beginTransaction();

            try{
                DB::table('lovata_shopaholic_offers')->where('id', $obOffer->id)->decrement('quantity', $iCount);
            }catch (\Exception $e) {
                DB::rollBack();
                return $obResult->setFalse(Lang::get('lovata.ordersshopaholic::lang.message.insufficient_amount'))->get();
            }

            DB::commit();
        }

        $obOrder->offers()->attach($obOffer, $arPivotData);
        $obOrder->save();

        $this->initRelation($obOrder, 'offers');
        $arResult = $this->relationRefresh('offers');
        
        $arResult['#Form-field-Order-price_block-group'] = $this->makePartial('price_block', ['obOrder' => $obOrder]);
        $arResult['message'] = Lang::get('lovata.ordersshopaholic::lang.message.offer_added');

        return $obResult->setTrue($arResult)->get();
    }

    /**
     * Search user
     * @return \Illuminate\Http\JsonResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function update_onSearchUser() {
        $sSearch = $this->obRequest->input('search');
        return User::searchByString($sSearch);
    }

    /**
     * Search user by name
     * @return \Illuminate\Http\JsonResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function update_onSearchUserByName() {
        $sSearch = $this->obRequest->input('search');
        return User::searchByName($sSearch);
    }

    /**
     * Search user by phone
     * @return \Illuminate\Http\JsonResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function update_onSearchUserByPhone() {
        $sSearch = $this->obRequest->input('search');
        return User::searchByPhone($sSearch);
    }

    /**
     * Save order data
     * @param null|integer $recordId
     * @param null $context
     * @return bool
     */
    public function update_onSave($recordId = null, $context = null) {

        $arOrderData = $this->obRequest->get('Order');

        /** @var Order $obOrder */
        $obOrder = Order::with('offers')->find($recordId);
        if(empty($obOrder)) {
            Flash::error(Lang::get('lovata.ordersshopaholic::lang.message.e_order_not_found'));
            return false;
        }

        if(empty($arOrderData['name'])) {
            Flash::error(Lang::get('lovata.ordersshopaholic::lang.validation.required', ['attribute' => Lang::get('lovata.ordersshopaholic::lang.fields.username')]));
            return false;
        }
        
        if(empty($arOrderData['phone']) || empty($sPhone = preg_replace("/[^0-9\+]/", "", $arOrderData['phone']))) {
            Flash::error(Lang::get('lovata.ordersshopaholic::lang.validation.required', ['attribute' => Lang::get('lovata.ordersshopaholic::lang.fields.phone')]));
            return false;
        }

        if(empty($arOrderData['email'])) {
            Flash::error(Lang::get('lovata.ordersshopaholic::lang.validation.required', ['attribute' => Lang::get('lovata.ordersshopaholic::lang.fields.email')]));
            return false;
        }

        $obOrder = Order::find($recordId);
        if(!$this->checkOrderStatus($obOrder, $arOrderData['status'])) {
            return false;
        }
        
        //Create new user
        if(isset($arOrderData['user_new']) && $arOrderData['user_new']) {
            if(!$this->createNewUser($arOrderData, $sPhone)) {
                return false;
            }
        } else {
            if(!$this->checkUserData($arOrderData, $sPhone)) {
                return false;
            }
        }
        
        if(!$this->updateOffersQuantity($obOrder)) {
            return false;
        }

        parent::update_onSave($recordId, $context);

        $bClose = $this->obRequest->get('close');
        if($bClose) {
            return redirect(Backend::url('lovata/ordersshopaholic/orders'));
        }
        
        $this->initRelation($obOrder, 'offers');
        $arResult = $this->relationRefresh('offers');

        $obOrder = Order::find($recordId);
        $arResult['#Form-field-Order-price_block-group'] = $this->makePartial('price_block', ['obOrder' => $obOrder]);
        
        return $arResult;
    }

    /**
     * Create new user
     * @param $arOrderData
     * @param $sPhone
     * @return bool
     */
    protected function createNewUser($arOrderData, $sPhone) {

        //Get user by email
        /** @var User $obUser */
        $obUser = User::withTrashed()->email($arOrderData['email'])->first();
        if(!empty($obUser)) {
            Flash::error(Lang::get('lovata.ordersshopaholic::lang.error.email_cant_use', ['name' => $obUser->name]));
            return false;
        }
        
        //Get user by phone
        $obUser = User::withTrashed()->phone($sPhone)->first();
        if(!empty($obUser)) {
            Flash::error(Lang::get('lovata.ordersshopaholic::lang.error.phone_cant_use', ['name' => $obUser->name]));
            return false;
        }

        //Create user and phone
        $obUser = User::createFromOrderData($arOrderData);
        if(empty($obUser)) {
            Flash::error(Lang::get('lovata.ordersshopaholic::lang.error.user_cant_create'));
            return false;
        }

        Phone::create([
            'user_id' => $obUser->id,
            'phone' => $arOrderData['phone'],
        ]);
        
        return true;
    }

    /**
     * Check user data
     * @param $arOrderData
     * @param $sPhone
     * @return bool
     */
    protected function checkUserData($arOrderData, $sPhone) {

        //Get user by phone
        $obUser = User::withTrashed()->phone($sPhone)->first();
        if(!empty($obUser) && $obUser->id != $arOrderData['user_search']) {
            
            Flash::error(Lang::get('lovata.ordersshopaholic::lang.error.phone_cant_use', ['name' => $obUser->name]));
            return false;
            
        } else if(empty($obUser)) {
            //Create user phone
            Phone::create([
                'user_id' => $arOrderData['user_search'],
                'phone' => $arOrderData['phone'],
            ]);
        }
        
        return true;
    }

    /**
     * Update order offers data
     * @param Order $obOrder
     * @return bool
     */
    protected function updateOffersQuantity($obOrder) {

        //Get offer list
        $arOfferList = $this->obRequest->get('Offer');
        if(empty($arOfferList)) {
            return true;
        }

        //Get offers
        $arOffers = $obOrder->offers;
        if($arOffers->isEmpty()) {
            return true;
        }
        
        //Process offers
        /** @var Offer $obOffer */
        foreach($arOffers as $obOffer) {
            
            if(!isset($arOfferList[$obOffer->id])) {
                continue;
            }
            
            $bNeedSave = false;
            
            //Update price
            if(isset($arOfferList[$obOffer->id]['price']) && $arOfferList[$obOffer->id]['price'] > 0 && $arOfferList[$obOffer->id]['price'] != $obOffer->pivot->getPriceValue()) {
                $obOffer->pivot->price = $arOfferList[$obOffer->id]['price'];
                $bNeedSave = true;
            }
            
            //Update quantity
            if(isset($arOfferList[$obOffer->id]['quantity']) && $arOfferList[$obOffer->id]['quantity'] > 0 && $arOfferList[$obOffer->id]['quantity'] != $obOffer->pivot->quantity) {
                $obOffer->pivot->quantity = $arOfferList[$obOffer->id]['quantity'];
                $bNeedSave = true;
                
            }
            
            //Save pivot data
            if($bNeedSave) {
                $bOfferSave = $obOffer->pivot->save();
                if(!$bOfferSave) {
                    Flash::error(Lang::get('lovata.ordersshopaholic::lang.message.e_offer_order_quantity', ['name' => $obOffer->name]));
                    return false;
                }
            }
        }
        
        return true;
    }

    /**
     * Delete offer from order
     * @return bool
     */
    public function update_onRelationButtonRemove() {

        //Get order data
        $arOrderData = $this->obRequest->get('order_data');
        
        if(empty($arOrderData) || !isset($arOrderData['order_id'])) {
            Flash::error(Lang::get('lovata.ordersshopaholic::lang.message.invalid_request'));
            return false;
        }
        
        //Get offers list id
        $arOffersListID = $this->obRequest->get('offer_list');
        $arOffersListID = array_unique($arOffersListID);
        
        if(empty($arOffersListID)) {
            return parent::onRelationButtonRemove();
        }

        //Get order object
        /** @var Order $obOrder */
        $obOrder = Order::with('offers')->find($arOrderData['order_id']);
        if(empty($obOrder)) {
            Flash::error(Lang::get('lovata.ordersshopaholic::lang.message.e_order_not_found'));
            return false;
        }
        
        if(!$this->checkOrderStatus($obOrder)) {
            return false;
        }
        
        //Get offers list
        $arOffers = $obOrder->offers;
        if($arOffers->isEmpty()) {
            return parent::onRelationButtonRemove();
        }
        
        //Process offers
        /** @var Offer $obOffer */
        foreach($arOffers as $obOffer) {
            if(in_array($obOffer->id, $arOffersListID)) {
                if(!OfferOrder::addOrderOfferQuantityBeforeDelete($obOffer)) {
                    Flash::error(Lang::get('lovata.ordersshopaholic::lang.message.e_offer_order_quantity', ['name' => $obOffer->name]));
                    return false;
                }
            }
        }
        
        $arResult = parent::onRelationButtonRemove();
        $obOrder->save();
        
        $obOrder = Order::find($arOrderData['order_id']);
        $arResult['#Form-field-Order-price_block-group'] = $this->makePartial('price_block', ['obOrder' => $obOrder]);
        return $arResult;
    }

    /**
     * Check order status for updating
     * @param Order $obOrder
     * @param $iNewStatus
     * @return bool
     */
    protected function checkOrderStatus($obOrder, $iNewStatus = null) {
        
        if(empty($obOrder)) {
            return true;
        }
        
        //Get blocked statuses
        $arStatus = Status::getBlockedStatusList();
        if(empty($arStatus)) {
            return true;
        }
        
        if(in_array($obOrder->status_id, $arStatus) && ((!empty($iNewStatus) && in_array($iNewStatus, $arStatus)) || empty($iNewStatus))) {
            Flash::error(Lang::get('lovata.ordersshopaholic::lang.message.e_order_status_blocked'));
            return false;
        }
        
        return true;
    }

    /**
     * Set active offer for product
     * @param array $arProduct
     * @return array
     */
    protected function setActiveOffer($arProduct) {

        $iPMinPrice = null;
        $bHasDiscountPrice = false;
        $iActiveOffer = 0;

        if(empty($arProduct['offers'])) {
            return $arProduct;
        }

        foreach($arProduct['offers'] as $iOfferID => $arOffer) {

            //Check discount price
            if(!empty($arOffer['old_price_value']) && $arOffer['old_price_value'] > 0 && !$bHasDiscountPrice) {
                $iPMinPrice = $arOffer['price_value'];
                $bHasDiscountPrice = true;
                $iActiveOffer = $iOfferID;
                continue;
            }

            $bSetActiveOffer = $iPMinPrice === null
                || (!$bHasDiscountPrice && $iPMinPrice > $arOffer['price_value'])
                || ($bHasDiscountPrice && !empty($arOffer['old_price_value']) && $arOffer['old_price_value'] > 0 && $iPMinPrice > $arOffer['price_value']);

            //Set first active price
            if($bSetActiveOffer) {
                $iPMinPrice = $arOffer['price_value'];
                $iActiveOffer = $iOfferID;
            }
        }

        $arProduct['active_offer'] = $iActiveOffer;
        return $arProduct;
    }
}