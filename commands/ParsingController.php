<?php
namespace app\commands;

use app\helpers\Constants;
use app\models\Dictionary;
use app\models\forms\SettingsForm;
use app\models\MonitoredGroup;
use app\models\MonitoredGroupPost;
use app\models\MonitoredGroupPostComment;
use Carbon\Carbon;
use Facebook\Facebook;
use yii\console\Controller;
use yii\db\Expression;
use yii\helpers\ArrayHelper;
use app\helpers\ConsoleHelper;

/**
 * Collects posts and comments for monitored group
 *
 * @copyright 	2018 Alex Nem
 * @link 		https://github.com/darkoffalex
 * @author 		Alex Nem
 *
 * @package app\commands
 */
class ParsingController extends Controller
{
    //Сколько дней (суток) в прошлое нужно парсить источник первый раз
    const FIRST_PARSE_INTERVAL_DAYS = 5;

    //Сколько минут должно пройти с последней синхронизации постов чтобы источник попадал в очередь на обновление
    const MAX_POST_SYC_ELD_MINUTES = 5;

    //Сколько групп будет запрошено за вызов во время синхронизации постов
    const CHUNK_SIZE_FEED_SYNC = 10;

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
     * Synchronize (parse) content for sources
     */
    public function actionIndex()
    {
        //Сообщение о начале процесса
        $pid = ConsoleHelper::processStart();


        //Найти порцию источников ожидающих обновления
        /* @var $groups MonitoredGroup[] */
        $groups = MonitoredGroup::find()
            ->with(['dictionaries'])
            ->where(new Expression('sync_done_last_time < (NOW() - INTERVAL '.self::MAX_POST_SYC_ELD_MINUTES.' MINUTE) OR sync_done_last_time IS NULL'))
            ->andWhere(new Expression('sync_in_progress IS NULL OR sync_in_progress = 0'))
            ->andWhere(['status_id' => Constants::STATUS_ENABLED])
            ->orderBy('sync_done_last_time ASC')
            ->limit(self::CHUNK_SIZE_FEED_SYNC)
            ->all();

        if(!empty($groups)){
            foreach ($groups as $group) {

                echo "Processing group : {$group->id} ({$group->facebook_id})\n";

                //Проверить - не обновлен ли источник (он мог попасть в порцию другого процесса пока обновлялся предыдущий)
                //Если уже обновлен - пропустить и перейти к следующему
                $group->refresh();
                if (!empty($group->sync_in_progress)) {
                    echo "This source is not in queue anymore. Passing iteration\n";
                    continue;
                }

                //Заблокировать источник (дабы он более не попал в чью-то очередь), пометить как "в процессе"
                $group->sync_in_progress = (int)true;
                $group->update();

                //Основная часть - парсинг и обновление
                try{

                    //Получить словари группы
                    /* @var $dictionaries Dictionary[] */
                    $dictionaries = $group->getDictionaries()->where(['status_id' => Constants::STATUS_ENABLED])->all();

                    //Объект для работы с API
                    $fb = new Facebook([
                        'app_id' => SettingsForm::getInstance()->fb_auth_client_id,
                        'app_secret' => SettingsForm::getInstance()->fb_auth_app_secret
                    ]);

                    //Выяснить с какого по какое число (и время) нужно собирать данные
                    //Если это первый парсинг - значит начинаем с недели назад до текущего момента
                    //Если это не первый раз - с момента до которого были собраны данные в прошлый раз то текущего
                    if(empty($group->sync_to)){
                        $group->sync_since = Carbon::now()->subDays(self::FIRST_PARSE_INTERVAL_DAYS)->format('Y-m-d H:i:s');
                        $group->sync_to = Carbon::now()->format('Y-m-d H:i:s');
                    }else{
                        $group->sync_since = $group->sync_to;
                        $group->sync_to = Carbon::now()->format('Y-m-d H:i:s');
                    }

                    //Получить время в виде timestamp
                    $since = Carbon::parse($group->sync_since)->getTimestamp();
                    $until = Carbon::parse($group->sync_to)->getTimestamp();

                    //Сформировать запрос
                    $baseRequestParams =  urldecode(http_build_query([
                        'fields' => implode(',',[
                            'message',
                            'type',
//                            'attachments',
                            'comments.fields(created_time,updated_time,from.fields(name,picture),message,attachment,comments.fields(created_time,from.fields(name,picture),message,attachment)).summary(true)',
                            'updated_time',
                            'created_time',
                            'from.fields(name,picture)',
                        ]),
                        'limit' => 25,
                        'since' => $since,
                        'until' => $until,
                        'summary' => 'true'
                    ]));
                    $url = "/".$group->facebook_id."/feed?{$baseRequestParams}";

                    //Дата обновления обрабатываемого поста
                    $postLastUpdateTime = null;

                    //Пока есть адрес запроса
                    while (!empty($url)){
                        //Получить текущую "страницу"
                        $fbResponse = $fb->get($url, $group->user->facebook_token);

                        //Формирование пути запроса следующей страницы (если больше нет страниц - пустая строка)
                        $nextRequest = $fbResponse->getGraphEdge()->getNextPageRequest();
                        $newUrl = !empty($nextRequest) ? str_replace('/'.$fb->getDefaultGraphVersion(),'',$nextRequest->getUrl()) : null;
                        $url = (!empty($newUrl) && $newUrl != $url) ? $newUrl : null;

                        //Получить посты текущей "страницы" и пройтись по ним в цикле
                        $postsArr = ArrayHelper::getValue($fbResponse->getDecodedBody(),'data',[]);

                        if(!empty($postsArr)){
                            foreach ($postsArr as $postItem){

                                //Дата обновления поста
                                $postLastUpdateTime = Carbon::parse(ArrayHelper::getValue($postItem,'updated_time'))->format('Y-m-d H:i:s');

                                //Сообщение об обрабатываемом посте
                                $postId = ArrayHelper::getValue($postItem,'id');
                                echo "Processing post : {$postId}\n";

                                //Создать или обновить пост
                                /* @var $post MonitoredGroupPost */
                                $post = MonitoredGroupPost::find()->where(['facebook_id' => ArrayHelper::getValue($postItem,'id')])->one();
                                if(empty($post)){
                                    $post = new MonitoredGroupPost();
                                    $post->facebook_id = ArrayHelper::getValue($postItem,'id');
                                }
                                $post->group_id = $group->id;
                                $post->text = ArrayHelper::getValue($postItem,'message');
                                $post->comments_count = (int)ArrayHelper::getValue($postItem,'comments.summary.total_count');
                                $post->created_at = ArrayHelper::getValue($postItem,'created_time');
                                $post->updated_at = ArrayHelper::getValue($postItem,'updated_time');

                                $creatingPost = $post->isNewRecord;
                                echo $creatingPost ? "Creating post {$post->facebook_id} - " : "Updating post {$post->facebook_id} - ";
                                $success = $post->save();
                                echo $success ? "DONE\n" : "ERROR\n";

                                if(!empty($dictionaries) && $creatingPost && $success){
                                    echo "Checking for ".count($dictionaries)." dictionaries - ";
                                    foreach ($dictionaries as $dictionary){
                                        $dictionary->NotifyAboutWords($group->id,$post->text,$post->id);
                                    }
                                    echo "DONE\n";
                                }

                                $comments = ArrayHelper::getValue($postItem,'comments.data');
                                if($success && !empty($comments)){
                                    echo "Appending comments to post {$post->facebook_id} : STARTED\n";
                                    $this->AppendComments($post,$dictionaries,$comments);
                                    echo "Appending comments to post {$post->facebook_id} : FINISHED\n";
                                }
                            }
                        }
                    }
                }
                catch (\Exception $ex){
                    //Коррекция даты запрашивания постов (от и до). Чтобы в случае обрыва работы функции в следующий раз начать
                    //с даты на которой оборвался процесс, присваиваем дату обновления последнего обрабатываемого поста
                    $group->sync_since = !empty($postLastUpdateTime) ? $postLastUpdateTime : $group->sync_since;
                    $group->parsing_errors_log .=  date('Y-m-d H:i:s', time())." - ".$ex->getMessage()."\n";
                    echo $ex->getMessage()."\n";
                }

                //Разблокировать источник
                $group->sync_in_progress= (int)false;
                $group->sync_done_last_time = date('Y-m-d H:i:s',time());
                $group->update();

                //Сообщение о завершении синхронизации источника
                echo "Finished group : {$group->id}\n\n";
            }
        }


        //Сообщение о завершении процесса
        ConsoleHelper::processEnd($pid);
    }

