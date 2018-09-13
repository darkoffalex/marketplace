<?php
namespace app\modules\groupAdmin\controllers;

use app\helpers\Constants;
use app\models\MoneyAccount;
use app\models\MoneyTransactionSearch;
use app\models\User;
use Yii;
use yii\web\Controller;

/**
 * Class OperationsController
 * @package app\modules\groupAdmin\controllers
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
        /* @var $account MoneyAccount */
        $account = $user->getMoneyAccount(Constants::GROUP_ADMIN_ACCOUNT);

        $searchModel = new MoneyTransactionSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, $account->id);
        return $this->render('index', compact('searchModel','dataProvider','account'));
    }
}