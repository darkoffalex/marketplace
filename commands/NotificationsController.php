<?php
namespace app\commands;

use app\models\DictionaryNotificationTask;
use app\models\forms\SettingsForm;
use pimax\FbBotApp;
use yii\console\Controller;
use app\models\form\CommonSettingsForm;
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
        //Установка временной зоны PHP
        date_default_timezone_set('UTC');

        //Синхронизировать MySQL с временной зоной PHP
        $now = new \DateTime();
        $min = $now->getOffset() / 60;
        $sgn = ($min < 0 ? -1 : 1);
        $min = abs($min);
        $hrs = floor($min / 60);
        $min -= $hrs * 60;
        $offset = sprintf('%+d:%02d', $hrs*$sgn, $min);
        Yii::$app->db->createCommand("SET time_zone='$offset';")->execute();

        //Вызов родительского метода
        return parent::beforeAction($action);
    }

    /**
     * Send all facebook messenger notifications
     */
    public function actionFbMessengerSend()
    {
        //Сообщение о начале процесса
        $processId = uniqid();
        $startedAt = date('Y-m-d H:i:s', time());
        echo "Process \"{$processId}\" started at {$startedAt}\n\n";

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


        //Сообщение о завершении процесса
        $endedAt = date('Y-m-d H:i:s', time());
        echo "Process \"{$processId}\" ended at {$endedAt}\n\n";
    }
}