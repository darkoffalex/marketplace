<?php

namespace app\modules\admin\controllers;

use app\helpers\Constants;
use app\helpers\CropHelper;
use app\helpers\Help;
use app\helpers\Sort;
use app\models\Marketplace;
use app\models\MarketplaceKey;
use app\models\Poster;
use app\models\PosterImage;
use app\models\PosterSearch;
use app\models\User;
use yii\helpers\FileHelper;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\bootstrap\ActiveForm;
use Yii;
use yii\web\UploadedFile;

/**
 * Управление объявлениями
 *
 * @copyright 	2018 Alex Nem
 * @link 		https://github.com/darkoffalex
 * @author 		Alex Nem
 *
 * @package app\modules\admin\controllers
 */
class PostersController extends Controller
{
    /**
     * Список объявлений
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new PosterSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render('index', compact('searchModel','dataProvider'));
    }

    /**
     * Создание объявления
     * @return array|string|Response
     */
    public function actionCreate()
    {
        $model = new Poster();

        //AJAX валидация
        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }

        //если пришли данные из POST и они успешно заружены в объект
        if (Yii::$app->request->isPost && $model->load(Yii::$app->request->post())) {

            //если все данные в итоге корректны
            if($model->validate()){

                /* @var $ownerUser User */
                $ownerUser = User::find()->where(['id' => (int)$model->user_id])->one();

                //если у пользователя нет доступа к маркетплейсу - связать через ключ
                if(!$ownerUser->hasAccessToMarketplace($model->marketplace_id)){

                    //создать ключ маркетплейса
                    $key = new MarketplaceKey();
                    $key->marketplace_id = $model->marketplace_id;
                    $key->code = Help::randomString(10);
                    $key->used_by_id = $ownerUser->id;

                    //убедиться в уникальности ключа
                    while (MarketplaceKey::find()->where(['code' => $key->code])->count() > 0){
                        $key->code = Help::randomString(10);
                    }

                    //сохранить
                    $key->save();
                }

                //базовые параметры
                $model->created_at = date('Y-m-d H:i:s',time());
                $model->created_by_id = Yii::$app->user->id;

                //если не удалось сохранить
                if(!$model->save()){
                    //логировать ошибки валидации модели
                    Yii::info($model->getFirstErrors(),'info');
                }

                return $this->redirect(Url::to(['/admin/posters/update', 'id' => $model->id]));
            }
        }

