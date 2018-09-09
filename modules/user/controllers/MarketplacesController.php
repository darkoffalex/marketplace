<?php
namespace app\modules\user\controllers;

use app\helpers\Constants;
use app\helpers\Help;
use app\models\Marketplace;
use app\models\Poster;
use yii\helpers\Url;
use yii\web\Controller;
use app\models\MarketplaceSearch;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use app\models\forms\BindMarketplaceForm;
use kartik\form\ActiveForm;
use Yii;

/**
 * Class MessagesController
 * @package app\modules\user\controllers
 */
class MarketplacesController extends Controller
{
    /**
     * Список маркетплейсов
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new MarketplaceSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams,null, Yii::$app->user->id);
        return $this->render('index', compact('searchModel','dataProvider'));
    }

    /**
     * Привязка маркетплейса
     * @return array|string|Response
     */
    public function actionBind()
    {
        $model = new BindMarketplaceForm();

        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }

        if($model->load(Yii::$app->request->post()) && $model->validate()){
            $key = $model->key;
            $key->used_by_id = Yii::$app->user->id;
            $key->used_at = date('Y-m-d H:i:s',time());
            $key->save();

            return $this->redirect(Url::to(['/user/marketplaces/index']));
        }

        return $this->renderAjax('_bind',compact('model'));
    }

    /**
     * Создание нового объявления
     * @param $id
     * @return Response
     * @throws NotFoundHttpException
     */
    public function actionNewPoster($id)
    {
        /* @var $marketplace Marketplace */
        $marketplace = Marketplace::find()
            ->alias('mp')
            ->joinWith('marketplaceKeys k')
            ->where(['mp.id' => (int)$id, 'k.used_by_id' => Yii::$app->user->id])
            ->one();

        if(empty($marketplace)){
            throw new NotFoundHttpException(Yii::t('app','Marketplace unavailable'),404);
        }

        //Создать временное объявление
        $model = new Poster();
        $model->marketplace_id = $marketplace->id;
        $model->user_id = Yii::$app->user->id;
        $model->status_id = Constants::STATUS_TEMPORARY;
        $model->created_at = date('Y-m-d H:i:s',time());
        $model->created_by_id = Yii::$app->user->id;

        if(!$model->save()){
            Yii::info($model->errors,'info');
        }

        //Перейти к редактированию
        return $this->redirect(Url::to(['/user/posters/update','id' => $model->id]));
    }
}
