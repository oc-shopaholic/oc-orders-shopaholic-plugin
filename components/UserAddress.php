<?php namespace Lovata\OrdersShopaholic\Components;

use Lang;
use Input;
use Cms\Classes\ComponentBase;
use Kharanenka\Helper\Result;

use Lovata\Toolbox\Traits\Helpers\TraitValidationHelper;
use Lovata\Toolbox\Classes\Helper\UserHelper;

use Lovata\OrdersShopaholic\Models\UserAddress as UserAddressModel;

/**
 * Class UserAddress
 * @package Lovata\OrderShopaholic\Components
 * @author  Sergey Zakharevich, s.zakharevich@lovata.com, LOVATA Group
 */
class UserAddress extends ComponentBase
{
    use TraitValidationHelper;

    /**
     * @return array
     */
    public function componentDetails()
    {
        return [
            'name'        => 'lovata.ordersshopaholic::lang.component.user_address_name',
            'description' => 'lovata.ordersshopaholic::lang.component.user_address_description',
        ];
    }

    /**
     * Update user address list
     * @return array
     * @throws \Exception
     */
    public function onUpdateList()
    {
        $arAddressList = Input::all();

        /** @var \Lovata\Buddies\Models\User $obUser */
        $obUser = UserHelper::instance()->getUser();
        if (empty($obUser) || empty($arAddressList)) {
            $sMessage = Lang::get('lovata.toolbox::lang.message.e_not_correct_request');
            return Result::setFalse()->setMessage($sMessage)->get();
        }

        $obAddressList = $obUser->address;

        //Process address list
        foreach ($obAddressList as $obAddress) {
            $arAddressData = null;

            //Find address data by ID
            foreach ($arAddressList as $sKey => $arData) {
                if ($obAddress->id != array_get($arData, 'id')) {
                    continue;
                }

                $arAddressData = $arData;
                unset($arAddressList[$sKey]);

                break;
            }

            //Update address data or remove old data
            if (!empty($arAddressData)) {
                $this->updateAddressObject($obAddress, $arAddressData);
            } else {
                $obAddress->delete();
            }
        }

        if (empty($arAddressList)) {
            return Result::get();
        }

        //Create new addresses
        foreach ($arAddressList as $arAddressData) {
            $this->createAddressObject($arAddressData);
        }

        return Result::get();
    }

    /**
     * Add address for user
     * @return array
     */
    public function onAdd()
    {
        $arAddressData = Input::all();

        $iUserID = UserHelper::instance()->getUserId();
        if (empty($arAddressData) || empty($iUserID)) {
            $sMessage = Lang::get('lovata.toolbox::lang.message.e_not_correct_request');
            return Result::setFalse()->setMessage($sMessage)->get();
        }

        $obAddress = UserAddressModel::findAddressByData($arAddressData, $iUserID);
        if (!empty($obAddress)) {
            $sMessage = Lang::get('lovata.ordersshopaholic::lang.message.e_address_exists');
            return Result::setFalse()->setMessage($sMessage)->get();
        }

        $arAddressData['user_id'] = $iUserID;
        $this->createAddressObject($arAddressData);

        return Result::get();
    }

    /**
     * Update address for user
     * @return array
     */
    public function onUpdate()
    {
        $arAddressData = Input::all();
        $iAddressID = array_get($arAddressData, 'id');

        $iUserID = UserHelper::instance()->getUserId();
        $obAddress = UserAddressModel::findAddressByData($arAddressData, $iUserID);

        if (empty($arAddressData) || empty($iAddressID) || empty($iUserID)) {
            $sMessage = Lang::get('lovata.toolbox::lang.message.e_not_correct_request');
            return Result::setFalse()->setMessage($sMessage)->get();
        }

        if (!empty($obAddress) && $obAddress->id != $iAddressID) {
            $sMessage = Lang::get('lovata.ordersshopaholic::lang.message.e_address_exists');
            return Result::setFalse()->setMessage($sMessage)->get();
        }

        //Find address object by ID
        $obAddress = UserAddressModel::getByUser($iUserID)->find($arAddressData['id']);
        if (empty($obAddress)) {
            $sMessage = Lang::get('lovata.toolbox::lang.message.e_not_correct_request');
            return Result::setFalse()->setMessage($sMessage)->get();
        }

        $this->updateAddressObject($obAddress, $arAddressData);

        return Result::get();
    }

    /**
     * Remove address for user by id address
     * @throws \Exception
     */
    public function onRemove()
    {
        $arAddressIDList = Input::get('id');
        $iUserID = UserHelper::instance()->getUserId();
        if (empty($arAddressIDList) || empty($iUserID)) {
            $sMessage = Lang::get('lovata.toolbox::lang.message.e_not_correct_request');
            return Result::setFalse()->setMessage($sMessage)->get();
        }

        if (!is_array($arAddressIDList)) {
            $arAddressIDList = [$arAddressIDList];
        }

        foreach ($arAddressIDList as $iAddressID) {
            //Find address object by ID
            $obAddress = UserAddressModel::getByUser($iUserID)->find($iAddressID);
            if (empty($obAddress)) {
                continue;
            }

            $obAddress->delete();
        }

        return Result::get();
    }

    /**
     * Remove all address for user
     * @throws \Exception
     */
    public function onClear()
    {
        $obUser = UserHelper::instance()->getUser();
        if (empty($obUser)) {
            $sMessage = Lang::get('lovata.toolbox::lang.message.e_not_correct_request');
            return Result::setFalse()->setMessage($sMessage)->get();
        }

        $obAddressList = $obUser->address;
        foreach ($obAddressList as $obAddress) {
            $obAddress->delete();
        }

        return Result::get();
    }

    /**
     * Update address object
     * @param UserAddressModel $obAddress
     * @param array            $arAddressData
     */
    public function updateAddressObject($obAddress, $arAddressData)
    {
        if (empty($obAddress) || empty($arAddressData)) {
            return;
        }

        try {
            $obAddress->update($arAddressData);
        } catch (\October\Rain\Database\ModelException $obException) {
            $this->processValidationError($obException);
        }
    }

    /**
     * Create address object
     * @param array $arAddressData
     */
    public function createAddressObject($arAddressData)
    {
        if (empty($arAddressData)) {
            return;
        }

        $arResponse = ['id' => null];

        try {
            $obAddress = UserAddressModel::create($arAddressData);
        } catch (\October\Rain\Database\ModelException $obException) {
            $this->processValidationError($obException);
        }

        if (!empty($obAddress)) {
            $arResponse['id'] = $obAddress->id;
        }

        Result::setData($arResponse);
    }
}
