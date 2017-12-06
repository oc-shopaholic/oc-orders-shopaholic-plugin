<?php namespace Lovata\OrdersShopaholic\Components;

use Cms\Classes\ComponentBase;
use Lovata\Toolbox\Traits\Helpers\TraitComponentNotFoundResponse;

use Lovata\OrdersShopaholic\Models\Order;

/**
 * Class OrderPage
 * @package Lovata\OrdersShopaholic\Components
 * @author Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 *
 * @link https://github.com/lovata/oc-shopaholic-plugin/wiki/OrderPage
 */
class OrderPage extends ComponentBase
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
     * Get element object
     * @return \Illuminate\Http\Response|null
     */
    public function onRun()
    {
        //Get element slug
        $sElementSlug = $this->property('slug');
        if (empty($sElementSlug) && !$this->property('slug_required')) {
            return null;
        }

        if (empty($this->obElement)) {
            return $this->getErrorResponse();
        }

        return null;
    }

    /**
     * Init plugin method
     */
    public function init()
    {
        //Get element slug
        $sElementSlug = $this->property('slug');
        if (empty($sElementSlug)) {
            return;
        }

        //Get element by slug
        $obElement = $this->getElementObject($sElementSlug);
        if (empty($obElement)) {
            return;
        }

        $this->obElement = $obElement;

        $this->obPaymentMethod = $obElement->payment_method;
    }

    /**
     * Get Order object
     * @return Order
     */
    public function get()
    {
        return $this->obElement;
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
}
