<?php
namespace app\commands;

use app\helpers\ConsoleHelper;
use app\helpers\MailSender;
use app\models\DictionaryNotificationTask;
use app\models\forms\SettingsForm;
use app\models\SystemNotification;
use pimax\FbBotApp;
use yii\console\Controller;
use app\components\FacebookBotMessage;
use Yii;


/**
 * Send notifications to messengers, email, etc.
 *
 * @copyright 	2018 Alex Nem
 * @link 		https://github.com/darkoffalex
 * @author 		Alex Nem
 *
 * @package app\commands
 */
class NotificationsController extends Controller
{
    /**
     * Переопределение before-action метода, для смены временной зоны на UTC (фейсбук работает с датами UTC)
     * @param \yii\base\Action $action
     * @return bool
     */
    public function beforeAction($action)
    {
        //Установка врменной зоны и синхронизация её с БД
        ConsoleHelper::setTimezone('UTC');

        //Вызов родительского метода
        return parent::beforeAction($action);
    }

    /**
     * Send all unsent monitoring notifications (to messengers)
     */
    public function actionMonitoringMessengerSend()
    {
        $pid = ConsoleHelper::processStart();

        /* @var $tasks DictionaryNotificationTask[] */
        $tasks = DictionaryNotificationTask::find()->where(['sent' => (int)false])->limit(100)->all();

        if(!empty($tasks)){
            //Получить объект чат-бота для отправки сообщений
            $bot = new FbBotApp(SettingsForm::getInstance()->fb_messenger_page_monitoring_token);

            //Пройти по всем задачам на отправку
            foreach ($tasks as $task)
            {
                try{
                    //Обновить данные задачи, если она вдруг уже обработана (другим экземпляром скрипта)
                    //пропустить итерацию
                    $task->refresh();
                    if($task->sent)continue;

                    //Пометить как "отправлено"
                    $task->sent = (int)true;
                    $task->update();

                    //Отправка сообщения
                    $bot->send(new FacebookBotMessage($task->subscriber->facebook_id,$task->notification->GetMessage(),[
                        [
                            'title' => 'Go to post/comment',
                            'url' => 'https://facebook.com/'.($task->notification->comment_id ? $task->notification->comment->facebook_id : $task->notification->post->facebook_id)
                        ],
                        [
                            'type' => 'postback',
                            'title' => 'Ignore group "'.$task->notification->post->group->name.'"',
                            'command' => 'IGNORE '.$task->notification->dictionary_id.'_'.$task->notification->post->group_id
                        ]
                    ]));

                    echo "Task {$task->id} completed (message sent to {$task->subscriber->facebook_id})\n";

                }
                catch (\Exception $ex){
                    $task->sent = (int)false;
                    $task->update();
                    echo $ex->getMessage();
                    Yii::info($ex->getMessage(),'info');
                }
            }
        }
        else{
            echo "No tasks found\n";
        }


        ConsoleHelper::processEnd($pid);
    }

    /**
     * Send all unsent system notifications (to messengers and emails)
     */
    public function actionSystemMessagesSend()
    {
        $pid = ConsoleHelper::processStart();

        /* @var $tasks SystemNotification[] */
        $tasks = SystemNotification::find()
            ->where(['sent' => (int)false])
            ->orderBy('created_at DESC')
            ->limit(100)
            ->all();

        if(!empty($tasks)){
            //Получить объект чат-бота для отправки сообщений
            $bot = new FbBotApp(SettingsForm::getInstance()->fb_messenger_page_notifications_token);

            //Пройти по всем задачам на отправку
            foreach ($tasks as $task)
            {
                try{
                    //Обновить данные задачи, если она вдруг уже обработана (другим экземпляром скрипта)
                    //пропустить итерацию
                    $task->refresh();
                    if($task->sent)continue;

                    //Пометить как "отправлено"
                    $task->sent = (int)true;
                    $task->update();

                    if(!empty($task->recipient_fb_id)){
                        //Отправка сообщения
                        $bot->send(new FacebookBotMessage($task->recipient_fb_id,$task->message_fb));
                        echo "System notifications {$task->id} sent to {$task->recipient_fb_id})\n";
                    }

                    if(!empty($task->recipient_email)){
                        MailSender::getInstance()->mailer->compose()
                            ->setFrom([SettingsForm::getInstance()->email_for_sending => Yii::$app->name])
                            ->setTo($task->recipient_email)
                            ->setSubject($task->subject_email)
                            ->setHtmlBody($task->message_email)
                            ->send();
                    }
                }
                catch (\Exception $ex){
                    $task->sent = (int)false;
                    $task->update();
                    echo $ex->getMessage();
                    Yii::info($ex->getMessage(),'info');
                }
            }
        }

        ConsoleHelper::processEnd($pid);
    }
}