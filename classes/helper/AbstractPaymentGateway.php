<?php namespace Lovata\OrdersShopaholic\Classes\Helper;

use Event;
use Redirect;
use Lovata\OrdersShopaholic\Classes\Processor\CartProcessor;
use Lovata\OrdersShopaholic\Classes\Processor\OfferCartPositionProcessor;
use Lovata\OrdersShopaholic\Models\Order;
use Lovata\OrdersShopaholic\Interfaces\PaymentGatewayInterface;

/**
 * Class AbstractPaymentGateway
 * @package Lovata\OrdersShopaholic\Classes\Helper
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
abstract class AbstractPaymentGateway implements PaymentGatewayInterface
{
    /** @var \Lovata\OrdersShopaholic\Models\Order */
    protected $obOrder;

    /** @var \Lovata\OrdersShopaholic\Models\PaymentMethod */
    protected $obPaymentMethod;

    /** @var array */
    protected $arPurchaseData = [];

    protected $bIsRedirect = false;
    protected $bIsSuccessful = false;

    /**
     * @param \Lovata\OrdersShopaholic\Models\Order $obOrder
     */
    public function purchase($obOrder)
    {
        if (empty($obOrder) || !$obOrder instanceof Order || empty($obOrder->payment_method)) {
            return;
        }

        $this->obOrder = $obOrder;
        $this->obPaymentMethod = $obOrder->payment_method;

        $this->preparePurchaseData();
        if (!$this->validatePurchaseData()) {
            return;
        }

        $this->sendPurchaseData();
        $this->processPurchaseResponse();
    }

    /**
     * Return true, if response has redirect URL
     * @return bool
     */
    public function isRedirect() : bool
    {
        return (bool) $this->bIsRedirect;
    }

    /**
     * Return true, if result of purchase is successful
     * @return bool
     */
    public function isSuccessful() : bool
    {
        return (bool) $this->bIsSuccessful;
    }

    /**
     * Get response array
     * @return string
     */
    abstract public function getResponse() : array;

    /**
     * Get redirect URL
     * @return string
     */
    abstract public function getRedirectURL() : string;

    /**
     * Get response message
     * @return string
     */
    abstract public function getMessage() : string;

    /**
     * Get gateway property value from array
     * @param string $sFieldName
     * @return null|string
     */
    protected function getGatewayProperty($sFieldName)
    {
        if (empty($this->obPaymentMethod) || empty($sFieldName)) {
            return null;
        }

        $arGatewayProperty = $this->obPaymentMethod->gateway_property;
        if (empty($arGatewayProperty) || !isset($arGatewayProperty[$sFieldName])) {
            return null;
        }

        $sResult = $arGatewayProperty[$sFieldName];

        return $sResult;
    }

    /**
     * Get order property value from array
     * @param string $sFieldName
     * @return null|string
     */
    protected function getOrderProperty($sFieldName)
    {
        if (empty($this->obOrder) || empty($sFieldName)) {
            return null;
        }

        $arPropertyList = $this->obOrder->property;
        if (empty($arPropertyList) || !isset($arPropertyList[$sFieldName])) {
            return null;
        }

        $sResult = $arPropertyList[$sFieldName];

        return $sResult;
    }

    /**
     * Set "wait payment" status
     */
    protected function setWaitPaymentStatus()
    {
        $obStatus = $this->obPaymentMethod->before_status;
        if (empty($obStatus)) {
            return;
        }

        $this->obOrder->status_id = $obStatus->id;
        $this->obOrder->save();
    }

    /**
     * Set success status
     */
    protected function setSuccessStatus()
    {
        $obStatus = $this->obPaymentMethod->after_status;
        if (empty($obStatus)) {
            return;
        }

        $this->obOrder->status_id = $obStatus->id;
        $this->obOrder->save();
    }

    /**
     * Set cancel status
     */
    protected function setCancelStatus()
    {
        $obStatus = $this->obPaymentMethod->cancel_status;
        if (empty($obStatus)) {
            return;
        }

        $this->obOrder->status_id = $obStatus->id;
        $this->obOrder->save();

        if ($this->obPaymentMethod->restore_cart) {
            CartProcessor::instance()->restoreFromOrder($this->obOrder, OfferCartPositionProcessor::class);
        }
    }

    /**
     * Set fail status
     */
    protected function setFailStatus()
    {
        $obStatus = $this->obPaymentMethod->fail_status;
        if (empty($obStatus)) {
            return;
        }

        $this->obOrder->status_id = $obStatus->id;
        $this->obOrder->save();

        if ($this->obPaymentMethod->restore_cart) {
            CartProcessor::instance()->restoreFromOrder($this->obOrder, OfferCartPositionProcessor::class);
        }
    }

    /**
     * Get redirect response on index page or other page
     * @param string $sEventName
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function returnRedirectResponse($sEventName)
    {
        if (empty($sEventName)) {
            return Redirect::to('/');
        }

        //Fire event
        $arEventList = Event::fire($sEventName, $this->obOrder);
        if (empty($arEventList)) {
            return Redirect::to('/');
        }

        foreach ($arEventList as $sRedirectURL) {
            if (empty($sRedirectURL) || !is_string($sRedirectURL)) {
                continue;
            }

            return Redirect::to($sRedirectURL);
        }

        return Redirect::to('/');
    }

    /**
     * Init order object by ID
     * @param int $iOrderID
     */
    protected function initOrderObject($iOrderID)
    {
        if (empty($iOrderID)) {
            return;
        }

        $this->obOrder = Order::getBySecretKey($iOrderID)->first();
        if (empty($this->obOrder)) {
            $this->obOrder = Order::find($iOrderID);
            if (empty($this->obOrder)) {
                return;
            }
        }

        $this->obPaymentMethod = $this->obOrder->payment_method;
    }

    /**
     * Prepare purchase data array for request
     * @return void
     */
    abstract protected function preparePurchaseData();

    /**
     * Validate purchase data array for request
     * @return bool
     */
    abstract protected function validatePurchaseData();

    /**
     * Send purchase data array for request
     * @return void
     */
    abstract protected function sendPurchaseData();

    /**
     * Process purchase response
     * @return void
     */
    abstract protected function processPurchaseResponse();
}
