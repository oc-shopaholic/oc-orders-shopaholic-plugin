<?php namespace Lovata\OrdersShopaholic\Classes\Processor;

use DB;
use Lang;
use Event;
use October\Rain\Support\Traits\Singleton;

use Kharanenka\Helper\Result;
use Lovata\Toolbox\Traits\Helpers\TraitValidationHelper;

use Lovata\OrdersShopaholic\Models\Order;
use Lovata\OrdersShopaholic\Models\Status;
use Lovata\OrdersShopaholic\Models\OrderPosition;
use Lovata\OrdersShopaholic\Models\OrderPromoMechanism;
use Lovata\OrdersShopaholic\Models\PromoMechanism;
use Lovata\OrdersShopaholic\Classes\Item\ShippingTypeItem;
use Lovata\OrdersShopaholic\Classes\PromoMechanism\OrderPromoMechanismProcessor;

/**
 * Class OrderProcessor
 * @package Lovata\OrdersShopaholic\Classes\Processor
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class OrderProcessor
{
    use Singleton;
    use TraitValidationHelper;

    const EVENT_ORDER_CREATED = 'shopaholic.order.created';
    const EVENT_ORDER_POSITION_CREATED = 'shopaholic.order_position.created';
    const EVENT_ORDER_FIND_USER_BEFORE_CREATE = 'shopaholic.order.find_user_before_create';
    const EVENT_ORDER_USER_CREATED = 'shopaholic.order.user_created';
    const EVENT_ORDER_GET_REDIRECT_URL = 'shopaholic.order.get_redirect_url';
    const EVENT_UPDATE_ORDER_DATA = 'shopaholic.order.update_data';
    const EVENT_UPDATE_ORDER_BEFORE_CREATE = 'shopaholic.order.before_create';
    const EVENT_UPDATE_ORDER_AFTER_CREATE = 'shopaholic.order.after_create';
    const EVENT_UPDATE_ORDER_RESPONSE_AFTER_CREATE = 'shopaholic.order.response_after_create';
    const EVENT_ORDER_CREATED_USER_MAIL_DATA = 'shopaholic.order.created.user.template.data';
    const EVENT_ORDER_CREATED_MANAGER_MAIL_DATA = 'shopaholic.order.created.manager.template.data';
    const EVENT_GET_SHIPPING_PRICE = 'shopaholic.order.get_shipping_price';

    /** @var \Lovata\Buddies\Models\User */
    protected $obUser;

    /** @var array */
    protected $arOrderData;

    /** @var null|Order */
    protected $obOrder = null;

    /** @var \Lovata\OrdersShopaholic\Classes\Collection\CartPositionCollection|\Lovata\OrdersShopaholic\Classes\Item\CartPositionItem[] */
    protected $obCartPositionList;

    /** @var \Lovata\OrdersShopaholic\Interfaces\PaymentGatewayInterface|null */
    protected $obPaymentGateway;

    /**
     * Create new order
     * @param                             $arOrderData
     * @param \Lovata\Buddies\Models\User $obUser
     * @return Order|null
     * @throws \Exception
     */
    public function create($arOrderData, $obUser = null)
    {
        $this->initOrderData($arOrderData);
        $this->initUser($obUser);
        $this->setOrderStatus();
        $this->updateOrderData();
        $this->fillShippingTypePrice();

        $this->initCartPositionList();
        if (!Result::status()) {
            return null;
        }

        //Begin transaction
        DB::beginTransaction();

        //Fire event before create order
        if (Event::fire(self::EVENT_UPDATE_ORDER_BEFORE_CREATE, [$this->arOrderData, $this->obUser], true) === false) {
            return null;
        }

        $this->createOrder();
        $this->processOrderPositionList();

        if (!empty($this->obOrder)) {
            //Fire event after create order
            Event::fire(self::EVENT_UPDATE_ORDER_AFTER_CREATE, $this->obOrder);
            $this->attachPromoMechanism();

            OrderPromoMechanismProcessor::update($this->obOrder);
            if ($this->obOrder->total_price_value > 0) {
                $this->sendPaymentPurchase();
            }
        }

        if (!Result::status()) {
            DB::rollBack();
            return null;
        }

        DB::commit();

        $this->obOrder->save();

        Event::fire(self::EVENT_ORDER_CREATED, $this->obOrder);

        CartProcessor::instance()->clear();

        $arResult = [
            'id'     => $this->obOrder->id,
            'number' => $this->obOrder->order_number,
            'key'    => $this->obOrder->secret_key,
        ];

        Result::setTrue($arResult);

        return $this->obOrder;
    }

    /**
     * Get payment gateway object
     * @return \Lovata\OrdersShopaholic\Interfaces\PaymentGatewayInterface|null
     */
    public function getPaymentGateway()
    {
        return $this->obPaymentGateway;
    }

    /**
     * Init order data
     * @param array $arOrderData
     */
    protected function initOrderData($arOrderData)
    {
        $this->arOrderData = $arOrderData;
        if (!empty($this->arOrderData)) {
            return;
        }

        $this->arOrderData = [];
    }

    /**
     * Init user object
     * @param \Lovata\Buddies\Models\User $obUser $obUser
     */
    protected function initUser($obUser)
    {
        $this->obUser = $obUser;
        if (empty($this->obUser)) {
            return;
        }

        $this->arOrderData['user_id'] = $this->obUser->id;
    }

    /**
     * Set order status (new)
     */
    protected function setOrderStatus()
    {
        $obStatus = Status::getFirstByCode(Status::STATUS_NEW);
        if (empty($obStatus)) {
            return;
        }

        $this->arOrderData['status_id'] = $obStatus->id;
    }

    /**
     * Update order data
     * Fire event and update order data
     */
    protected function updateOrderData()
    {
        $arEventDataList = Event::fire(self::EVENT_UPDATE_ORDER_DATA, [$this->arOrderData]);
        if (empty($arEventDataList)) {
            return;
        }

        foreach ($arEventDataList as $arEventData) {
            if (empty($arEventData) || !is_array($arEventData)) {
                continue;
            }

            foreach ($arEventData as $sKey => $sValue) {
                $this->arOrderData[$sKey] = $sValue;
            }
        }
    }

    /**
     * Get shipping type price
     */
    protected function fillShippingTypePrice()
    {
        $fShippingPrice = Event::fire(self::EVENT_GET_SHIPPING_PRICE, [$this->arOrderData], true);
        if ($fShippingPrice !== null) {
            $this->arOrderData['shipping_price'] = (float) $fShippingPrice;
        }

        $iShippingTypeID = array_get($this->arOrderData, 'shipping_type_id');
        if (empty($iShippingTypeID)) {
            return;
        }

        //Get shipping type object
        $obShippingTypeItem = ShippingTypeItem::make($iShippingTypeID);

        if ($fShippingPrice !== null) {
            $this->arOrderData['shipping_price'] = (float) $fShippingPrice;
        } else {
            $this->arOrderData['shipping_price'] = $obShippingTypeItem->getFullPriceValue();
            $obApiClass = $obShippingTypeItem->api;
            if (!empty($obApiClass) && !$obApiClass->validate()) {
                Result::setFalse()->setMessage($obApiClass->getMessage());
            }
        }
        $this->arOrderData['shipping_tax_percent'] = $obShippingTypeItem->tax_percent;
    }

    /**
     * Init cart position list, validate positions
     */
    protected function initCartPositionList()
    {
        if (!Result::status()) {
            return;
        }

        //Get cart element list
        $this->obCartPositionList = CartProcessor::instance()->get();

        foreach ($this->obCartPositionList as $obCartPositionItem) {
            $obProcessor = $obCartPositionItem->getOrderPositionProcessor();
            if (!$obProcessor->validate()) {
                $this->obCartPositionList->exclude($obCartPositionItem->id);
            }
        }

        if ($this->obCartPositionList->isEmpty()) {
            $sMessage = Lang::get('lovata.ordersshopaholic::lang.message.empty_cart');
            Result::setFalse()->setMessage($sMessage);
        }
    }

    /**
     * Process order position list (create in DB)
     */
    protected function processOrderPositionList()
    {
        if (!Result::status()) {
            return;
        }

        /** @var \Lovata\OrdersShopaholic\Classes\Item\CartPositionItem $obCartPositionItem */
        foreach ($this->obCartPositionList as $obCartPositionItem) {

            $obProcessor = $obCartPositionItem->getOrderPositionProcessor();
            if (!$obProcessor->check()) {
                break;
            }

            $arOrderPositionData = $obProcessor->getData();
            $arOrderPositionData['order_id'] = $this->obOrder->id;

            if ($this->createOrderPosition($arOrderPositionData, $obCartPositionItem)) {
                continue;
            }

            $arErrorData = Result::data();
            $arErrorData['cart_position_id'] = $obCartPositionItem->id;
            Result::setFalse($arErrorData);

            break;
        }
    }

    /**
     * Create order position
     * @param array                                                  $arOrderPositionData
     * @param \Lovata\OrdersShopaholic\Classes\Item\CartPositionItem $obCartPositionItem
     * @return bool
     */
    protected function createOrderPosition($arOrderPositionData, $obCartPositionItem)
    {
        try {
            $obOrderPosition = OrderPosition::create($arOrderPositionData);
            $this->obOrder->order_position()->add($obOrderPosition);
        } catch (\October\Rain\Database\ModelException $obException) {
            $this->processValidationError($obException);
            return false;
        }

        Event::fire(self::EVENT_ORDER_POSITION_CREATED, [$obCartPositionItem, $obOrderPosition]);

        return true;
    }

    /**
     * Create new order
     */
    protected function createOrder()
    {
        if (!Result::status()) {
            return;
        }

        try {
            $this->obOrder = Order::create($this->arOrderData);
        } catch (\October\Rain\Database\ModelException $obException) {
            $this->processValidationError($obException);
            return;
        }
    }

    /**
     * Check payment type, send purchase if need
     */
    protected function sendPaymentPurchase()
    {
        if (!Result::status()) {
            return;
        }

        //Get order payment
        if (empty($this->obOrder)) {
            return;
        }

        $obPaymentMethod = $this->obOrder->payment_method;
        if (empty($obPaymentMethod) || !$obPaymentMethod->send_purchase_request) {
            return;
        }

        $this->obPaymentGateway = $this->obOrder->payment_method->gateway;
        if (empty($this->obPaymentGateway)) {
            return;
        }

        $this->obPaymentGateway->purchase($this->obOrder);
        if (!$this->obPaymentGateway->isRedirect() && !$this->obPaymentGateway->isSuccessful()) {
            Result::setFalse($this->obPaymentGateway->getResponse())->setMessage($this->obPaymentGateway->getMessage());
        }
    }

    /**
     * Attach promo mechanisms with auto add == true
     */
    protected function attachPromoMechanism()
    {
        $obPromoMechanismList = PromoMechanism::getAutoAdd()->get();
        if ($obPromoMechanismList->isEmpty()) {
            return;
        }

        /** @var PromoMechanism $obPromoMechanism */
        foreach ($obPromoMechanismList as $obPromoMechanism) {
            try {
                $arPromoMechanismData = [
                    'order_id'       => $this->obOrder->id,
                    'mechanism_id'   => $obPromoMechanism->id,
                    'name'           => $obPromoMechanism->name,
                    'type'           => $obPromoMechanism->type,
                    'increase'       => $obPromoMechanism->increase,
                    'priority'       => $obPromoMechanism->priority,
                    'discount_value' => $obPromoMechanism->discount_value,
                    'discount_type'  => $obPromoMechanism->discount_type,
                    'final_discount' => $obPromoMechanism->final_discount,
                    'property'       => $obPromoMechanism->property,
                    'element_id'     => $obPromoMechanism->id,
                    'element_type'   => PromoMechanism::class,
                    'element_data'   => [],
                ];

                $this->obOrder->order_promo_mechanism()->add(OrderPromoMechanism::create($arPromoMechanismData));
            } catch (\Exception $obException) {
                return;
            }
        }
    }
}
