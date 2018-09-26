<?php

namespace app\modules\admin\controllers;

use app\helpers\Constants;
use app\helpers\Help;
use app\models\Cv;
use app\models\CvSearch;
use app\models\Marketplace;
use Yii;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * Контроллер отвечающий за управление заявками
 *
 * @copyright 	2018 Alex Nem
 * @link 		https://github.com/darkoffalex
 * @author 		Alex Nem
 *
 * @package app\modules\admin\controllers
 */
class CvsController extends Controller
{
    /**
     * Список все заявок
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new CvSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render('index', compact('searchModel','dataProvider'));
    }

    /**
     * Просмотр и одобрение заявки
     * @param $id
     * @return array|string
     * @throws NotFoundHttpException
     */
    public function actionUpdate($id)
    {
        /* @var Cv $model */
        $model = Cv::findOne((int)$id);

        //Если не найден
        if(empty($model)){
            throw new NotFoundHttpException('Page not found',404);
        }

        //AJAX валидация
        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }

        //Старый статус
        $oldStatus = $model->status_id;

        //Если пришли данные из POST и они успешно заружены в объект
        if (Yii::$app->request->isPost && $model->load(Yii::$app->request->post())) {

            //Если все данные в итоге корректны
            if($model->validate()){

                //Базовые параметры, обновить
                $model->updated_at = date('Y-m-d H:i:s',time());
                $model->updated_by_id = Yii::$app->user->id;
                $model->update();

                //Если изменился статус - уведомить владельца
                if($oldStatus != $model->status_id && $model->status_id != Constants::CV_STATUS_NEW){
                    $model->user->notifyFbMarketplaceConfirmation($model);
                }

                //Если нужно создать маркетплейс по заявке
                //У заявки не должно быть маркетплейсов и статус должен быть "одобрено"
                if($model->create_marketplace && empty($model->marketplaces) && $model->status_id == Constants::CV_STATUS_APPROVED)
                {
                    //Создать маркетплейс для этого пользователя
                    $marketplace = new Marketplace();
                    $marketplace->cv_id = $model->id;
                    $marketplace->name = $model->group_name;

                    $marketplace->group_url = $model->group_url;
                    $marketplace->group_admin_profile = $model->group_admin_profile;
                    $marketplace->group_description = $model->group_description;
                    $marketplace->group_popularity = $model->group_popularity;
                    $marketplace->group_thematics = $model->group_thematics;
                    $marketplace->domain_alias = Help::slug($model->group_name);
                    $marketplace->status_id = Constants::STATUS_DISABLED;

                    $marketplace->timezone = $model->timezone;
                    $marketplace->user_id = $model->user_id;
                    $marketplace->country_id = $model->country_id;
                    $marketplace->geo = $model->group_geo;
                    $marketplace->created_at = date('Y-m-d H:i:s',time());
                    $marketplace->updated_at = date('Y-m-d H:i:s',time());
                    $marketplace->created_by_id = Yii::$app->user->id;
                    $marketplace->updated_by_id = Yii::$app->user->id;
                    $marketplace->save();

                    //Перейти к редактированию маркетплейса
                    return $this->redirect(Url::to(['/admin/marketplaces/update', 'id' => $marketplace->id]));
                }

                //К списку
                $this->redirect(Url::to(['/admin/cvs/index']));
            }
        }

        //Вывести форму редактирования
        return $this->renderAjax('_edit',compact('model','flags'));
    }

    /**
     * Удаление заявки
     * @param $id
     * @throws NotFoundHttpException
     */
    public function actionDelete($id)
    {
        /* @var Cv $model */
        $model = Cv::findOne((int)$id);

        //если не найден
        if(empty($model)){
            throw new NotFoundHttpException('Page not found',404);
        }

        //удаление из базы
        $model->delete();

        //к списку
        $this->redirect(Url::to(['/admin/cvs/index']));
    }
}