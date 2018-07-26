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
    const EVENT_ORDER_CREATED_USER_MAIL_DATA = 'shopaholic.order.created.user.template.data';
    const EVENT_ORDER_CREATED_MANAGER_MAIL_DATA = 'shopaholic.order.created.manager.template.data';

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

        $this->initCartPositionList();

        //Begin transaction
        DB::beginTransaction();

        $this->createOrder();
        $this->processOrderPositionList();
        $this->sendPaymentPurchase();

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
        $obStatus = Status::getByCode(Status::STATUS_NEW)->first();
        if (empty($obStatus)) {
            return;
        }

        $this->arOrderData['status_id'] = $obStatus->id;
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

            if ($this->createOrderPosition($arOrderPositionData)) {
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
     * @param array $arOrderPositionData
     * @return bool
     */
    protected function createOrderPosition($arOrderPositionData)
    {
        try {
            OrderPosition::create($arOrderPositionData);
        } catch (\October\Rain\Database\ModelException $obException) {
            $this->processValidationError($obException);
            return false;
        }

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
}
