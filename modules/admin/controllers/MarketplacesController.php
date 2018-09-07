<?php

namespace app\modules\admin\controllers;

use app\helpers\FileLoad;
use app\helpers\Help;
use app\models\Rate;
use app\models\Tariff;
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
        /* @var $tariffs Tariff */
        $tariffs = Tariff::find()->all();

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

            //Загрузка изображение
            $model->header_image = UploadedFile::getInstance($model,'header_image');

            //если все данные в итоге корректны
            if($model->validate()){

                //Сохранение изображения
                FileLoad::loadAndClearOld($model,'header_image','header_image_filename',!empty($model->header_image));

                //базовые параметры, обновить
                $model->created_at = date('Y-m-d H:i:s',time());
                $model->created_by_id = Yii::$app->user->id;
                $model->updated_at = date('Y-m-d H:i:s',time());
                $model->updated_by_id = Yii::$app->user->id;

                if($model->save()){
                    //Синхронизация тарифов
                    $model->syncTariffs($model->tariffs);
                }

                //к детальному редактированию
                $this->redirect(Url::to(['/admin/marketplaces/index']));
            }
        }

        //вывести форму редактирования
        return $this->render('edit',compact('model','tariffs'));
    }

    /**
     * Редактирование маркетплейса
     * @param $id
     * @return array|string
     * @throws NotFoundHttpException
     */
    public function actionUpdate($id)
    {
        /* @var $tariffs Tariff */
        $tariffs = Tariff::find()->all();

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

                //Синхронизация тарифов
                $model->syncTariffs($model->tariffs);

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

        //обновить связанные данные
        $model->refresh();

        //вывести форму редактирования
        return $this->render('edit',compact('model','tariffs'));
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