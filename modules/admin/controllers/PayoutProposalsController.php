<?php

namespace app\modules\admin\controllers;

use app\helpers\AccountsHelper;
use app\helpers\CropHelper;
use app\helpers\Sort;
use app\models\PayoutProposalImage;
use app\models\PayoutProposalSearch;
use yii\helpers\FileHelper;
use yii\helpers\Url;
use yii\web\Controller;
use app\models\PayoutProposal;
use yii\web\NotFoundHttpException;
use yii\bootstrap\ActiveForm;
use yii\web\Response;
use app\helpers\Constants;
use Yii;
use yii\web\UploadedFile;

/**
 * Контроллер для управления заявками на выплаты
 *
 * @copyright 	2018 Alex Nem
 * @link 		https://github.com/darkoffalex
 * @author 		Alex Nem
 *
 * @package app\modules\admin\controllers
 */
class PayoutProposalsController extends Controller
{
    /**
     * Список заявок
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new PayoutProposalSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render('index', compact('searchModel','dataProvider','account'));
    }

    /**
     * Обновление/ревью
     * @param $id
     * @return array|string|Response
     * @throws NotFoundHttpException
     */
    public function actionUpdate($id)
    {
        /* @var $model PayoutProposal */
        $model = PayoutProposal::find()->where(['id' => (int)$id])->one();

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
                $model->updated_at = date('Y-m-d H:i:s',time());

                //Если сохранено
                if($model->save()){

                    if($oldStatus == Constants::PAYMENT_STATUS_NEW){

                        //Если это отказ
                        if($model->status_id == Constants::PAYMENT_STATUS_CANCELED){
                            //Получить счет пользователя (админа групп) и счет вывода
                            $userAdminGroupAccount = $model->user->getMoneyAccount(Constants::GROUP_ADMIN_ACCOUNT);
                            $sysOutgoAccount = AccountsHelper::getSysOutgoAccount();
                            //Вернуть деньги
                            AccountsHelper::moveMoney($sysOutgoAccount->id,$userAdminGroupAccount->id,$model->transaction->amount);
                        }

                        //Обновть состояние связанной транзакции
                        $model->transaction->status_id = $model->status_id;
                        $model->transaction->updated_at = date('Y-m-d H:i:s',time());
                        $model->transaction->updated_by_id = Yii::$app->user->id;
                        $model->transaction->update();
                    }

                    return $this->redirect(Yii::$app->request->referrer);
                }
            }
        }

        return $this->renderAjax('_edit',compact('model'));
    }

    /**
     * Загрузка изображения
     * @param $id
     * @return array|null
     * @throws NotFoundHttpException
     */
    public function actionUploadImage($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        /* @var $proposal PayoutProposal */
        $proposal = PayoutProposal::find()->where(['id' => (int)$id])->one();

        if(empty($proposal)){
            throw new NotFoundHttpException(Yii::t('app','Not found'),404);
        }

        /* @var $image UploadedFile */
        $image = UploadedFile::getInstanceByName('filename');
        $uploadDir = Yii::getAlias('@webroot/upload/images/');

        if(!empty($image->size)){
            FileHelper::createDirectory($uploadDir);
            $filename = uniqid().'.'.$image->extension;
            $path = $uploadDir.$filename;

            if($image->saveAs($path)){

                $pi = new PayoutProposalImage();
                $pi -> proposal_id = $proposal->id;
                $pi -> filename = $filename;
                $pi -> priority = Sort::GetNextPriority(PayoutProposalImage::class,['proposal_id' => $proposal->id]);
                $pi -> title = $image->name;
                $pi -> size = $image->size;
                $pi -> description = null;
                $pi -> status_id = Constants::STATUS_TEMPORARY;
                $pi -> created_at = date('Y-m-d H:i:s',time());
                $pi -> updated_at = date('Y-m-d H:i:s',time());
                $pi -> created_by_id = Yii::$app->user->id;
                $pi -> updated_by_id = Yii::$app->user->id;
                $pi -> save();

                return ['files' => [
                    [
                        'name' => $pi->title,
                        'size' => $pi->size,
                        'url' => Url::to("@web/upload/images/{$pi->filename}"),
                        'thumbnailUrl' => CropHelper::ThumbnailUrl($pi->filename,100,100),
                        'deleteUrl' => Url::to(['/admin/payout-proposals/delete-image', 'id' => $pi->id]),
                        'deleteType' => 'GET',
                    ]
                ]];
            }
        }

        return null;
    }

    /**
     * Удаление изображения
     * @param $id
     * @return array
     * @throws NotFoundHttpException
     */
    public function actionDeleteImage($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        /* @var $model PayoutProposalImage */
        $model = PayoutProposalImage::findOne((int)$id);

        if(empty($model)){
            throw new NotFoundHttpException(Yii::t('app','Not found'),404);
        }

        $proposal = $model->proposal;

        $model->deleteImage();
        $model->delete();

        $proposal->refresh();
        return ['files' => $proposal->getImagesListed()];
    }
}