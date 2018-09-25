<?php

namespace app\modules\groupAdmin\controllers;

use app\components\Controller;
use app\helpers\Constants;
use app\helpers\Help;
use app\models\Dictionary;
use app\models\MonitoredGroupDictionary;
use kartik\form\ActiveForm;
use Yii;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * Словари ключевых слов
 *
 * @copyright 	2018 Alex Nem
 * @link 		https://github.com/darkoffalex
 * @author 		Alex Nem
 *
 * @package app\modules\groupAdmin\controllers
 */
class DictionariesController extends Controller
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
        $searchModel = new Dictionary();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, Yii::$app->user->id);
        return $this->render('index', compact('searchModel','dataProvider'));
    }

    /**
     * Создание
     * @return array|string
     */
    public function actionCreate()
    {
        $model = new Dictionary();
        $model->user_id = Yii::$app->user->id;
        $model->scenario = 'editing';

        //Генерация уникального ключа
        $uniqueKey = Help::randomString(6,true);
        while (Dictionary::find()->where(['key' => $uniqueKey])->count() > 0){
            $uniqueKey = Help::randomString(6,true);
        }

        $model->key = $uniqueKey;

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
                    if(!empty($model->groups_arr)){
                        foreach ($model->groups_arr as $groupId){
                            $cp = new MonitoredGroupDictionary();
                            $cp->dictionary_id = $model->id;
                            $cp->monitored_group_id = $groupId;
                            $cp->save();
                        }
                    }
                }

                //к списку
                $this->redirect(Url::to(['/group-admin/dictionaries/index']));
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
        /* @var $model Dictionary */
        $model = Dictionary::find()->where(['id' => (int)$id, 'user_id' => Yii::$app->user->id])->one();

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

                //Если ключ пуст - сгенерировать
                if(empty($model->key)){
                    $uniqueKey = Help::randomString(6,true);
                    while (Dictionary::find()->where(['key' => $uniqueKey])->count() > 0){
                        $uniqueKey = Help::randomString(6,true);
                    }
                    $model->key = $uniqueKey;
                }

                //если не удалось сохранить
                if(!$model->save()){
                    //логировать ошибки валидации модели
                    Yii::info($model->getFirstErrors(),'info');
                }else{
                    //отвязать от всех групп
                    MonitoredGroupDictionary::deleteAll(['dictionary_id' => $model->id]);

                    //установка групп
                    if(!empty($model->groups_arr)){
                        foreach ($model->groups_arr as $groupId){
                            $cp = new MonitoredGroupDictionary();
                            $cp->dictionary_id = $model->id;
                            $cp->monitored_group_id = $groupId;
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
     * Удаление
     * @param $id
     * @return Response
     * @throws NotFoundHttpException
     */
    public function actionDelete($id)
    {
        /* @var $model Dictionary */
        $model = Dictionary::find()->where(['id' => (int)$id, 'user_id' => Yii::$app->user->id])->one();

        //если не найден
        if(empty($model)){
            throw new NotFoundHttpException('Page not found',404);
        }

        $model->delete();

        //вернуться
        return $this->redirect(Yii::$app->request->referrer);
    }


    /**
     * Установка словарей из списка (ajax)
     * @param $id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionSetGroups($id)
    {
        Yii::$app->response->format = Response::FORMAT_RAW;

        /* @var $model Dictionary */
        $model = Dictionary::find()->where(['id' => (int)$id])->one();

        //если не найден
        if(empty($model)){
            throw new NotFoundHttpException('Page not found',404);
        }

        $groups_arr = Yii::$app->request->post('groups');

        //отвязать от всех групп
        MonitoredGroupDictionary::deleteAll(['dictionary_id' => $model->id]);

        //установка групп
        if(!empty($groups_arr)){
            foreach ($groups_arr as $groupId){
                $cp = new MonitoredGroupDictionary();
                $cp->dictionary_id = $model->id;
                $cp->monitored_group_id = $groupId;
                $cp->save();
            }
        }

        return 'OK';
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

        /* @var $model Dictionary */
        $model = Dictionary::find()->where(['id' => (int)$id, 'user_id' => Yii::$app->user->id])->one();

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
}
