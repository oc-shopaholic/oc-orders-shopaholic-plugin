<?php namespace Lovata\OrdersShopaholic\Classes\Collection;

use Lovata\Toolbox\Classes\Collection\ElementCollection;

use Lovata\OrdersShopaholic\Classes\Item\PaymentMethodItem;
use Lovata\OrdersShopaholic\Classes\Store\PaymentMethodListStore;

/**
 * Class PaymentMethodCollection
 * @package Lovata\Shopaholic\Classes\Collection
 * @author Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class PaymentMethodCollection extends ElementCollection
{
    /** @var PaymentMethodListStore */
    protected $obPaymentMethodListStore;

    /**
     * PaymentMethodCollection constructor.
     * @param PaymentMethodListStore $obPaymentMethodListStore
     */
    public function __construct(PaymentMethodListStore $obPaymentMethodListStore)
    {
        $this->obPaymentMethodListStore = $obPaymentMethodListStore;
        parent::__construct();
    }
    
    /**
     * Make element item
     * @param int   $iElementID
     * @param \Lovata\OrdersShopaholic\Models\PaymentMethod  $obElement
     *
     * @return PaymentMethodItem
     */
    protected function makeItem($iElementID, $obElement = null)
    {
        return PaymentMethodItem::make($iElementID, $obElement);
    }

    /**
     * Sort list
     * @return $this
     */
    public function sort()
    {
        if(!$this->isClear() && $this->isEmpty()) {
            return $this->returnThis();
        }

        //Get sorting list
        $arElementIDList = $this->obPaymentMethodListStore->getBySorting();
        if(empty($arElementIDList)) {
            return $this->clear();
        }

        if($this->isClear()) {
            $this->arElementIDList = $arElementIDList;
            return $this->returnThis();
        }

        $this->arElementIDList = array_intersect($arElementIDList, $this->arElementIDList);
        return $this->returnThis();
    }
    
    /**
     * Apply filter by active product list0
     * @return $this
     */
    public function active()
    {
        $arElementIDList = $this->obPaymentMethodListStore->getActiveList();
        return $this->intersect($arElementIDList);
    }
}