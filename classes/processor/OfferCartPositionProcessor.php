<?php namespace Lovata\OrdersShopaholic\Classes\Processor;

use Event;

use Lovata\Shopaholic\Models\Offer;
use Lovata\Shopaholic\Classes\Item\OfferItem;

/**
 * Class OfferCartPositionProcessor
 * @package Lovata\OrdersShopaholic\Classes\Processor
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class OfferCartPositionProcessor extends AbstractCartPositionProcessor
{
    const MODEL_CLASS = Offer::class;

    /**
     * Add position to cart
     * @param array $arPositionData
     * @return bool
     */
    public function add($arPositionData)
    {
        $bResult = parent::add($arPositionData);
        if ($bResult) {
            Event::fire('shopaholic.cart.add', $this->arPositionData['item_id']);
        }

        return $bResult;
    }

    /**
     * Validate position data, after add/update actions
     * @return bool
     */
    protected function validatePositionData()
    {
        if (empty($this->arPositionData) || !is_array($this->arPositionData)) {
            return false;
        }

        $iPositionID = array_get($this->arPositionData, 'id');
        $iItemID = array_get($this->arPositionData, 'item_id');
        if((empty($iPositionID) && empty($iItemID)) || empty($this->arPositionData['quantity']) || $this->arPositionData['quantity'] < 1) {
            return false;
        }

        if (!empty($iPositionID) && empty($iItemID)) {
            return true;
        }

        //Get offer item
        $obOfferItem = OfferItem::make($iItemID);
        if ($obOfferItem->isEmpty() || $obOfferItem->product->isEmpty()) {
            return false;
        }

        return true;
    }

    /**
     * Prepare position data to save
     */
    protected function preparePositionData()
    {
        if (!isset($this->arPositionData['item_id'])) {
            $this->arPositionData['item_id'] = array_get($this->arPositionData, 'offer_id');
            array_forget($this->arPositionData, 'offer_id');
        }

        parent::preparePositionData();
    }
}
