<?php namespace Lovata\OrdersShopaholic\Components;

use Lovata\OrdersShopaholic\Classes\Item\OrderItem;
use Lovata\Toolbox\Classes\Component\ElementPage;
use Lovata\Toolbox\Traits\Helpers\TraitComponentNotFoundResponse;

use Lovata\OrdersShopaholic\Models\Order;

/**
 * Class OrderPage
 * @package Lovata\OrdersShopaholic\Components
 * @author Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 *
 * @link https://github.com/lovata/oc-shopaholic-plugin/wiki/OrderPage
 */
class OrderPage extends ElementPage
{
    use TraitComponentNotFoundResponse;

    /** @var Order */
    public $obElement = null;

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
     * Get element object
     * @param string $sElementSlug
     * @return Order
     */
    protected function getElementObject($sElementSlug)
    {
        if (empty($sElementSlug)) {
            return null;
        }

        $obElement = Order::getBySecretKey($sElementSlug)->first();

        return $obElement;
    }

    /**
     * @param int    $iElementID
     * @param Order $obElement
     * @return OrderItem
     */
    protected function makeItem($iElementID, $obElement)
    {
        return OrderPage::makeItem($iElementID, $obElement);
    }
}
