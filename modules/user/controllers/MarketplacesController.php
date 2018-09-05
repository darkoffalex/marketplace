<?php
namespace app\modules\user\controllers;

use app\helpers\Help;
use yii\helpers\Url;
use yii\web\Controller;
use app\models\MarketplaceSearch;
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
}
