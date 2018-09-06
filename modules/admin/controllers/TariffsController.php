<?php

namespace app\modules\admin\controllers;

use app\helpers\FileLoad;
use app\helpers\Help;
use app\models\TariffSearch;
use yii\helpers\Url;
use yii\web\Controller;
use app\models\Tariff;
use kartik\form\ActiveForm;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use Yii;
use yii\web\UploadedFile;

/**
 * Контроллер для управления тарифами
 *
 * @copyright 	2018 Alex Nem
 * @link 		https://github.com/darkoffalex
 * @author 		Alex Nem
 *
 * @package app\modules\admin\controllers
 */
class TariffsController extends Controller
{
    /**
     * Список тарифов
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new TariffSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render('index', compact('searchModel','dataProvider'));
    }

    /**
     * Создание
     * @return array|string
     */
    public function actionCreate()
    {
        $model = new Tariff();

        //AJAX валидация
        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            $model->base_price = Help::toCents($model->base_price);
            $model->discounted_price = Help::toCents($model->discounted_price);
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }

        //если пришли данные из POST и они успешно заружены в объект
        if (Yii::$app->request->isPost && $model->load(Yii::$app->request->post())) {

            $model->base_price = Help::toCents($model->base_price);
            $model->discounted_price = Help::toCents($model->discounted_price);

            //Загрузка изображение
            $model->image = UploadedFile::getInstance($model,'image');

            //если все данные в итоге корректны
            if($model->validate()){

                //Сохранение изображения
                FileLoad::loadAndClearOld($model,'image','image_filename',!empty($model->image));

                //базовые параметры
                $model->created_at = date('Y-m-d H:i:s',time());
                $model->updated_at = date('Y-m-d H:i:s',time());
                $model->created_by_id = Yii::$app->user->id;
                $model->updated_by_id = Yii::$app->user->id;

                //если не удалось сохранить
                if(!$model->save()){
                    //логировать ошибки валидации модели
                    Yii::info($model->getFirstErrors(),'info');
                }

                //к списку
                $this->redirect(Url::to(['/admin/tariffs/index']));
            }
        }

        //вывести форму редактирования
        return $this->render('edit',compact('model'));
    }

    /**
     * Редактировать
     * @param $id
     * @return array|string
     * @throws NotFoundHttpException
     */
    public function actionUpdate($id)
    {
        /* @var $model Tariff */
        $model = Tariff::find()->where(['id' => (int)$id])->one();

        if(empty($model)){
            throw new NotFoundHttpException(Yii::t('app','Page not found'),404);
        }

        //AJAX валидация
        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            $model->base_price = Help::toCents($model->base_price);
            $model->discounted_price = Help::toCents($model->discounted_price);
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }

        //если пришли данные из POST и они успешно заружены в объект
        if (Yii::$app->request->isPost && $model->load(Yii::$app->request->post())) {

            $model->base_price = Help::toCents($model->base_price);
            $model->discounted_price = Help::toCents($model->discounted_price);

            //Загрузка изображение
            $model->image = UploadedFile::getInstance($model,'image');

            //если все данные в итоге корректны
            if($model->validate()){

                //Сохранение изображения
                FileLoad::loadAndClearOld($model,'image','image_filename',!empty($model->image));

                //базовые параметры
                $model->created_at = date('Y-m-d H:i:s',time());
                $model->updated_at = date('Y-m-d H:i:s',time());
                $model->created_by_id = Yii::$app->user->id;
                $model->updated_by_id = Yii::$app->user->id;

                //если не удалось сохранить
                if(!$model->save()){
                    //логировать ошибки валидации модели
                    Yii::info($model->getFirstErrors(),'info');
                }
            }
        }

        //вывести форму редактирования
        return $this->render('edit',compact('model'));
    }

    /**
     * Удалить
     * @param $id
     * @return Response
     * @throws NotFoundHttpException
     */
    public function actionDelete($id)
    {
        /* @var $model Tariff */
        $model = Tariff::find()->where(['id' => (int)$id])->one();

        if(empty($model)){
            throw new NotFoundHttpException(Yii::t('app','Page not found'),404);
        }

        FileLoad::deleteFile($model,'image_filename');
        $model->delete();

        return $this->redirect(Yii::$app->request->referrer);
    }

    /**
     * Удалить изображение
     * @param $id
     * @return Response
     * @throws NotFoundHttpException
     */
    public function actionDeleteImage($id)
    {
        /* @var $model Tariff */
        $model = Tariff::find()->where(['id' => (int)$id])->one();

        if(empty($model)){
            throw new NotFoundHttpException(Yii::t('app','Page not found'),404);
        }

        FileLoad::deleteFile($model,'image_filename');

        return $this->redirect(Yii::$app->request->referrer);
    }
}