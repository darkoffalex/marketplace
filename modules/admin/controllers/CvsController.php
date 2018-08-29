<?php

namespace app\modules\admin\controllers;

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
 * @copyright 	2017 Alex Nem
 * @link 		https://github.com/darkoffalex
 * @author 		Alex Nem
 *
 * @package app\modules\admin\controllers
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
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render('index', compact('searchModel','dataProvider'));
    }

    /**
     * Просмотр и одобрение заявки
     * @param $id
     * @return array|string
     * @throws NotFoundHttpException
     */
    public function actionUpdate($id)
    {
        /* @var Cv $model */
        $model = Cv::findOne((int)$id);

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

                //базовые параметры, обновить
                $model->updated_at = date('Y-m-d H:i:s',time());
                $model->updated_by_id = Yii::$app->user->id;
                $model->update();

                //к списку
                $this->redirect(Url::to(['/admin/cvs/index', 'id' => $model->id]));
            }
        }

        //вывести форму редактирования
        return $this->renderAjax('_edit',compact('model','flags'));
    }

    /**
     * Удаление заявки
     * @param $id
     * @throws NotFoundHttpException
     */
    public function actionDelete($id)
    {
        /* @var Cv $model */
        $model = Cv::findOne((int)$id);

        //если не найден
        if(empty($model)){
            throw new NotFoundHttpException('Page not found',404);
        }

        //удаление из базы
        $model->delete();

        //к списку
        $this->redirect(Url::to(['/admin/cvs/index', 'id' => $model->id]));
    }
}