<?php

namespace app\modules\groupAdmin\controllers;

use app\components\Controller;
use app\helpers\Constants;
use app\models\MonitoredGroup;
use app\models\MonitoredGroupDictionary;
use app\models\MonitoredGroupPost;
use app\models\MonitoredGroupPostComment;
use kartik\form\ActiveForm;
use Yii;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * Управление группами
 *
 * @copyright 	2018 Alex Nem
 * @link 		https://github.com/darkoffalex
 * @author 		Alex Nem
 *
 * @package app\controllers
 */
class MonitoredGroupsController extends Controller
{
    /**
     * Обработчик ошибок (эмуляция action'а error)
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    /**
     * Главная страница
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new MonitoredGroup();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams,Yii::$app->user->id);
        return $this->render('index', compact('searchModel','dataProvider'));
    }

    /**
     * Создание
     * @return array|string
     */
    public function actionCreate()
    {
        $model = new MonitoredGroup();
        $model->scenario = 'editing';
        $model->user_id = Yii::$app->user->id;

        //AJAX валидация
        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }

        //если пришли данные из POST и они успешно заружены в объект
        if (Yii::$app->request->isPost && $model->load(Yii::$app->request->post())) {

            //если все данные в итоге корректны
            if($model->validate()){
                //базовые параметры
                $model->created_at = date('Y-m-d H:i:s',time());
                $model->updated_at = date('Y-m-d H:i:s',time());
                $model->created_by_id = Yii::$app->user->id;
                $model->updated_by_id = Yii::$app->user->id;

                //если не удалось сохранить
                if(!$model->save()){
                    //логировать ошибки валидации модели
                    Yii::info($model->getFirstErrors(),'info');
                }else{
                    //установка словарей
                    if(!empty($model->dictionaries_arr)){
                        foreach ($model->dictionaries_arr as $dictionaryId){
                            $cp = new MonitoredGroupDictionary();
                            $cp->dictionary_id = $dictionaryId;
                            $cp->monitored_group_id = $model->id;
                            $cp->save();
                        }
                    }
                }

                //к списку
                $this->redirect(Url::to(['/group-admin/monitored-groups/index']));
            }
        }

        //вывести форму редактирования
        return $this->renderAjax('_edit',compact('model'));
    }

    /**
     * Редактирование
     * @param $id
     * @return array|string
     * @throws NotFoundHttpException
     */
    public function actionUpdate($id)
    {
        /* @var $model MonitoredGroup */
        $model = MonitoredGroup::find()->where(['id' => (int)$id, 'user_id' => Yii::$app->user->id])->one();

        //если не найден
        if(empty($model)){
            throw new NotFoundHttpException('Page not found',404);
        }

        //AJAX валидация
        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }

        //если пришли данные из POST и они успешно заружены в объект
        if (Yii::$app->request->isPost && $model->load(Yii::$app->request->post())) {

            //если все данные в итоге корректны
            if($model->validate()){
                //базовые параметры
                $model->created_at = date('Y-m-d H:i:s',time());
                $model->created_by_id = Yii::$app->user->id;

                //если не удалось сохранить
                if(!$model->save()){
                    //логировать ошибки валидации модели
                    Yii::info($model->getFirstErrors(),'info');
                }else{
                    //отвязать от всех словарей
                    MonitoredGroupDictionary::deleteAll(['monitored_group_id' => $model->id]);

                    //установка словарей
                    if(!empty($model->dictionaries_arr)){
                        foreach ($model->dictionaries_arr as $dictionaryId){
                            $cp = new MonitoredGroupDictionary();
                            $cp->dictionary_id = $dictionaryId;
                            $cp->monitored_group_id = $model->id;
                            $cp->save();
                        }
                    }
                }

                //к списку
                $this->redirect(Yii::$app->request->referrer);
            }
        }

        //вывести форму редактирования
        return $this->renderAjax('_edit',compact('model'));
    }

    /**
     * Смена статуса из списка (ajax)
     * @param $id
     * @param $status
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionStatusChange($id, $status)
    {
        Yii::$app->response->format = Response::FORMAT_RAW;

        /* @var $model MonitoredGroup */
        $model = MonitoredGroup::find()->where(['id' => (int)$id, 'user_id' => Yii::$app->user->id])->one();

        //если не найден
        if(empty($model)){
            throw new NotFoundHttpException('Page not found',404);
        }

        //установить в зависимости от значения status
        $model->status_id = $status == 'true' ? Constants::STATUS_ENABLED : Constants::STATUS_DISABLED;
        if($model->save()){
            return 'OK';
        }

        return 'FAILED';
    }


    /**
     * Удаление
     * @param $id
     * @return Response
     * @throws NotFoundHttpException
     */
    public function actionDelete($id)
    {
        /* @var $model MonitoredGroup */
        $model = MonitoredGroup::find()->where(['id' => (int)$id, 'user_id' => Yii::$app->user->id])->one();

        //если не найден
        if(empty($model)){
            throw new NotFoundHttpException('Page not found',404);
        }

        $model->delete();

        //вернуться
        return $this->redirect(Yii::$app->request->referrer);
    }

    /**
     * Информация
     * @param $id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionInfo($id)
    {
        /* @var $model MonitoredGroup */
        $model = MonitoredGroup::find()->where(['id' => (int)$id, 'user_id' => Yii::$app->user->id])->one();

        //если не найден
        if(empty($model)){
            throw new NotFoundHttpException('Page not found',404);
        }

        return $this->renderAjax('_info',compact('model'));
    }

    /**
     * Установка словарей из списка (ajax)
     * @param $id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionSetDictionaries($id)
    {
        Yii::$app->response->format = Response::FORMAT_RAW;

        /* @var $model MonitoredGroup */
        $model = MonitoredGroup::find()->where(['id' => (int)$id, 'user_id' => Yii::$app->user->id])->one();

        //если не найден
        if(empty($model)){
            throw new NotFoundHttpException('Page not found',404);
        }

        $dictionaries_arr = Yii::$app->request->post('dictionaries');

        //отвязать от всех словарей
        MonitoredGroupDictionary::deleteAll(['monitored_group_id' => $model->id]);

        //установка словарей
        if(!empty($dictionaries_arr)){
            foreach ($dictionaries_arr as $dictionaryId){
                $cp = new MonitoredGroupDictionary();
                $cp->dictionary_id = $dictionaryId;
                $cp->monitored_group_id = $model->id;
                $cp->save();
            }
        }
        return 'OK';
    }

    /**
     * Список спаршенных постов
     * @return string
     */
    public function actionPosts()
    {
        $searchModel = new MonitoredGroupPost();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, Yii::$app->user->id);
        return $this->render('posts', compact('searchModel','dataProvider'));
    }

    /**
     * Список комментариев
     * @param null $id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionComments($id = null)
    {
        /* @var $post MonitoredGroupPost */
        $post = MonitoredGroupPost::find()->where(['id' => $id])->one();

        if(empty($post)){
            throw new NotFoundHttpException('Page not found',404);
        }

        /* @var $comments MonitoredGroupPostComment[] */
        $comments = MonitoredGroupPostComment::find()
            ->with(['childrenSorted'])
            ->where(['post_id' => $post->id])
            ->andWhere('parent_id = 0 OR parent_id IS NULL')
            ->orderBy('created_at ASC')
            ->all();

        return $this->renderAjax('_comments',compact('comments','post'));
    }
}
