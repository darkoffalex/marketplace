<?php
namespace app\modules\user\controllers;

use app\models\MoneyTransactionSearch;
use app\models\User;
use Yii;
use yii\web\Controller;

/**
 * Class OperationsController
 * @package app\modules\user\controllers
 */
class OperationsController extends Controller
{
    /**
     * Список всех операций пользователя
     * @return string
     */
    public function actionIndex()
    {
        /* @var $user User */
        $user = Yii::$app->user->identity;

        $searchModel = new MoneyTransactionSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, $user->getMoneyAccount()->id);
        return $this->render('index', compact('searchModel','dataProvider'));
    }
}