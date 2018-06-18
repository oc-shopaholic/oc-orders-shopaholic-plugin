<?php namespace Lovata\OrdersShopaholic\Classes\Processor;

use Lovata\Toolbox\Traits\Helpers\TraitValidationHelper;

use Lovata\OrdersShopaholic\Models\CartPosition;

/**
 * Class AbstractCartPositionProcessor
 * @package Lovata\OrdersShopaholic\Classes\Processor
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
abstract class AbstractCartPositionProcessor
{
    use TraitValidationHelper;

    const MODEL_CLASS = \Model::class;

    /** @var \Lovata\OrdersShopaholic\Models\Cart */
    protected $obCart = null;

    /** @var \Lovata\Buddies\Models\User */
    protected $obUser = null;

    /** @var array */
    protected $arPositionData;

    /** @var CartPosition */
    protected $obCartPosition;

    /**
     * AbstractCartPositionProcessor constructor.
     * @param \Lovata\OrdersShopaholic\Models\Cart $obCart
     * @param \Lovata\Buddies\Models\User          $obUser
     */
    public function __construct($obCart, $obUser)
    {
        $this->obCart = $obCart;
        $this->obUser = $obUser;
    }

    /**
     * Add position to cart
     * @param array $arPositionData
     * @return bool
     */
    public function add($arPositionData)
    {
        $this->arPositionData = $arPositionData;
        if (!$this->validatePositionData()) {
            return false;
        }

        $this->preparePositionData();

        //Find position in current cart
        $this->findPosition();

        try {
            if (!empty($this->obCartPosition)) {
                $this->obCartPosition->update($this->arPositionData);
            } else {
                $this->obCartPosition = CartPosition::create($this->arPositionData);
            }
        } catch (\October\Rain\Database\ModelException $obException) {
            $this->processValidationError($obException);
            return false;
        }

        return true;
    }

    /**
     * Update position data in current cart
     * @param array $arPositionData
     * @return bool
     */
    public function update($arPositionData)
    {
        $this->arPositionData = $arPositionData;
        if (!$this->validatePositionData()) {
            return false;
        }

        $this->preparePositionData();
        $this->findPosition();
        if (empty($this->obCartPosition)) {
            return false;
        }

        try {
            $this->obCartPosition->update($this->arPositionData);
        } catch (\October\Rain\Database\ModelException $obException) {
            $this->processValidationError($obException);
            return false;
        }

        return true;
    }

    /**
     * Remove position from current cart
     * @param int $iPositionID
     * @return void
     * @throws \Exception
     */
    public function remove($iPositionID)
    {
        if (empty($iPositionID)) {
            return;
        }

        $this->arPositionData = [
            'item_id'   => $iPositionID,
            'item_type' => static::MODEL_CLASS,
        ];

        $this->findPosition();
        if (empty($this->obCartPosition)) {
            return;
        }

        $this->obCartPosition->delete();
    }

    /**
     * Validate position data, after add/update actions
     * @return bool
     */
    abstract protected function validatePositionData();

    /**
     * Prepare position data to save
     */
    protected function preparePositionData()
    {
        $this->arPositionData['item_type'] = static::MODEL_CLASS;
        $this->arPositionData['cart_id'] = $this->obCart->id;
    }

    /**
     * Find position in current cart
     */
    protected function findPosition()
    {
        $this->obCartPosition = CartPosition::getByCart($this->obCart->id)
            ->getByItemType($this->arPositionData['item_type'])
            ->getByItemID($this->arPositionData['item_id'])
            ->first();
    }
}
