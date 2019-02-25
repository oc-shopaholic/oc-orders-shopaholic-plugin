<?php namespace Lovata\OrdersShopaholic\Classes\Processor;

use DB;
use Lang;
use Kharanenka\Helper\Result;
use Lovata\Shopaholic\Models\Offer;
use Lovata\Shopaholic\Models\Settings;

/**
 * Class AbstractOrderPositionProcessor
 * @package Lovata\OrdersShopaholic\Classes\Processor
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class OfferOrderPositionProcessor extends AbstractOrderPositionProcessor
{
    //Settings
    protected $bCheckOfferQuantity = false;
    protected $bDecrementOfferQuantity = false;

    /** @var \Lovata\Shopaholic\Classes\Item\OfferItem $obOfferItem */
    protected $obOfferItem;

    /** @var \Lovata\Shopaholic\Models\Offer $obOffer */
    protected $obOffer;

    /**
     * Validate cart position
     * @return bool
     */
    public function validate()
    {
        //Get offer item and check "active" flag
        $bResult = $this->obOfferItem->isActive() && $this->obOfferItem->product->isActive();

        return $bResult;
    }

    /**
     * Check availability cart position
     * @return bool
     */
    public function check()
    {
        //Get offer object
        $this->obOffer = $this->obOfferItem->getObject();
        if (empty($this->obOffer)) {
            return false;
        }

        $bResult = $this->checkOfferQuantity() && $this->decrementOfferQuantity();

        return $bResult;
    }

    /**
     * Get cart position data
     * @return array
     */
    public function getData()
    {
        $arResult = [
            'item_id'     => $this->obOffer->id,
            'item_type'   => Offer::class,
            'quantity'    => $this->obCartPosition->quantity,
            'property'    => $this->obCartPosition->property,
            'tax_percent' => $this->obCartPosition->tax_percent,
        ];

        return $arResult;
    }

    /**
     * Init method
     */
    protected function init()
    {
        // Get order behavior flags from settings
        $this->bCheckOfferQuantity = (bool) Settings::getValue('check_offer_quantity');
        $this->bDecrementOfferQuantity = (bool) Settings::getValue('decrement_offer_quantity');

        $this->obOfferItem = $this->obCartPosition->item;
    }

    /**
     * Check offer quantity
     * @return bool
     */
    protected function checkOfferQuantity()
    {
        //Check quantity
        if (!$this->bCheckOfferQuantity || $this->obOffer->quantity >= $this->obCartPosition->quantity) {
            return true;
        }

        $sMessage = Lang::get('lovata.ordersshopaholic::lang.message.insufficient_amount');
        Result::setFalse(['cart_position_id' => $this->obCartPosition->id])->setMessage($sMessage);

        return false;
    }

    /**
     * Decrement offer quantity
     * @return bool
     */
    protected function decrementOfferQuantity()
    {
        //Decrement offer quantity
        if (!$this->bDecrementOfferQuantity) {
            return true;
        }

        try {
            $this->obOffer->quantity -= $this->obCartPosition->quantity;
            $this->obOffer->save();
        } catch (\Exception $obException) {

            $sMessage = Lang::get('lovata.ordersshopaholic::lang.message.insufficient_amount');
            Result::setFalse(['cart_position_id' => $this->obCartPosition->id])->setMessage($sMessage);
            return false;
        }

        return true;
    }
}
