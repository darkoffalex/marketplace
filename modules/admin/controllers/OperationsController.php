<?php

namespace app\modules\admin\controllers;

use app\helpers\Constants;
use app\helpers\Help;
use app\models\MoneyTransaction;
use app\models\MoneyTransactionSearch;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\Response;
use kartik\form\ActiveForm;
use yii\web\NotFoundHttpException;
use Yii;

/**
 * Контроллер отвечающий за управление денежными операциями
 *
 * @copyright 	2018 Alex Nem
 * @link 		https://github.com/darkoffalex
 * @author 		Alex Nem
 *
 * @package app\modules\admin\controllers
 */
class OperationsController extends Controller
{
    /**
     * Вывод истории операций
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new MoneyTransactionSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render('index', compact('searchModel','dataProvider','account'));
    }

    /**
     * Создание операции
     * @return array|string|Response
     */
    public function actionCreate()
    {
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
                $model->updated_by_id = Yii::$app->user->id;
                $model->created_by_id = Yii::$app->user->id;
                $model->updated_at = date('Y-m-d H:i:s',time());
                $model->created_at = date('Y-m-d H:i:s',time());

                //Если статус установлен в "выполнено" - произвести начисление и списание
                if($model->save() && $model->status_id == Constants::PAYMENT_STATUS_DONE){

                    $model->fromAccount->amount -= $model->amount;
                    $model->fromAccount->updated_at = date('Y-m-d H:i:s',time());
                    $model->fromAccount->updated_by_id = Yii::$app->user->id;
                    $model->fromAccount->save();

                    $model->toAccount->amount += $model->amount;
                    $model->toAccount->updated_at = date('Y-m-d H:i:s',time());
                    $model->toAccount->updated_by_id = Yii::$app->user->id;
                    $model->toAccount->save();
                }

                return $this->redirect(Url::to(['/admin/operations/index']));
            }
        }

        return $this->renderAjax('_edit',compact('model'));
    }

    /**
     * Редактирование
     * @param $id
     * @return array|string|Response
     * @throws NotFoundHttpException
     */
    public function actionUpdate($id)
    {
        /* @var $model MoneyTransaction */
        $model = MoneyTransaction::findOne((int)$id);

        if(empty($model)){
            throw new NotFoundHttpException(Yii::t('app','Page not found'),404);
        }

        //AJAX валидация
        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }

        $oldStatus = $model->status_id;

        if($model->load(Yii::$app->request->post())){
            if($model->validate()){
                $model->updated_by_id = Yii::$app->user->id;
                $model->created_at = date('Y-m-d H:i:s',time());

                //Если статус был сменен на "выполнено" - произвести начисление и списание
                if($model->save() && $model->status_id == Constants::PAYMENT_STATUS_DONE && $oldStatus != $model->status_id){

                    $model->fromAccount->amount -= $model->amount;
                    $model->fromAccount->updated_at = date('Y-m-d H:i:s',time());
                    $model->fromAccount->updated_by_id = Yii::$app->user->id;
                    $model->fromAccount->save();

                    $model->toAccount->amount += $model->amount;
                    $model->toAccount->updated_at = date('Y-m-d H:i:s',time());
                    $model->toAccount->updated_by_id = Yii::$app->user->id;
                    $model->toAccount->save();
                }

                return $this->redirect(Url::to(['/admin/operations/index']));
            }
        }

        return $this->renderAjax('_edit',compact('model'));
    }

    /**
     * Удаление
     * @param $id
     * @return Response
     * @throws NotFoundHttpException
     */
    public function actionDelete($id)
    {
        /* @var $model MoneyTransaction */
        $model = MoneyTransaction::findOne((int)$id);

        if(empty($model)){
            throw new NotFoundHttpException(Yii::t('app','Page not found'),404);
        }

        $model->delete();
        return $this->redirect(Yii::$app->request->referrer);
    }
}