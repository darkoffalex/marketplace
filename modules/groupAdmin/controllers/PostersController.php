<?php
namespace app\modules\groupAdmin\controllers;

use app\helpers\CropHelper;
use app\helpers\Sort;
use app\models\Poster;
use app\models\PosterImage;
use Yii;
use app\models\PosterSearch;
use yii\helpers\FileHelper;
use yii\helpers\Url;
use yii\web\Controller;
use app\helpers\Constants;
use yii\web\NotFoundHttpException;
use yii\bootstrap\ActiveForm;
use yii\web\Response;
use yii\web\UploadedFile;

/**
 * Class MessagesController
 * @package app\modules\groupAdmin\controllers
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
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams,null,Yii::$app->user->id);
        return $this->render('index', compact('searchModel','dataProvider'));
    }

    /**
     * Редактировать
     * @param $id
     * @return array|string
     * @throws NotFoundHttpException
     */
    public function actionUpdate($id)
    {
        /* @var $model Poster */
        $model = Poster::find()->alias('p')
            ->joinWith('marketplace mp')
            ->where(['p.id' => (int)$id])
            ->andWhere('p.status_id != :excepted', ['excepted' => Constants::STATUS_TEMPORARY])
            ->andWhere(['mp.user_id' => Yii::$app->user->id])
            ->one();

        if(empty($model)){
            throw new NotFoundHttpException(Yii::t('app','Not found'),404);
        }

        //AJAX валидация
        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }

        $oldApproveStatus = $model->isApprovedByAll();
        $oldRefuseReason = $model->refuse_reason;

        if($model->load(Yii::$app->request->post()) && $model->validate()){

            //Если подтверждено супер-админом и админом группы
            if($model->isApprovedByAll()){
                //Перенести данные в "подтвержденные"
                $model->approveData(true);
                //Если ранее не было подтверждено - уведомить владельца объявления об одобрении
                if(empty($oldApproveStatus)){
                    $model->user->notifyAdvertisementConfirmation($model);
                }
            }
            //Если есть причина отклонения - уведомить
            elseif(!empty($model->refuse_reason) && $model->refuse_reason != $oldRefuseReason){
                $model->user->notifyAdvertisementConfirmation($model);
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
        $model = Poster::find()->alias('p')
            ->joinWith('marketplace mp')
            ->where(['p.id' => (int)$id])
            ->andWhere('p.status_id != :excepted', ['excepted' => Constants::STATUS_TEMPORARY])
            ->andWhere(['mp.user_id' => Yii::$app->user->id])
            ->one();

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
        $model = Poster::find()->alias('p')
            ->joinWith('marketplace mp')
            ->where(['p.id' => (int)$id])
            ->andWhere('p.status_id != :excepted', ['excepted' => Constants::STATUS_TEMPORARY])
            ->andWhere(['mp.user_id' => Yii::$app->user->id])
            ->one();

        if(empty($model)){
            throw new NotFoundHttpException(Yii::t('app','Not found'),404);
        }

        //AJAX валидация
        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }

        $oldApproveStatus = $model->isApprovedByAll();
        $oldRefuseReason = $model->refuse_reason;

        if($model->load(Yii::$app->request->post()) && $model->validate()){

            //Если подтверждено супер-админом и админом группы
            if($model->isApprovedByAll()){
                //Перенести данные в "подтвержденные"
                $model->approveData(true);
                //Если ранее не было подтверждено - уведомить владельца объявления об одобрении
                if(empty($oldApproveStatus)){
                    $model->user->notifyAdvertisementConfirmation($model);
                }
            }
            //Если есть причина отклонения - уведомить
            elseif(!empty($model->refuse_reason) && $model->refuse_reason != $oldRefuseReason){
                $model->user->notifyAdvertisementConfirmation($model);
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
        $poster = Poster::find()->alias('p')
            ->joinWith('marketplace mp')
            ->where(['p.id' => (int)$id])
            ->andWhere(['mp.user_id' => Yii::$app->user->id])
            ->one();

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
                        'deleteUrl' => Url::to(['/group-admin/posters/delete-image', 'id' => $pi->id]),
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
        $model = PosterImage::find()
            ->alias('i')
            ->joinWith('poster.marketplace mp')
            ->where(['i.id' => (int)$id, 'mp.user_id' => Yii::$app->user->id])
            ->one();

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
                'deleteUrl' => Url::to(['/group-admin/posters/delete-image', 'id' => $posterImage->id]),
                'deleteType' => 'GET',
            ];
        }

        return ['files' => $items];
    }
}