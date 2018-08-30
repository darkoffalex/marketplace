<?php

namespace app\modules\groupAdmin\controllers;

use app\helpers\Constants;
use app\models\Cv;
use app\models\CvSearch;
use Yii;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * Контроллер отвечающий за управление заявками
 *
 * @copyright 	2018 Alex Nem
 * @link 		https://github.com/darkoffalex
 * @author 		Alex Nem
 *
 * @package app\modules\groupAdmin\controllers
 */
class CvsController extends Controller
{
    /**
     * Список все заявок
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new CvSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, Yii::$app->user->id);
        return $this->render('index', compact('searchModel','dataProvider'));
    }

    /**
     * Создание заявки
     * @return array|string
     * @throws NotFoundHttpException
     */
    public function actionCreate()
    {
        /* @var Cv $model */
        $model = new Cv();
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

                //базовые параметры, обновить
                $model->status_id = Constants::CV_STATUS_NEW;
                $model->created_at = date('Y-m-d H:i:s',time());
                $model->created_by_id = Yii::$app->user->id;
                $model->updated_at = date('Y-m-d H:i:s',time());
                $model->updated_by_id = Yii::$app->user->id;
                $model->save();

                //к списку
                $this->redirect(Url::to(['/group-admin/cvs/index']));
            }
        }

        //вывести форму редактирования
        return $this->renderAjax('_edit',compact('model','flags'));
    }

    /**
     * Просмотр заявки
     * @param $id
     * @return string
     */
    public function actionView($id)
    {
        /* @var Cv $model */
        $model = Cv::find()->where(['id' => (int)$id, 'user_id' => Yii::$app->user->id])->one();
        return $this->renderPartial('_view',compact('model'));
    }

    /**
     * Удаление заявки
     * @param $id
     * @throws NotFoundHttpException
     */
    public function actionDelete($id)
    {
        /* @var Cv $model */
        $model = Cv::find()->where(['id' => (int)$id, 'user_id' => Yii::$app->user->id])->one();

        //если не найден
        if(empty($model)){
            throw new NotFoundHttpException('Page not found',404);
        }

        //удаление из базы
        $model->delete();

        //к списку
        $this->redirect(Url::to(['/group-admin/cvs/index']));
    }
}