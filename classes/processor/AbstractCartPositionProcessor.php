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
        $this->preparePositionData();
        if (!$this->validatePositionData()) {
            return false;
        }

        //Find position in current cart
        $this->findPosition();

        try {
            if (!empty($this->obCartPosition)) {
                $this->obCartPosition->update($this->arPositionData);
                if ($this->obCartPosition->trashed()) {
                    $this->obCartPosition->restore();
                }
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
        $this->preparePositionData();
        if (!$this->validatePositionData()) {
            return false;
        }

        $this->findPosition();
        if (empty($this->obCartPosition)) {
            return false;
        }

        try {
            $this->obCartPosition->update($this->arPositionData);
            if ($this->obCartPosition->trashed()) {
                $this->obCartPosition->restore();
            }
        } catch (\October\Rain\Database\ModelException $obException) {
            $this->processValidationError($obException);
            return false;
        }

        return true;
    }

    /**
     * Remove position from current cart
     * @param int    $iPositionID
     * @param string $sType
     * @return void
     * @throws \Exception
     */
    public function remove($iPositionID, $sType = 'offer')
    {
        if (empty($iPositionID)) {
            return;
        }

        if ($sType == 'position') {
            $this->arPositionData = [
                'id'        => $iPositionID,
                'item_type' => static::MODEL_CLASS,
            ];
        } else {
            $this->arPositionData = [
                'item_id'   => $iPositionID,
                'item_type' => static::MODEL_CLASS,
            ];
        }

        $this->findPosition();
        if (empty($this->obCartPosition)) {
            return;
        }

        $this->obCartPosition->delete();
    }

    /**
     * Remove position from current cart
     * @param int $iPositionID
     * @return void
     * @throws \Exception
     */
    public function restore($iPositionID)
    {
        if (empty($iPositionID)) {
            return;
        }

        $this->arPositionData = [
            'id'        => $iPositionID,
            'item_type' => static::MODEL_CLASS,
        ];

        $this->findPosition();
        if (empty($this->obCartPosition)) {
            return;
        }

        $this->obCartPosition->restore();
    }

    /**
     * Get position object
     * @return CartPosition
     */
    public function getPositionObject()
    {
        return $this->obCartPosition;
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
        if (array_key_exists('item_id', $this->arPositionData) && empty($this->arPositionData['item_id']) && !empty($this->arPositionData['id'])) {
            unset($this->arPositionData['item_id']);
        }
    }

    /**
     * Find position in current cart
     */
    protected function findPosition()
    {
        $iPositionID = array_get($this->arPositionData, 'id');

        $iItemID = array_get($this->arPositionData, 'item_id');
        $sItemType = array_get($this->arPositionData, 'item_type');

        if (!empty($iPositionID)) {
            $this->obCartPosition = CartPosition::withTrashed()->getByCart($this->obCart->id)->getByItemType($sItemType)->find($iPositionID);

            return;
        }

        //Get item property
        $arItemProperty = (array) array_get($this->arPositionData, 'property');

        //Get cart position list by item_id and item_type
        $obCartPositionList = CartPosition::withTrashed()->getByCart($this->obCart->id)->getByItemType($sItemType)->getByItemID($iItemID)->get();
        if ($obCartPositionList->isEmpty()) {
            return;
        }

        /** @var CartPosition $obCartPosition */
        foreach ($obCartPositionList as $obCartPosition) {
            $arCartPositionProperty = (array) $obCartPosition->property;
            $bCheck = (empty($arItemProperty) && empty($arCartPositionProperty))
                || (!array_diff($arItemProperty, $arCartPositionProperty) && !array_diff($arCartPositionProperty, $arItemProperty));
            if ($bCheck) {
                $this->obCartPosition = $obCartPosition;
                break;
            }
        }
    }
}
