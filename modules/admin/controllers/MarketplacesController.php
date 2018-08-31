<?php

namespace app\modules\admin\controllers;

use Yii;
use yii\helpers\Url;
use yii\web\Controller;
use app\models\MarketplaceSearch;
use yii\web\NotFoundHttpException;
use app\models\Marketplace;
use kartik\form\ActiveForm;
use yii\web\Response;
/**
 * Управление маркетплейсами клиентов
 *
 * @copyright 	2018 Alex Nem
 * @link 		https://github.com/darkoffalex
 * @author 		Alex Nem
 *
 * @package app\modules\admin\controllers
 */
class MarketplacesController extends Controller
{
    /**
     * Список
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new MarketplaceSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render('index', compact('searchModel','dataProvider'));
    }

    /**
     * Создание маркетплейса
     * @return array|string
     * @throws NotFoundHttpException
     */
    public function actionCreate()
    {
        /* @var Marketplace $model */
        $model = new Marketplace();
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
                $model->created_at = date('Y-m-d H:i:s',time());
                $model->created_by_id = Yii::$app->user->id;
                $model->updated_at = date('Y-m-d H:i:s',time());
                $model->updated_by_id = Yii::$app->user->id;
                $model->save();

                //к детальному редактированию
                $this->redirect(Url::to(['/admin/marketplaces/update','id' => $model->id]));
            }
        }

        //вывести форму редактирования
        return $this->renderAjax('_create',compact('model'));
    }

    /**
     * Редактирование маркетплейса
     * @param $id
     * @return array|string
     * @throws NotFoundHttpException
     */
    public function actionUpdate($id)
    {
        /* @var Marketplace $model */
        $model = Marketplace::findOne((int)$id);

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
            }
        }

        //вывести форму редактирования
        return $this->render('edit',compact('model'));
    }
}