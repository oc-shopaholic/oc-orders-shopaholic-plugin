<?php namespace Lovata\OrdersShopaholic\Classes\Item;

use Lovata\Toolbox\Classes\Item\ElementItem;

use Lovata\Shopaholic\Plugin;
use Lovata\OrdersShopaholic\Models\PaymentMethod;

/**
 * Class PaymentMethodItem
 * @package Lovata\Shopaholic\Classes\Item
 * @author Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 *
 * @property        $id
 * @property string $name
 * @property string $code
 * @property string $preview_text
 */
class PaymentMethodItem extends ElementItem
{
    const CACHE_TAG_ELEMENT = 'order-shopaholic-payment-method-element';

    /** @var PaymentMethod */
    protected $obElement = null;

    /**
     * Set element object
     */
    protected function setElementObject()
    {
        if(!empty($this->obElement) && ! $this->obElement instanceof PaymentMethod) {
            $this->obElement = null;
        }

        if(!empty($this->obElement) || empty($this->iElementID)) {
            return;
        }

        $this->obElement = PaymentMethod::active()->find($this->iElementID);
    }

    /**
     * Get cache tag array for model
     * @return array
     */
    protected static function getCacheTag()
    {
        return [Plugin::CACHE_TAG, self::CACHE_TAG_ELEMENT];
    }

    /**
     * Set element data from model object
     *
     * @return array
     */
    protected function getElementData()
    {
        if(empty($this->obElement)) {
            return null;
        }

        $arResult = [
            'id'            => $this->obElement->id,
            'name'          => $this->obElement->name,
            'code'          => $this->obElement->code,
            'preview_text'  => $this->obElement->preview_text,
        ];

        return $arResult;
    }
}