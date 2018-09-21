<?php
namespace app\modules\user\controllers;

use app\helpers\AccountsHelper;
use app\models\MoneyAccount;
use app\models\User;
use Yii;
use app\helpers\Constants;
use app\helpers\CropHelper;
use app\helpers\Sort;
use app\models\Poster;
use app\models\PosterImage;
use app\models\PosterSearch;
use yii\helpers\FileHelper;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\Response;
use yii\web\UploadedFile;
use yii\bootstrap\ActiveForm;
use yii\web\NotFoundHttpException;

/**
 * Class MessagesController
 * @package app\modules\user\controllers
 */
class PostersController extends Controller
{
    /**
     * Список объявлений
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new PosterSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams,Yii::$app->user->id);
        return $this->render('index', compact('searchModel','dataProvider'));
    }

    /**
     * Редактировать/создать
     * Важным моментом являтся то, что создание (добавление объекта в базу) уже должно быть произведено ранее
     * @param $id
     * @return array|string|Response
     * @throws NotFoundHttpException
     */
    public function actionUpdate($id)
    {
        /* @var $model Poster */
        $model = Poster::find()->where(['id' => (int)$id, 'user_id' => Yii::$app->user->id])->one();

        if(empty($model)){
            throw new NotFoundHttpException(Yii::t('app','Not found'),404);
        }

        $model->scenario = 'editing';

        //Если маркетплейс доверенный - автоматически подтвердить супер-админом
        if($model->marketplace->trusted){
            $model->approved_by_sa = (int)true;
        }

        //Если о объявления временный статус - значит оно создается в данный момент (не редактируется)
        if($model->status_id == Constants::STATUS_TEMPORARY){
            $model->created_at = date('Y-m-d H:i:s',time());
            $model->created_by_id = Yii::$app->user->id;
            $model->scenario = 'creating';
        }

        //AJAX валидация
        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }

        //Загрузка данных из POST и их валидация
        if($model->load(Yii::$app->request->post()) && $model->validate()){

            //Если есть изменения в основных полях - сбросить подтверждения
            if($model->hasNewChanges()){
                $model->approved_by_ga = (int)false;
                $model->approved_by_sa = (int)$model->marketplace->trusted;
            }

            //Если статус по каком-то причинам все еще времнный - перевести в "активный"
            if(empty($model->status_id) || $model->status_id == Constants::STATUS_TEMPORARY){
                $model->status_id = Constants::STATUS_ENABLED;
            }

            //Получить интервал по выбранному тарифу (устанавливается только в случае создания)
            if(!empty($model->marketplace_tariff_id) && $model->scenario == 'creating'){
                $model->period_seconds = $model->marketplaceTariff->tariff->getIntervalInSeconds();
            }

            $model->updated_at = date('Y-m-d H:i:s',time());
            $model->updated_by_id = Yii::$app->user->id;
            $model->save();

            //Если это создание - перенаправляем на оплату
            if($model->scenario == 'creating'){
                return $this->redirect(Url::to(['/user/posters/payment', 'id' => $model->id]));
            }
        }

