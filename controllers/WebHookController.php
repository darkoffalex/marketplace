<?php

namespace app\controllers;

use app\components\Controller;
use app\components\FacebookBotMessage;
use app\models\Dictionary;
use app\models\DictionarySubscriber;
use app\models\forms\SettingsForm;
use Facebook\Facebook;
use yii\helpers\ArrayHelper;
use pimax\FbBotApp;
use Yii;

/**
 * Контроллер отвечающий за веб-хук (обработку событий сторонних сервисов). Используется для реализации чат-ботов
 * и прочих аналогичных систем
 *
 * @copyright    2018 Alex Nem
 * @link        https://github.com/darkoffalex
 * @author        Alex Nem
 *
 * @package app\controllers
 */
class WebHookController extends Controller
{
    /**
     * Основной хук страницы. Обработка сообщений странице, и прочих событий (напр. лайки, комменты и тд)
     * @return null
     */
    public function actionFbPageHook()
    {
        //Ключ проверки при первой настройке
        $verify_token = CommonSettingsForm::getInstance()->fb_messenger_hook_verify_token;

        //Если это проверка во время настройки - вернуть ответ
        if (!empty($_REQUEST['hub_mode']) && $_REQUEST['hub_mode'] == 'subscribe' && $_REQUEST['hub_verify_token'] == $verify_token) {
            return $_REQUEST['hub_challenge'];
        } //Если это уже реальные запросы
        else {
            //Получить запрос и распарсить его
            $dataJson = file_get_contents("php://input");
            $data = json_decode($dataJson, true);

            //ID страницы с которой отправлено сообщение)
            $pageId = ArrayHelper::getValue($data, 'entry.0.id');

            //Если это обращение к основной странице приложения
            //работет основной сценарий (обращение к базовым функциям бота)
            if (!empty($pageId) && $pageId == CommonSettingsForm::getInstance()->fb_messenger_valid_page_id) {
                $this->handleRegularMessages($data);
            }
        }

        return null;
    }

