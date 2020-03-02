<?php namespace Lovata\OrdersShopaholic\Components;

use Redirect;
use Kharanenka\Helper\Result;
use Lovata\Toolbox\Classes\Helper\UserHelper;
use Lovata\Toolbox\Classes\Component\ElementPage;
use Lovata\Toolbox\Traits\Helpers\TraitComponentNotFoundResponse;

use Lovata\OrdersShopaholic\Models\Order;
use Lovata\OrdersShopaholic\Classes\Item\OrderItem;

/**
 * Class OrderPage
 * @package Lovata\OrdersShopaholic\Components
 * @author Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class OrderPage extends ElementPage
{
    use TraitComponentNotFoundResponse;

    /** @var Order */
    public $obElement = null;

    /** @var \Lovata\Buddies\Models\User */
    public $obUser;

    /** @var \Lovata\OrdersShopaholic\Models\PaymentMethod */
    public $obPaymentMethod = null;

    /** @var array Component property list */
    protected $arPropertyList = [];

    /**
     * @return array
     */
    public function componentDetails()
    {
        return [
            'name'          => 'lovata.ordersshopaholic::lang.component.order_page_name',
            'description'   => 'lovata.ordersshopaholic::lang.component.order_page_description',
        ];
    }

    /**
     * @return array
     */
    public function defineProperties()
    {
        $this->arPropertyList = array_merge($this->arPropertyList, $this->getElementPageProperties());

        return $this->arPropertyList;
    }

    /**
     * Init plugin method
     */
    public function init()
    {
        parent::init();

        if (!empty($this->obElement)) {
            $this->obPaymentMethod = $this->obElement->payment_method;
        }
    }

    /**
     * Get PaymentMethod object
     * @return \Lovata\OrdersShopaholic\Models\PaymentMethod
     */
    public function getPaymentMethod()
    {
        return $this->obPaymentMethod;
    }

    /**
     * Send purchase request to payment gateway
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|array
     * @throws \Exception
     */
    public function onPurchase()
    {
        if (empty($this->obElement) || empty($this->obPaymentMethod)) {
            return $this->getErrorResponse();
        }

        //Get gateway payment object
        $obPaymentGateway = $this->obPaymentMethod->gateway;
        if (empty($obPaymentGateway)) {
            return $this->getErrorResponse();
        }

        $obPaymentGateway->purchase($this->obElement);
        if ($obPaymentGateway->isRedirect()) {
            $sRedirectURL = $obPaymentGateway->getRedirectURL();

            return Redirect::to($sRedirectURL);
        } else if ($obPaymentGateway->isSuccessful()) {
            Result::setTrue($obPaymentGateway->getResponse());
        } else {
            Result::setFalse($obPaymentGateway->getResponse());
        }

        return Result::setMessage($obPaymentGateway->getMessage())->get();
    }

    /**
     * Get element object
     * @param string $sElementSlug
     * @return Order
     */
    protected function getElementObject($sElementSlug)
    {
        if (empty($sElementSlug)) {
            return null;
        }

        $this->obUser = UserHelper::instance()->getUser();

        $obElement = Order::getBySecretKey($sElementSlug)->first();
        if (!empty($obElement) && !empty($this->obUser) && $obElement->user_id != $this->obUser->id) {
            $obElement = null;
        }

        return $obElement;
    }

    /**
     * @param int    $iElementID
     * @param Order $obElement
     * @return OrderItem
     */
    protected function makeItem($iElementID, $obElement)
    {
        return OrderItem::make($iElementID, $obElement);
    }
}
