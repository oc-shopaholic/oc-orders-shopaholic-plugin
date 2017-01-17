<?php namespace Lovata\OrdersShopaholic\Controllers;

use Backend\Classes\Controller;
use BackendMenu;
use Lang;
use Flash;
use Illuminate\Http\Request;
use Lovata\OrdersShopaholic\Models\PaymentMethod;

class PaymentMethods extends Controller
{
    public $implement = [
        'Backend\Behaviors\ListController',
        'Backend\Behaviors\FormController',
        'Backend\Behaviors\ReorderController',
        'PlanetaDelEste.Widgets.Behaviors.ModalController',
    ];
    
    public $listConfig = 'config_list.yaml';
    public $formConfig = 'config_form.yaml';
    public $reorderConfig = 'config_reorder.yaml';

    /** @var Request */
    protected $obRequest;
    
    public function __construct(Request $obRequest)
    {
        $this->obRequest = $obRequest;

        parent::__construct();
        BackendMenu::setContext('Lovata.OrdersShopaholic', 'ordersshopaholic-menu', 'ordersshopaholic-menu-paymentmethods');
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
            if(!$obElement = PaymentMethod::find($iElementID))
                continue;

            $obElement->delete();
        }

        Flash::success(Lang::get('lovata.ordersshopaholic::lang.message.delete_success'));
        return $this->listRefresh();
    }
}