<?php namespace Lovata\OrdersShopaholic\Controllers;

use Backend\Classes\FormField;
use Backend\FormWidgets\DatePicker;
use BackendMenu;
use Backend\Classes\Controller;
use Lovata\OrdersShopaholic\Models\OrderPosition;

/**
 * Class OrderPositions
 * @package Lovata\OrdersShopaholic\Controllers
 * @author Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class OrderPositions extends Controller
{
    public $implement = [
        'Backend.Behaviors.FormController',
        'Backend.Behaviors.ImportExportController',
    ];

    public $formConfig = 'config_form.yaml';
    public $importExportConfig = 'config_import_export.yaml';

    /**
     * OrderPositions constructor.
     */
    public function __construct()
    {
        parent::__construct();
        BackendMenu::setContext('Lovata.OrdersShopaholic', 'orders-shopaholic-menu', 'orders-shopaholic-menu-orders');
    }

    /**
     * Render start date.
     * @return bool|mixed|string
     */
    public function renderStartDate()
    {
        $obFormField = new FormField('start_date', 'start_date');

        $obDatePicker = new DatePicker($this, $obFormField);
        $obDatePicker->mode = 'date';
        $obDatePicker->format = 'Y-m-d';
        $obDatePicker->model = new OrderPosition();

        return $obDatePicker->render();
    }

    /**
     * Render end date.
     * @return bool|mixed|string
     */
    public function renderEndDate()
    {
        $obFormField = new FormField('end_date', 'end_date');

        $obDatePicker = new DatePicker($this, $obFormField);
        $obDatePicker->mode = 'date';
        $obDatePicker->format = 'Y-m-d';
        $obDatePicker->model = new OrderPosition();

        return $obDatePicker->render();
    }
}
