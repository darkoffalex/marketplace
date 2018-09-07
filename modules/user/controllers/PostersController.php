<?php
namespace app\modules\user\controllers;

use app\helpers\Constants;
use app\models\Poster;
use app\models\PosterSearch;
use yii\helpers\Url;
use yii\web\Controller;
use Yii;
use yii\web\NotFoundHttpException;

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
}