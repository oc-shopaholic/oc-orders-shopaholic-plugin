<?php namespace Lovata\OrdersShopaholic\Classes\PromoMechanism\WithoutCondition;

use Lovata\OrdersShopaholic\Classes\PromoMechanism\InterfacePromoMechanism;
use Lovata\OrdersShopaholic\Classes\PromoMechanism\AbstractPromoMechanism;

/**
 * Class WithoutConditionDiscountPosition
 * @package Lovata\OrdersShopaholic\Classes\PromoMechanism\WithoutCondition
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class WithoutConditionDiscountPosition extends AbstractPromoMechanism implements InterfacePromoMechanism
{
    const LANG_NAME = 'lovata.ordersshopaholic::lang.promo_mechanism_type.without_condition_discount_position';

    protected $bWithQuantityLimit = true;
    protected $bCalculatePerUnit = true;

    /**
     * Get discount type
     * @return string
     */
    public static function getType() : string
    {
        return self::TYPE_POSITION;
    }

    /**
     * Check position is available for discount
     * @param \Lovata\OrdersShopaholic\Classes\PromoMechanism\AbstractPromoMechanismProcessor $obProcessor
     * @param \Lovata\OrdersShopaholic\Classes\Item\CartPositionItem                          $obPosition
     * @return bool
     */
    protected function check($obProcessor, $obPosition = null) : bool
    {
        if (!parent::check($obProcessor, $obPosition)) {
            return false;
        }

        return $this->checkPosition($obPosition);
    }
}