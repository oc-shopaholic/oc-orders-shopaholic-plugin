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

        if(empty($this->arPositionData['offer_id']) || empty($this->arPositionData['quantity']) || $this->arPositionData['quantity'] < 1) {
            return false;
        }

        //Get offer item
        $obOfferItem = OfferItem::make($this->arPositionData['offer_id']);
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
        $this->arPositionData['item_id'] = $this->arPositionData['offer_id'];
        unset($this->arPositionData['offer_id']);

        parent::preparePositionData();
    }
}