    /**
     * Добавление комментариев
     * @param MonitoredGroupPost $post
     * @param Dictionary[] $dictionaries
     * @param array $comments
     * @param int $parentId
     */
    private function AppendComments(&$post,&$dictionaries,$comments,$parentId = 0)
    {
        if(!empty($comments)){
            foreach ($comments as $commentItem){

                /* @var $comment MonitoredGroupPostComment */
                $comment = MonitoredGroupPostComment::find()
                    ->where(['facebook_id' => ArrayHelper::getValue($commentItem,'id')])
                    ->one();

                $new = empty($comment);

                if($new){
                    $comment = new MonitoredGroupPostComment();
                    $comment -> facebook_id = ArrayHelper::getValue($commentItem,'id');
                }

                $comment->post_id = $post->id;
                $comment->text = ArrayHelper::getValue($commentItem,'message');
                $comment->parent_id = $parentId;
                $comment->comments_count = count(ArrayHelper::getValue($commentItem,'comments.data',[]));
                $comment->created_at = Carbon::parse(ArrayHelper::getValue($commentItem,'created_time'))->format('Y-m-d H:i:s');
                $comment->updated_at = Carbon::parse(ArrayHelper::getValue($commentItem,'updated_time'))->format('Y-m-d H:i:s');

                echo $new ? "  Creating comment {$comment->facebook_id} - " : "  Updating comment {$comment->facebook_id} - ";

                if($comment->save()){

                    echo "DONE\n";

                    if($new){
                        if(!empty($dictionaries)){
                            echo "  Checking for ".count($dictionaries)." dictionaries - ";
                            foreach ($dictionaries as $dictionary){
                                $dictionary->NotifyAboutWords($post->group_id,$comment->text,$post->id,$comment->id);
                            }
                            echo "DONE\n";
                        }
                    }

                    if($comment->comments_count > 0){
                        $this->AppendComments($post,$dictionaries,ArrayHelper::getValue($commentItem,'comments.data'),$comment->id);
                    }
                }
                else{
                    echo "ERROR\n";
                }
            }
        }
    }
}