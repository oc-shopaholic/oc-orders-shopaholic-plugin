<?php namespace Lovata\OrdersShopaholic\Controllers;

use Backend\Classes\Controller;
use BackendMenu;
use Flash;
use Illuminate\Http\Request;
use Lang;
use Lovata\OrdersShopaholic\Models\ShippingType;

class ShippingTypes extends Controller
{
    public $implement = [
        'Backend\Behaviors\ListController',
        'Backend\Behaviors\FormController',
        'Backend\Behaviors\ReorderController',
        'PlanetaDelEste.Widgets.Behaviors.ModalController',
    ];

    /** @var Request */
    protected $obRequest;

    public $listConfig = 'config_list.yaml';
    public $formConfig = 'config_form.yaml';
    public $reorderConfig = 'config_reorder.yaml';

    public function __construct(Request $obRequest)
    {
        $this->obRequest = $obRequest;

        parent::__construct();
        BackendMenu::setContext('Lovata.OrdersShopaholic', 'ordersshopaholic-menu', 'ordersshopaholic-menu-shippingtypes');
    }

    /**
     * Ajax element list deleting
     * @return mixed
     */
    public function index_onDelete() {

        $arElementsID = $this->obRequest->input('checked');

        if(empty($arElementsID) || !is_array($arElementsID)) {
            return $this->listRefresh();
        }

        foreach($arElementsID as $iElementID) {
            if(!$obElement = ShippingType::find($iElementID))
                continue;

            $obElement->delete();
        }

        Flash::success(Lang::get('lovata.ordersshopaholic::lang.message.delete_success'));
        return $this->listRefresh();
    }
}