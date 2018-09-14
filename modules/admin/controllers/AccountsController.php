<?php

namespace app\modules\admin\controllers;

use app\helpers\AccountsHelper;
use app\helpers\Constants;
use app\helpers\Help;
use app\models\MoneyAccount;
use app\models\MoneyAccountSearch;
use app\models\MoneyTransactionSearch;
use app\models\MoneyTransaction;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\bootstrap\ActiveForm;
use yii\db\Query;
use Yii;

/**
 * Контроллер отвечающий за управление счетами
 *
 * @copyright 	2018 Alex Nem
 * @link 		https://github.com/darkoffalex
 * @author 		Alex Nem
 *
 * @package app\modules\admin\controllers
 */
class AccountsController extends Controller
{
    /**
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new MoneyAccountSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render('index', compact('searchModel','dataProvider'));
    }

    /**
     * История операций
     * @param $id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionHistory($id)
    {
        /* @var $account MoneyAccount */
        $account = MoneyAccount::findOne((int)$id);

        if(empty($account)){
            throw new NotFoundHttpException(Yii::t('app','Page not found'),404);
        }

        $searchModel = new MoneyTransactionSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, $account->id);
        return $this->renderAjax('_history', compact('searchModel','dataProvider','account'));
    }

    /**
     * Логирование вывода средств
     * @param $id
     * @return array|string
     * @throws NotFoundHttpException
     */
    public function actionWithdrawal($id)
    {
        /* @var $account MoneyAccount */
        $account = MoneyAccount::findOne((int)$id);

        if(empty($account)){
            throw new NotFoundHttpException(Yii::t('app','Page not found'),404);
        }

        $model = new MoneyTransaction();

        //AJAX валидация
        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            $model->amount = Help::toCents($model->amount);
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }

        if($model->load(Yii::$app->request->post())){
            $model->amount = Help::toCents($model->amount);
            if($model->validate()){
                $model -> from_account_id = $account->id;
                $model -> to_account_id = AccountsHelper::getSysOutgoAccount()->id;
                $model -> status_id = Constants::PAYMENT_STATUS_DONE;
                $model -> type_id = Constants::PAYMENT_INTERNAL_INITIATED;
                $model -> created_at = date('Y-m-d H:i:s',time());
                $model -> updated_at = date('Y-m-d H:i:s',time());
                $model -> created_by_id = Yii::$app->user->id;
                $model -> updated_by_id = Yii::$app->user->id;

                if($model ->save()){
                    $model->fromAccount->amount -= $model->amount;
                    $model->fromAccount->updated_at = date('Y-m-d H:i:s',time());
                    $model->fromAccount->updated_by_id = Yii::$app->user->id;
                    $model->fromAccount->save();

                    $model->toAccount->amount += $model->amount;
                    $model->toAccount->updated_at = date('Y-m-d H:i:s',time());
                    $model->toAccount->updated_by_id = Yii::$app->user->id;
                    $model->toAccount->save();

                    return $this->redirect(Yii::$app->request->referrer);
                }
            }
        }

        return $this->renderAjax('_withdrawal',compact('model'));
    }

    /**
     * AJAX поиск счетов для выпадающейго списка (по начальным символам имени владельца и ID'у)
     * @param null $q
     * @param null $id
     * @return array
     */
    public function actionAjaxSearch($q = null, $id = null)
    {
        //формат ответе - JSON
        Yii::$app->response->format = Response::FORMAT_JSON;

        //ответ по умолчанию
        $out = ['results' => ['id' => '', 'text' => '']];

        //если запрос не пуст
        if (!is_null($q)) {

            //сформировать запрос к базе
            $query = new Query();
            $query->select('ma.id, u.name, ma.account_type_id')->from('money_account ma')->leftJoin('user u','ma.user_id = u.id');

            $query->where(['like','u.name', $q])
                ->orWhere(['ma.id' => (int)$q])
                ->limit(20);

            //получить данные и сформировать ответ
            $command = $query->createCommand();
            $data = array_values($command->queryAll());

            $tmp = [];

            $typeNames = [
                Constants::SYSTEM_INCOME_ACCOUNT => Yii::t('app','System-income'),
                Constants::SYSTEM_OUTGO_ACCOUNT => Yii::t('app','System-outgo'),
                Constants::GROUP_ADMIN_ACCOUNT => Yii::t('app','Group-admin'),
                Constants::MEMBER_ACCOUNT => Yii::t('app','Advertiser'),
                Constants::MANAGER_ACCOUNT => Yii::t('app','Manager')
            ];

            foreach($data as $index => $arr){
                $name = !empty($arr['name']) ? $arr['name'] : Yii::t('app','SYSTEM');
                $typeName = ArrayHelper::getValue($typeNames, $arr['account_type_id']);
                $tmp[] = ['id' => $arr['id'], 'text' => $arr['id']." ({$name}) [{$typeName}]"];
            }

            $out['results'] = $tmp;
        }
        //если пуст запрос но указан ID
        elseif ($id > 0) {
            //найти по ID и сформировать ответ
            $account = MoneyAccount::findOne((int)$id);
            if(!empty($account)){
                $out['results'] = ['id' => $id, 'text' => $account->getFullName()];
            }
        }

        //вернуть ответ
        return $out;
    }
}