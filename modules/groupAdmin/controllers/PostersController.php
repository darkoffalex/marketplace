<?php
namespace app\modules\groupAdmin\controllers;

use app\models\Poster;
use app\models\PosterImage;
use Yii;
use app\models\PosterSearch;
use yii\web\Controller;
use app\helpers\Constants;
use yii\web\NotFoundHttpException;
use yii\bootstrap\ActiveForm;
use yii\web\Response;

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

        if($model->load(Yii::$app->request->post()) && $model->validate()){

            //Если подтверждено супер-админом и админом группы
            if($model->approved_by_sa && $model->approved_by_ga){

                //Перенести данные в "подтвержденные"
                $model->title_approved = $model->title;
                $model->description_approved = $model->description;
                $model->phone_approved = $model->phone;
                $model->whats_app_approved = $model->whats_app;

                //Опубликовано
                $model->published = (int)true;

                //Опубликовать и изображения
                PosterImage::updateAll(['status_id' => Constants::STATUS_ENABLED],['poster_id' => $model->id]);
            }

            $model->updated_by_id = Yii::$app->user->id;
            $model->updated_at = date('Y-m-d H:i:s');
            $model->save();

            return $this->redirect(Yii::$app->request->referrer);
        }

        return $this->renderAjax('_check',compact('model'));
    }
}