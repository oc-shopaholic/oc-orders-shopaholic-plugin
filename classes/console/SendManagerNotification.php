<?php namespace Lovata\OrdersShopaholic\Classes\Console;

use Lovata\Toolbox\Classes\Helper\SendMailHelper;
use October\Rain\Argon\Argon;
use Illuminate\Console\Command;
use System\Models\MailTemplate;

use Lovata\OrdersShopaholic\Models\Task;

/**
 * Class SendManagerNotification
 * @package Lovata\BaseCode\Classes\Console
 * @author  Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
class SendManagerNotification extends Command
{
    /**
     * @var string
     */
    protected $name = 'shopaholic:order.send_manager_notification';

    /**
     * @var string
     */
    protected $description = 'Receiving active tasks and sending a notification to the manager';

    /**
     * Command handler
     */
    public function handle()
    {
        $obDateNow = Argon::now();

        //Get active task list
        $obTaskList = Task::where('sent', false)
            ->getActiveTask()
            ->where('date', '<=', $obDateNow->toDateTimeString())
            ->whereNotNull('mail_template')
            ->get();
        if ($obTaskList->isEmpty()) {
            return;
        }

        foreach ($obTaskList as $obTask) {
            $this->sendMailNotification($obTask);
            $obTask->sent = true;
            $obTask->save();
        }
    }

    /**
     * Send mail notifications to manager
     * @param Task $obTask
     */
    protected function sendMailNotification($obTask)
    {
        //Get mail template
        $sMailTemplateCode = $obTask->mail_template;
        if (empty($sMailTemplateCode) || empty(MailTemplate::findOrMakeTemplate($sMailTemplateCode))) {
            return;
        }

        //Get manager object
        $obManager = $obTask->manager;
        if (empty($obManager) || empty($obManager->email)) {
            return;
        }

        $arEmailData = [
            'task' => $obTask,
        ];

        SendMailHelper::instance()->send($sMailTemplateCode, $obManager->email, $arEmailData, Task::EVENT_EXTEND_EMAIL_NOTIFICATION_DATA);
    }
}