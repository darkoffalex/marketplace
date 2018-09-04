<?php

namespace app\modules\admin\controllers;

use app\helpers\FileLoad;
use app\helpers\Help;
use app\models\Rate;
use Yii;
use yii\helpers\Url;
use yii\web\Controller;
use app\models\MarketplaceSearch;
use yii\web\NotFoundHttpException;
use app\models\Marketplace;
use kartik\form\ActiveForm;
use yii\web\Response;
use yii\web\UploadedFile;

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

            //Загрузка изображение
            $model->header_image = UploadedFile::getInstance($model,'header_image');

            //если все данные в итоге корректны
            if($model->validate()){
                //Сохранение изображения
                FileLoad::loadAndClearOld($model,'header_image','header_image_filename',!empty($model->header_image));

                //базовые параметры, обновить
                $model->updated_at = date('Y-m-d H:i:s',time());
                $model->updated_by_id = Yii::$app->user->id;
                $model->update();

                //Если это редактирование изображения
                if(Yii::$app->request->post('image_editing')){
                    return $this->redirect(Url::to(['/admin/marketplaces/update', 'id' => $model->id]).'#picture');
                }
            }
        }

        //вывести форму редактирования
        return $this->render('edit',compact('model'));
    }

    /**
     * Удаление маркетплейса
     * @param $id
     * @return Response
     * @throws NotFoundHttpException
     */
    public function actionDelete($id)
    {
        /* @var Marketplace $model */
        $model = Marketplace::findOne((int)$id);

        //если не найден
        if(empty($model)){
            throw new NotFoundHttpException('Page not found',404);
        }

        $model->delete();
        return $this->redirect(Yii::$app->request->referrer);
    }

    /**
     * Создать тариф
     * @param $id
     * @return array|string
     * @throws NotFoundHttpException
     */
    public function actionCreateRate($id)
    {
        /* @var Marketplace $model */
        $marketplace = Marketplace::findOne((int)$id);

        //если не найден
        if(empty($marketplace)){
            throw new NotFoundHttpException('Page not found',404);
        }

        $model = new Rate();
        $model->marketplace_id = $marketplace->id;

        //AJAX валидация
        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            $model->price = Help::toCents($model->price);
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }

        //если пришли данные из POST и они успешно заружены в объект
        if (Yii::$app->request->isPost && $model->load(Yii::$app->request->post())) {
            $model->price = Help::toCents($model->price);
            if($model->validate()){
                $model->save();
                return $this->redirect(Url::to(['/admin/marketplaces/update', 'id' => $model->marketplace_id]).'#rates');
            }
        }

        //вывести форму редактирования
        return $this->renderAjax('_edit_rate',compact('model'));
    }

    /**
     * Редактировать тариф
     * @param $id
     * @return array|string|Response
     * @throws NotFoundHttpException
     */
    public function actionEditRate($id)
    {
        /* @var $model Rate */
        $model = Rate::find()->where(['id' => (int)$id])->one();

        //если не найден
        if(empty($model)){
            throw new NotFoundHttpException('Page not found',404);
        }

        //AJAX валидация
        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            $model->price = Help::toCents($model->price);
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }

        //если пришли данные из POST и они успешно заружены в объект
        if (Yii::$app->request->isPost && $model->load(Yii::$app->request->post())) {
            $model->price = Help::toCents($model->price);
            if($model->validate()){
                $model->save();
                return $this->redirect(Url::to(['/admin/marketplaces/update', 'id' => $model->marketplace_id]).'#rates');
            }
        }

        //вывести форму редактирования
        return $this->renderAjax('_edit_rate',compact('model'));
    }

    /**
     * Удалить тариф
     * @param $id
     * @return Response
     * @throws NotFoundHttpException
     */
    public function actionDeleteRate($id)
    {
        /* @var $model Rate */
        $model = Rate::find()->where(['id' => (int)$id])->one();

        //если не найден
        if(empty($model)){
            throw new NotFoundHttpException('Page not found',404);
        }

        $mpId = $model->marketplace_id;
        $model->delete();
        return $this->redirect(Url::to(['/admin/marketplaces/update', 'id' => $mpId]).'#rates');
    }

    /**
     * Удаление изображения маркетплейса
     * @param $id
     * @return Response
     * @throws NotFoundHttpException
     */
    public function actionDeleteImage($id)
    {
        /* @var Marketplace $model */
        $model = Marketplace::findOne((int)$id);

        //если не найден
        if(empty($model)){
            throw new NotFoundHttpException('Page not found',404);
        }

        FileLoad::deleteFile($model,'header_image_filename');
        return $this->redirect(Url::to(['/admin/marketplaces/update', 'id' => $model->id]).'#rates');
    }
}