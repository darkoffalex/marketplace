<?php
namespace app\modules\user\controllers;

use app\helpers\Constants;
use app\helpers\CropHelper;
use app\helpers\Sort;
use app\models\Poster;
use app\models\PosterImage;
use app\models\PosterSearch;
use yii\helpers\FileHelper;
use yii\helpers\Url;
use yii\web\Controller;
use Yii;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\UploadedFile;

/**
 * Class MessagesController
 * @package app\modules\user\controllers
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
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams,Yii::$app->user->id);
        return $this->render('index', compact('searchModel','dataProvider'));
    }

    /**
     * Редактировать
     * @param $id
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException
     */
    public function actionUpdate($id)
    {
        /* @var $model Poster */
        $model = Poster::find()->where(['id' => (int)$id, 'user_id' => Yii::$app->user->id])->one();
        $model->scenario = 'editing';

        if(empty($model)){
            throw new NotFoundHttpException(Yii::t('app','Not found'),404);
        }

        if($model->status_id == Constants::STATUS_TEMPORARY){
            $model->created_at = date('Y-m-d H:i:s',time());
            $model->created_by_id = Yii::$app->user->id;
        }

        if($model->load(Yii::$app->request->post()) && $model->validate()){

            if($model->hasNewChanges()){
                $model->approved_by_ga = (int)false;
                $model->approved_by_sa = (int)false;
            }

            if(empty($model->status_id) || $model->status_id == Constants::STATUS_TEMPORARY){
                $model->status_id = Constants::STATUS_ENABLED;
            }

            if(!empty($model->marketplace_tariff_id)){
                $model->period_seconds = $model->marketplaceTariff->tariff->getIntervalInSeconds();
            }

            $model->updated_at = date('Y-m-d H:i:s',time());
            $model->updated_by_id = Yii::$app->user->id;

            if($model->save()){
                return $this->redirect(Url::to(['/user/posters/payment', 'id' => $model->id]));
            }
        }

        return $this->render('edit',compact('model'));
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
        $poster = Poster::find()->where(['id' => (int)$id, 'user_id' => Yii::$app->user->id])->one();

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
                        'deleteUrl' => Url::to(['/user/posters/delete-image', 'id' => $pi->id]),
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
            ->joinWith('poster p')
            ->where(['i.id' => (int)$id, 'p.user_id' => Yii::$app->user->id])
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
                'deleteUrl' => Url::to(['/user/posters/delete-image', 'id' => $posterImage->id]),
                'deleteType' => 'GET',
            ];
        }

        return ['files' => $items];
    }
}