    /**
     * Обаботка прямых сообщений странице
     * @param $data array
     */
    private function handleRegularMessages($data)
    {
        if (!empty($data['entry'][0]['messaging'])) {
            foreach ($data['entry'][0]['messaging'] as $item) {

                // Т Е К С Т О В Ы Е  К О М А Н Д Ы
                if (!empty($item['message']) && empty($item['message']['is_echo'])) {

                    //Отправитель (ID в контексте страницы)
                    $senderId = $item['sender']['id'];

                    //Данные пользователя
                    $usersData = [];

                    //Попытка получить данные пользователя
                    try {
                        $fbNew = new Facebook([
                            'app_id' => CommonSettingsForm::getInstance()->fb_messenger_client_id,
                            'app_secret' => CommonSettingsForm::getInstance()->fb_messenger_app_secret,
                        ]);

                        $usersData = $fbNew->get("/{$senderId}?fields=name,timezone,gender,profile_pic,third_party_id", CommonSettingsForm::getInstance()->fb_messenger_page_token)
                            ->getGraphUser()
                            ->asArray();
                    }
                    catch (\Exception $ex) {
                        Yii::info($ex->getMessage(), 'info');
                    }

                    //Отправленный текст
                    $command = $item['message']['text'];

                    //Если что-то отправлено (текст не пуст)
                    if (!empty($command)) {
                        //Параметры отправителя
                        $senderName = ArrayHelper::getValue($usersData, 'name');
                        $avatarUrl = ArrayHelper::getValue($usersData, 'profile_pic');
                        //$tpID = ArrayHelper::getValue($usersData, 'third_party_id');

                        //Ответ по умолчанию
                        $response = "Hello i am hype.today chat-bot\n";
                        $response .= "Available commands : \n\n";
                        $response .= "DICT {Dictionary key} - Subscribe/un-subscribe to notifications of dictionary\n\n";

                        //Аргумент (параметр) передаваемый в команде
                        $argument = null;

                        //Подписка на обычные уведомления
                        if ($this->isCommand($command, 'DICT', $argument)) {
                            /* @var $dictionary Dictionary */
                            $dictionary = Dictionary::find()->where(['key' => $argument])->one();
                            if(!empty($dictionary)){
                                /* @var $subscriber DictionarySubscriber */
                                $subscriber = $dictionary->getDictionarySubscribers()->andWhere(['facebook_id' => $senderId])->one();

                                if(empty($subscriber)){
                                    $subscriber = new DictionarySubscriber();
                                    $subscriber -> facebook_id = $senderId;
                                    $subscriber -> dictionary_id = $dictionary->id;
                                    $subscriber -> name = $senderName;
                                    $subscriber -> avatar_url = $avatarUrl;
                                    $subscriber -> created_at = date('Y-m-d H:i:s');
                                    $subscriber -> excluded_groups = null;
                                    $subscriber -> save();

                                    $response = "You successfully subscribed to dictionary \"{$dictionary->name}\", you will receive notification about keywords ";
                                    $response.= "of dictionary which can be found in these groups : ".implode(',',array_values(ArrayHelper::map($dictionary->monitoredGroups,'id','name')))."\n\n";
                                }else{
                                    $subscriber -> delete();
                                    $response = "You successfully un-subscribed from dictionary \"{$dictionary->name}\", you will not receive any notifications now\n\n";
                                }
                            }else{
                                $response = "Seems like dictionary with id {$argument} not exist. Please check your data\n\n";
                            }
                        }
                        //Отправка ответа отправителю от имени основной страницы
                        try {
                            $bot = new FbBotApp(CommonSettingsForm::getInstance()->fb_messenger_page_token);
                            $bot->send(new FacebookBotMessage($senderId,$response));
                        } catch (\Exception $ex) {
                            Yii::info($ex->getMessage(), 'info');
                        }
                    }
                }
                // К О М А Н Д Ы  К Н О П О К ( P O S T B A C K S )
                elseif (!empty($item['postback'])) {

                    //Команда кнопки
                    $command = $item['postback']['payload'];

                    if(!empty($command)){
                        //ID отправителя команды
                        $senderId = $item['sender']['id'];

                        //Аргумент (параметр) передаваемый в команде
                        $argument = null;

                        //Если это команда игнорирования группы
                        if($this->isCommand($command,'IGNORE',$argument)){

                            //Идентификаторы словаря и группы
                            $params = explode('_',$argument);
                            $dictionaryId = ArrayHelper::getValue($params,0);
                            $groupId = ArrayHelper::getValue($params,1);

                            /* @var $subscriber DictionarySubscriber */
                            $subscriber = DictionarySubscriber::find()->where(['facebook_id' => $senderId, 'dictionary_id' => (int)$dictionaryId])->one();
                            if(!empty($subscriber)){
                                $subscriber->AddToIgnored($groupId,true);

                                try {
                                    $bot = new FbBotApp(CommonSettingsForm::getInstance()->fb_messenger_page_token);
                                    $bot->send(new FacebookBotMessage($senderId,"Group successfully added to ignored group list\n\n"));
                                } catch (\Exception $ex) {
                                    Yii::info($ex->getMessage(), 'info');
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * Проаверка - соответствует ли строка команде. В случае успеха в $argument будет помещено значение
     * идушее после пробела (например для "COMMAND 123abc" в $argument будет записано 123abc)
     * @param string $text
     * @param string $commandId
     * @param string|int|null $argument
     * @return bool
     */
    private function isCommand($text, $commandId, &$argument = null)
    {
        $matches = [];
        $found = preg_match("/{$commandId}(\s)(\w+)/", $text, $matches);

        if (!$found) {
            $argument = null;
            return $text === $commandId;
        }

        $argument = ArrayHelper::getValue($matches, 2);
        return true;
    }
}