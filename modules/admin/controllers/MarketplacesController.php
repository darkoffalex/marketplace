<?php

namespace app\modules\admin\controllers;

use app\helpers\FileLoad;
use app\helpers\Help;
use app\models\MarketplaceTariffPrice;
use app\models\Rate;
use app\models\Tariff;
use Yii;
use yii\helpers\ArrayHelper;
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
                $model->save();

                //к детальному редактированию
                $this->redirect(Url::to(['/admin/marketplaces/update', 'id' => $model->id]));
            }
        }

        //вывести форму редактирования
        return $this->renderAjax('_create',compact('model','tariffs'));
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
        return $this->redirect(Url::to(['/admin/marketplaces/update', 'id' => $model->id]).'#picture');
    }

    /**
     * Добавление тарифа-вложения
     * @param $id
     * @return array|string
     * @throws NotFoundHttpException
     */
    public function actionAddTariff($id)
    {
        /* @var Marketplace $marketplace */
        $marketplace = Marketplace::findOne((int)$id);
        $tariffs = Tariff::find()->orderBy('is_main DESC')->all();

        //если не найден
        if(empty($marketplace)){
            throw new NotFoundHttpException('Page not found',404);
        }

        $model = new MarketplaceTariffPrice();

        //AJAX валидация
        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            $model->price = Help::toCents($model->price);
            $model->discounted_price = Help::toCents($model->discounted_price);
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }

        if($model->load(Yii::$app->request->post())){
            $model->price = Help::toCents($model->price);
            $model->discounted_price = Help::toCents($model->discounted_price);

            if($model->validate()){
                $model->marketplace_id = $marketplace->id;
                $model->save();

                return $this->redirect(Url::to(['/admin/marketplaces/update', 'id' => $model->marketplace_id]).'#tariffs');
            }
        }

        return $this->renderAjax('_add_tariff',compact('model','marketplace','tariffs'));
    }

    /**
     * Редактировать вложение-тариф
     * @param $id
     * @return array|string|Response
     * @throws NotFoundHttpException
     */
    public function actionEditTariffAttachment($id)
    {
        /* @var MarketplaceTariffPrice $model */
        $model = MarketplaceTariffPrice::findOne((int)$id);
        $tariffs = Tariff::find()->orderBy('is_main DESC')->all();

        //если не найден
        if(empty($model)){
            throw new NotFoundHttpException('Page not found',404);
        }

        //AJAX валидация
        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            $model->price = Help::toCents($model->price);
            $model->discounted_price = Help::toCents($model->discounted_price);
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }

        if($model->load(Yii::$app->request->post())){
            $model->price = Help::toCents($model->price);
            $model->discounted_price = Help::toCents($model->discounted_price);

            if($model->validate()){
                $model->save();
                return $this->redirect(Url::to(['/admin/marketplaces/update', 'id' => $model->marketplace_id]).'#tariffs');
            }
        }

        return $this->renderAjax('_add_tariff',compact('model','marketplace','tariffs'));
    }

    /**
     * Удаление вложения-тарифа
     * @param $id
     * @return Response
     * @throws NotFoundHttpException
     */
    public function actionDeleteTariffAttachment($id)
    {
        /* @var MarketplaceTariffPrice $model */
        $model = MarketplaceTariffPrice::findOne((int)$id);

        //если не найден
        if(empty($model)){
            throw new NotFoundHttpException('Page not found',404);
        }

        $marketplaceId = $model->marketplace_id;
        $model->delete();
        return $this->redirect(Url::to(['/admin/marketplaces/update', 'id' => $marketplaceId]).'#tariffs');
    }

    /**
     * Создание нового тарифа
     * @param int $normal
     * @return array|string|Response
     */
    public function actionCreateNewTariff($normal = 0)
    {
        $model = new Tariff();

        //если пришли данные из POST и они успешно заружены в объект
        if (Yii::$app->request->isPost && $model->load(Yii::$app->request->post())) {

            $model->base_price = Help::toCents($model->base_price);
            $model->discounted_price = Help::toCents($model->discounted_price);
            $model->is_main = (int)false;
            $model->show_on_page = (int)false;

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
                }

                if(empty($normal)){
                    Yii::$app->response->format = Response::FORMAT_JSON;
                    return [
                        'is_form' => false,
                        'content' => null,
                        'tariff' => ['id' => $model->id, 'name' => $model->name]
                    ];
                }

                return $this->redirect(Yii::$app->request->referrer);
            }
        }

        if(empty($normal)){
            Yii::$app->response->format = Response::FORMAT_JSON;
            return [
                'is_form' => true,
                'content' => $this->renderPartial('_tariff_editing',compact('model')),
                'tariff' => null
            ];
        }

        //вывести форму редактирования
        return $this->renderAjax('_tariff_editing',compact('model'));
    }
}