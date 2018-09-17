<?php
namespace app\modules\groupAdmin\controllers;

use app\helpers\Constants;
use app\models\MoneyAccount;
use app\models\MoneyTransactionSearch;
use app\models\User;
use Yii;
use yii\web\Controller;
use app\models\PayoutProposal;
use app\models\forms\SettingsForm;
use app\helpers\AccountsHelper;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use app\helpers\Help;
use yii\bootstrap\ActiveForm;
use app\models\MoneyTransaction;
use yii\helpers\Url;

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

    /**
     * Создание заявки на выплату средств
     * @return array|string|Response
     */
    public function actionNewProposal()
    {
        /* @var $user User */
        $user = Yii::$app->user->identity;

        /* @var $account MoneyAccount */
        $account = $user->getMoneyAccount(Constants::GROUP_ADMIN_ACCOUNT);

        if($account->amount < SettingsForm::getInstance()->payout_min_sum){
            return $this->renderAjax('_proposal_error');
        }

        //Новая заявка (связана с пользователем)
        $model = new PayoutProposal();
        $model -> user_id = Yii::$app->user->id;
        $model -> scenario = 'creating';

        //AJAX валидация
        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            $model->amount = Help::toCents($model->amount);
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }

        //Если есть данные формы
        if($model->load(Yii::$app->request->post())){

            //Перевести сумму в центы
            $model->amount = Help::toCents($model->amount);

            //Если прошло валидацию
            if($model->validate()){

                //Создать транзакцию
                $transaction = new MoneyTransaction();
                $transaction->from_account_id = $account->id;
                $transaction->to_account_id = AccountsHelper::getSysOutgoAccount()->id;
                $transaction->amount = $model->amount;
                $transaction->created_at = date('Y-m-d H:i:s',time());
                $transaction->updated_at = date('Y-m-d H:i:s',time());
                $transaction->created_by_id = Yii::$app->user->id;
                $transaction->updated_by_id = Yii::$app->user->id;
                $transaction->type_id = Constants::PAYMENT_INTERNAL_INITIATED;
                $transaction->status_id = Constants::PAYMENT_STATUS_NEW;
                $transaction->description = Yii::t('app','Withdrawal initiated by user');
                $transaction->web_payment_type_id = null;

                //Если транзация была создана
                if($transaction->save()){

                    //Создать заявку (связать с транзакцией)
                    $model->transaction_id = $transaction->id;
                    $model->status_id = Constants::PAYMENT_STATUS_NEW;
                    $model->created_at = date('Y-m-d H:i:s',time());
                    $model->updated_at = date('Y-m-d H:i:s',time());
                    $model->created_by_id = Yii::$app->user->id;
                    $model->updated_by_id = Yii::$app->user->id;
                    $model->save();

                    //Сделать перевод средств на счет вывода (средства будут возрващены в случае отклонения заявки)
                    AccountsHelper::moveMoney($transaction->from_account_id,$transaction->to_account_id,$transaction->amount);

                    //Вернуться на главную страницу
                    return $this->redirect(Url::to(['/group-admin/operations/index']));
                }
            }
        }

        return $this->renderAjax('_proposal_create',compact('model'));
    }

    /**
     * Информация о заявке
     * @param $id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionProposalInfo($id)
    {
        /* @var $model PayoutProposal */
        $model = PayoutProposal::find()
            ->where(['id' => (int)$id, 'user_id' => Yii::$app->user->id])
            ->one();

        //если не найден
        if(empty($model)){
            throw new NotFoundHttpException('Page not found',404);
        }

        return $this->renderAjax('_proposal_info',compact('model'));
    }

    /**
     * Удаление заявки и возврат средств
     * @param $id
     * @return Response
     * @throws NotFoundHttpException
     */
    public function actionProposalDelete($id)
    {
        /* @var $model PayoutProposal */
        $model = PayoutProposal::find()
            ->where(['id' => (int)$id, 'user_id' => Yii::$app->user->id, 'status_id' => [Constants::PAYMENT_STATUS_NEW,Constants::PAYMENT_STATUS_CANCELED]])
            ->one();

        //если не найден
        if(empty($model)){
            throw new NotFoundHttpException('Page not found',404);
        }

        //Если это новая (не обработанная заявка) - нужно вернуть средства назад
        //перед удалением
        if($model->status_id == Constants::PAYMENT_STATUS_NEW){

            //Идентификаторы счетов
            $transaction = $model->transaction;
            $srcAccountID = $transaction->from_account_id;
            $dstAccountID = $transaction->to_account_id;

            //Вернуть средства
            AccountsHelper::moveMoney($dstAccountID,$srcAccountID,$transaction->amount);

            //Удалить транзакцию
            $transaction->delete();
        }

        //Удалть
        $model->delete();

        //Вернуться на главную страницу
        return $this->redirect(Url::to(['/group-admin/operations/index']));
    }
}