        //вывести форму редактирования
        return $this->renderAjax('_create',compact('model'));
    }

    /**
     * Редактирование
     * @param $id
     * @return array|string
     * @throws NotFoundHttpException
     */
    public function actionUpdate($id)
    {
        /* @var $model Poster */
        $model = Poster::find()->where(['id' => (int)$id])->one();

        if(empty($model)){
            throw new NotFoundHttpException(Yii::t('app','Not found'),404);
        }

        //AJAX валидация
        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }

        if($model->load(Yii::$app->request->post()) && $model->validate()){

            //Если подтверждено супер-админом и админом группы
            if($model->isApprovedByAll()){
                //Перенести данные в "подтвержденные"
                $model->approveData(true);
            }

            $model->updated_by_id = Yii::$app->user->id;
            $model->updated_at = date('Y-m-d H:i:s');
            $model->save();
        }

        return $this->render('edit',compact('model'));
    }

    /**
     * Удаление
     * @param $id
     * @return Response
     * @throws NotFoundHttpException
     */
    public function actionDelete($id)
    {
        /* @var $model Poster */
        $model = Poster::find()->where(['id' => (int)$id])->one();

        if(empty($model)){
            throw new NotFoundHttpException(Yii::t('app','Not found'),404);
        }

        if(!empty($model->posterImages)){
            foreach ($model->posterImages as $image){
                $image->deleteImage();
            }
        }

        $model->delete();

        return $this->redirect(Yii::$app->request->referrer);
    }

    /**
     * Проверка (модерация) объявления
     * @param $id
     * @return array|string
     * @throws NotFoundHttpException
     */
    public function actionCheck($id)
    {
        /* @var $model Poster */
        $model = Poster::find()->where(['id' => (int)$id])->one();

        if(empty($model)){
            throw new NotFoundHttpException(Yii::t('app','Not found'),404);
        }

        //AJAX валидация
        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }

        if($model->load(Yii::$app->request->post()) && $model->validate()){

            //Если подтверждено супер-админом и админом группы
            if($model->isApprovedByAll()){
                //Перенести данные в "подтвержденные"
                $model->approveData(true);
            }

            $model->updated_by_id = Yii::$app->user->id;
            $model->updated_at = date('Y-m-d H:i:s');
            $model->save();

            return $this->redirect(Yii::$app->request->referrer);
        }

        return $this->renderAjax('_check',compact('model'));
    }

    /**
     * Загрузка изображения
     * @param $id
     * @return array|null
     * @throws NotFoundHttpException
     */
    public function actionUploadImage($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        /* @var $poster Poster */
        $poster = Poster::find()->where(['id' => (int)$id])->one();

        if(empty($poster)){
            throw new NotFoundHttpException(Yii::t('app','Not found'),404);
        }

        /* @var $image UploadedFile */
        $image = UploadedFile::getInstanceByName('filename');
        $uploadDir = Yii::getAlias('@webroot/upload/images/');

        if(!empty($image->size)){
            FileHelper::createDirectory($uploadDir);
            $filename = uniqid().'.'.$image->extension;
            $path = $uploadDir.$filename;

            if($image->saveAs($path)){

                $pi = new PosterImage();
                $pi -> poster_id = $poster->id;
                $pi -> filename = $filename;
                $pi -> priority = Sort::GetNextPriority(PosterImage::class,['poster_id' => $poster->id]);
                $pi -> main_pic = (int)false;
                $pi -> title = $image->name;
                $pi -> size = $image->size;
                $pi -> description = null;
                $pi -> status_id = Constants::STATUS_TEMPORARY;
                $pi -> created_at = date('Y-m-d H:i:s',time());
                $pi -> updated_at = date('Y-m-d H:i:s',time());
                $pi -> created_by_id = Yii::$app->user->id;
                $pi -> updated_by_id = Yii::$app->user->id;
                $pi -> save();

                return ['files' => [
                    [
                        'name' => $pi->title,
                        'size' => $pi->size,
                        'url' => Url::to("@web/upload/images/{$pi->filename}"),
                        'thumbnailUrl' => CropHelper::ThumbnailUrl($pi->filename,100,100),
                        'deleteUrl' => Url::to(['/admin/posters/delete-image', 'id' => $pi->id]),
                        'deleteType' => 'GET',
                    ]
                ]];
            }
        }

        return null;
    }

    /**
     * Удаление изображения
     * @param $id
     * @return array
     * @throws NotFoundHttpException
     */
    public function actionDeleteImage($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        /* @var $model PosterImage */
        $model = PosterImage::find()->where(['id' => (int)$id])->one();

        if(empty($model)){
            throw new NotFoundHttpException(Yii::t('app','Not found'),404);
        }

        $poster = $model->poster;

        $model->deleteImage();
        $model->delete();

        $poster->refresh();

        $items = [];
        foreach ($poster->posterImages as $posterImage){
            $items[] = [
                'name' => $posterImage->title,
                'size' => $posterImage->size,
                'url' => Url::to("@web/upload/images/{$posterImage->filename}"),
                'thumbnailUrl' => CropHelper::ThumbnailUrl($posterImage->filename,100,100),
                'deleteUrl' => Url::to(['/admin/posters/delete-image', 'id' => $posterImage->id]),
                'deleteType' => 'GET',
            ];
        }

        return ['files' => $items];
    }
}