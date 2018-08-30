<?php
namespace app\modules\groupAdmin\controllers;

use app\models\MarketplaceSearch;
use yii\web\Controller;
use Yii;
/**
 * Class MessagesController
 * @package app\modules\groupAdmin\controllers
 */
class MarketplacesController extends Controller
{
    /**
     * Сообщение о недоступности опции мониторинга
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new MarketplaceSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams,Yii::$app->user->id);
        return $this->render('index', compact('searchModel','dataProvider'));
    }
}
