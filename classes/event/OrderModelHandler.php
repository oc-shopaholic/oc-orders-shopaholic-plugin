<?php namespace Lovata\OrdersShopaholic\Classes\Event;

use Lovata\Toolbox\Classes\Helper\SendMailHelper;

use Lovata\Shopaholic\Models\Settings;
use Lovata\OrdersShopaholic\Models\Order;
use Lovata\OrdersShopaholic\Classes\OrderProcessor;

/**
 * Class OrderModelHandler
 * @package Lovata\OrdersShopaholic\Classes\Event
 * @author Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class OrderModelHandler
{
    /** @var  Order */
    protected $obElement;

    /**
     * Add listeners
     * @param \Illuminate\Events\Dispatcher $obEvent
     */
    public function subscribe($obEvent)
    {
        $obEvent->listen(OrderProcessor::EVENT_ORDER_CREATED, function ($obOrder) {
            $this->obElement = $obOrder;

            $bSendEmail = Settings::getValue('send_email_after_creating_order');
            if (!$bSendEmail) {
                return;
            }

            $this->sendUserEmailAfterCreating();
            $this->sendManagerEmailAfterCreating();
        });
    }

    /**
     * Get model class name
     * @return string
     */
    protected function getModelClass()
    {
        return Order::class;
    }

    /**
     * Send email to user, after order creating
     */
    protected function sendUserEmailAfterCreating()
    {
        //Get user email
        $arOrderProeprtyList = $this->obElement->property;
        if (empty($arOrderProeprtyList) || !isset($arOrderProeprtyList['email']) || empty($arOrderProeprtyList['email'])) {
            return;
        }

        $sEmail = $arOrderProeprtyList['email'];

        //Get mail data
        $arMailData = $this->getDefaultEmailData();

        //Get mail template
        $sMailTemplate = Settings::getValue('creating_order_mail_template', 'lovata.ordersshopaholic::mail.create_order_user');

        $obSendMailHelper = SendMailHelper::instance();
        $obSendMailHelper->send(
            $sMailTemplate,
            $sEmail,
            $arMailData,
            OrderProcessor::EVENT_ORDER_CREATED_USER_MAIL_DATA,
            true);
    }

    /**
     * Send email to manager, after order creating
     */
    protected function sendManagerEmailAfterCreating()
    {
        //Get email list
        $sEmailList = Settings::getValue('creating_order_manager_email_list');
        if (empty($sEmailList)) {
            return;
        }

        //Get mail data
        $arMailData = $this->getDefaultEmailData();

        //Get mail template
        $sMailTemplate = Settings::getValue('creating_order_manager_mail_template', 'lovata.ordersshopaholic::mail.create_order_manager');

        $obSendMailHelper = SendMailHelper::instance();
        $obSendMailHelper->send(
            $sMailTemplate,
            $sEmailList,
            $arMailData,
            OrderProcessor::EVENT_ORDER_CREATED_MANAGER_MAIL_DATA,
            true);
    }

    /**
     * Get default array data for template
     * @return array
     */
    protected function getDefaultEmailData()
    {
        $arResult = [
            'order'        => $this->obElement,
            'order_number' => $this->obElement->order_number,
            'site_url'     => config('app.url'),
        ];

        return $arResult;
    }
}