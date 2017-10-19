<?php namespace Lovata\OrdersShopaholic\Classes;

use DB;
use Lang;
use Event;
use Kharanenka\Helper\Result;

use Lovata\Shopaholic\Models\Settings;
use Lovata\OrdersShopaholic\Models\Order;
use Lovata\OrdersShopaholic\Models\Status;
use Lovata\Shopaholic\Classes\Item\OfferItem;

/**
 * Class OrderProcessor
 * @package Lovata\OrdersShopaholic\Classes
 * @author Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class OrderProcessor
{
    /** @var \Lovata\Buddies\Models\User */
    protected $obUser;
    
    /** @var CartProcessor */
    protected $obCartProcessor;
    
    //Settings
    protected $bCheckOfferQuantity = false;
    protected $bDecrementOfferQuantity = false;

    /**
     * OrderProcessor constructor.
     */
    public function __construct()
    {
        $this->obCartProcessor = CartProcessor::instance();
        
        // Get order behavior flags from settings
        $this->bCheckOfferQuantity = Settings::getValue('check_offer_quantity');
        $this->bDecrementOfferQuantity = Settings::getValue('decrement_offer_quantity');
    }

    /**
     * Create new order
     * @param $arOrderData
     * @param \Lovata\Buddies\Models\User $obUser
     * @return Order|null
     */
    public function create($arOrderData, $obUser = null)
    {
        //Get cart element list
        $obCartElementList = $this->obCartProcessor->get();
        if($obCartElementList->isEmpty()) {

            $sMessage = Lang::get('lovata.ordersshopaholic::lang.message.empty_cart');
            Result::setMessage($sMessage);
            return null;
        }

        if(empty($arOrderData)) {
            $arOrderData = [];
        }

        $this->obUser = $obUser;
        if(!empty($this->obUser)) {
            $arOrderData['user_id'] = $this->obUser->id;
        }

        $obStatus = Status::getByCode(Status::STATUS_NEW)->first();
        if(!empty($obStatus)) {
            $arOrderData['status_id'] = $obStatus->id;
        }
        
        //Begin transaction
        DB::beginTransaction();

        $obOrder = Order::create($arOrderData);

        $bOrderHasOffers = false;
        /** @var \Lovata\OrdersShopaholic\Classes\Item\CartElementItem $obCartElementItem */
        foreach($obCartElementList as $obCartElementItem) {
            
            //Get offer item and check "active"
            $obOfferItem = OfferItem::make($obCartElementItem->offer_id);
            if($obOfferItem->isEmpty() || $obOfferItem->product->isEmpty()) {
                continue;
            }

            //Get offer object
            /** @var \Lovata\Shopaholic\Models\Offer $obOffer */
            $obOffer = $obOfferItem->getObject();

            //Check quantity
            if($this->bCheckOfferQuantity && $obOffer->quantity < $obCartElementItem->quantity) {
                
                $sMessage = Lang::get('lovata.ordersshopaholic::lang.message.insufficient_amount');
                Result::setFalse(['offer_id' => $obCartElementItem->offer_id])->setMessage($sMessage);
                break;
            }

            //Decrement offer quantity
            if($this->bDecrementOfferQuantity) {
                try {
                    DB::table('lovata_shopaholic_offers')
                        ->where('id', $obOffer->id)
                        ->decrement('quantity', $obCartElementItem->quantity);
                } catch (\Exception $e) {

                    $sMessage = Lang::get('lovata.ordersshopaholic::lang.message.insufficient_amount');
                    Result::setFalse(['offer_id' => $obCartElementItem->offer_id])->setMessage($sMessage);
                    break;
                }
            }
            
            $arPivotData = [
                'price'     => $obOffer->getPriceValue(),
                'old_price' => $obOffer->getOldPriceValue(),
                'quantity'  => $obCartElementItem->quantity,
                'code'      => $obOffer->code,
            ];
            
            //Attach offer to order
            $obOrder->offer()->attach($obOffer->id, $arPivotData);
            $bOrderHasOffers = true;
        }
        
        if(!Result::status() || !$bOrderHasOffers) {
            DB::rollBack();

            if(!$bOrderHasOffers) {
                $sMessage = Lang::get('lovata.ordersshopaholic::lang.message.empty_cart');
                Result::setMessage($sMessage);
            }

            return null;
        }

        DB::commit();
        $obOrder->save();

        Event::fire('shopaholic.order.created', $obOrder);
        
        $this->obCartProcessor->clear();
        
        $arResult = [
            'id'     => $obOrder->id,
            'number' => $obOrder->order_number,
        ];
        
        Result::setTrue($arResult);
        return $obOrder;
    }
}