        //Вывод формы
        return $this->render('edit',compact('model'));
    }

    /**
     * Удаление
     * @param $id
     * @return Response
     * @throws NotFoundHttpException
     */
    public function actionDelete($id)
    {
        /* @var $model Poster */
        $model = Poster::find()->where(['id' => (int)$id, 'user_id' => Yii::$app->user->id])->one();

        if(empty($model)){
            throw new NotFoundHttpException(Yii::t('app','Not found'),404);
        }

        if(!empty($model->posterImages)){
            foreach ($model->posterImages as $image){
                $image->deleteImage();
            }
        }

        $model->delete();
        return $this->redirect(Yii::$app->request->referrer);
    }

    /**
     * Предпросмотр (в модальном окне)
     * @param $id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionPreview($id)
    {
        /* @var $model Poster */
        $model = Poster::find()->where(['id' => (int)$id, 'user_id' => Yii::$app->user->id])->one();

        if(empty($model)){
            throw new NotFoundHttpException(Yii::t('app','Not found'),404);
        }

        $model->load(Yii::$app->request->get());

        return $this->renderAjax('_preview',  compact('model'));
    }

    /**
     * Показать причину отказа
     * @param $id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionReason($id)
    {
        /* @var $model Poster */
        $model = Poster::find()->where(['id' => (int)$id, 'user_id' => Yii::$app->user->id])->one();

        if(empty($model)){
            throw new NotFoundHttpException(Yii::t('app','Not found'),404);
        }

        $model->load(Yii::$app->request->get());

        return $this->renderAjax('_reason',  compact('model'));
    }

    /**
     * Страница оплаты
     * @param $id
     * @return array|string|Response
     * @throws NotFoundHttpException
     */
    public function actionPayment($id)
    {
        /* @var $model Poster */
        $model = Poster::find()->where(['id' => (int)$id, 'user_id' => Yii::$app->user->id])->one();

        if(empty($model) || $model->status_id == Constants::STATUS_TEMPORARY || $model->isPaid()){
            throw new NotFoundHttpException(Yii::t('app','Not found'),404);
        }

        //Если выбран тариф типа "пост от админа" и время публикации не подтверждено - выводить форму выбора времени публикации
        if($model->marketplaceTariff->tariff->special_type == Constants::TARIFF_SUB_TYPE_ADMIN_POST && $model->admin_post_time_approve_status != Constants::ADMIN_POST_TIME_APPROVED)
        {
            //AJAX валидация
            if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return ActiveForm::validate($model);
            }

            //Если информация была загружена в модель
            if($model->load(Yii::$app->request->post()) && $model->validate()){
                $model->updated_at = date('Y-m-d H:i:s',time());
                $model->updated_by_id = Yii::$app->user->id;
                $model->save();
            }

            //Вывод формы
            return $this->render('payment-admin-post-configure', compact('model'));
        }

        //Тестовый режим
        //В этом месте происходит эмитация оплаты
        //TODO: убрать этот блок в дальнейшем
        if(Yii::$app->request->post('test-mode')){

            //Если есть цена
            if(!empty($model->marketplaceTariff->price)){
                /* @var $user User */
                $user = Yii::$app->user->identity;
                /* @var $memberAccount MoneyAccount */
                $memberAccount = $user->getMoneyAccount(Constants::MEMBER_ACCOUNT);
                /* @var $sysIncomeAccount MoneyAccount */
                $sysIncomeAccount = AccountsHelper::getSysIncomeAccount();
                /* @var $groupAdminAccount MoneyAccount */
                $groupAdminAccount = $model->marketplace->user->getMoneyAccount(Constants::GROUP_ADMIN_ACCOUNT);

                // Зачисление суммы на системный счет (это должно будет происходить после подтверждения платежа)
                $mainOperation = AccountsHelper::makeOperation(
                    $memberAccount->id,
                    $sysIncomeAccount->id,
                    $model->marketplaceTariff->getActivePrice(),
                    Constants::PAYMENT_STATUS_DONE,
                    Constants::PAYMENT_WEB_INITIATED,
                    null,
                    Yii::t('app','Payment for advertisement «{name}» ({rateName})',['name' => $model->title, 'rateName' => $model->marketplaceTariff->getNameWithDetails()]));

                // Если процент обладателя маркетплейсом больше ноля - ему нужно зачилить (на счет админа группы) процент от суммы
                if(!empty($mainOperation) && $model->marketplace->user->ag_income_percentage > 0){
                    $percentAmount = (int)($mainOperation->amount / 100 * $model->marketplace->user->ag_income_percentage);
                    AccountsHelper::makeOperation(
                        $sysIncomeAccount->id,
                        $groupAdminAccount->id,
                        $percentAmount,
                        Constants::PAYMENT_STATUS_DONE,
                        Constants::PAYMENT_INTERNAL_INITIATED,
                        null,
                        Yii::t('app','Payment for advertisement «{name}» ({id})', ['name' => $model->title, 'id' => $model->id]));
                }
            }

            $model->paid_at = date('Y-m-d H:i:s');
            $model->save();

            return $this->redirect(Url::to(['/user/posters/payment-done', 'id' => $model->id]));
        }

        return $this->render('payment-regular',compact('model'));
    }

    /**
     * Страница "после оплаты"
     * @param $id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionPaymentDone($id)
    {
        /* @var $model Poster */
        $model = Poster::find()->where(['id' => (int)$id, 'user_id' => Yii::$app->user->id])->one();

        if(empty($model) || !$model->isPaid()){
            throw new NotFoundHttpException(Yii::t('app','Not found'),404);
        }

        return $this->render('payment-done',compact('model'));
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

        /* @var $poster Poster */
        $poster = Poster::find()->where(['id' => (int)$id, 'user_id' => Yii::$app->user->id])->one();

        if(empty($poster)){
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

                $pi = new PosterImage();
                $pi -> poster_id = $poster->id;
                $pi -> filename = $filename;
                $pi -> priority = Sort::GetNextPriority(PosterImage::class,['poster_id' => $poster->id]);
                $pi -> main_pic = (int)false;
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
                        'deleteUrl' => Url::to(['/user/posters/delete-image', 'id' => $pi->id]),
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

        /* @var $model PosterImage */
        $model = PosterImage::find()
            ->alias('i')
            ->joinWith('poster p')
            ->where(['i.id' => (int)$id, 'p.user_id' => Yii::$app->user->id])
            ->one();

        if(empty($model)){
            throw new NotFoundHttpException(Yii::t('app','Not found'),404);
        }

        $poster = $model->poster;

        $model->deleteImage();
        $model->delete();

        $poster->refresh();

        $items = [];
        foreach ($poster->posterImages as $posterImage){
            $items[] = [
                'name' => $posterImage->title,
                'size' => $posterImage->size,
                'url' => Url::to("@web/upload/images/{$posterImage->filename}"),
                'thumbnailUrl' => CropHelper::ThumbnailUrl($posterImage->filename,100,100),
                'deleteUrl' => Url::to(['/user/posters/delete-image', 'id' => $posterImage->id]),
                'deleteType' => 'GET',
            ];
        }

        return ['files' => $items];
    }
}