<?php

namespace app\modules\user\controllers;

use app\helpers\FileLoad;
use app\models\ShortLinkSearch;
use app\models\User;
use Yii;
use app\helpers\Constants;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;
use app\components\Controller;
use app\models\ShortLink;
use yii\web\Response;
use yii\web\UploadedFile;

/**
 * Спиок сокращенных ссылок
 *
 * @copyright 	2018 Alex Nem
 * @link 		https://github.com/darkoffalex
 * @author 		Alex Nem
 *
 * @package app\modules\user\controllers
 */
class ShortLinksController extends Controller
{
    /**
     * Главная страница
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new ShortLinkSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, Yii::$app->user->id);
        return $this->render('index', compact('searchModel','dataProvider'));
    }

    /**
     * Создание
     * @return array|string
     * @throws NotFoundHttpException
     */
    public function actionCreate()
    {
        /* @var $user User */
        $user = Yii::$app->user->identity;

        //Если пользователь не подтвержден и у него есть ссылки - нельзя создать
        if(!$user->isApprovedMember() && $user->getShortLinks()->count() > 0){
            throw new NotFoundHttpException(Yii::t('app','Page not found'),404);
        }

        $model = new ShortLink();
        $model->user_id = Yii::$app->user->id;
        $model->status_id = Constants::STATUS_ENABLED;
        $model->type_id = Constants::SHORT_LINK_REGULAR;

        //AJAX валидация
        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            $model->scenario = $model->type_id == Constants::SHORT_LINK_REGULAR ? 'editing_re' : 'editing_wa';
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }

        //если пришли данные из POST и они успешно заружены в объект
        if (Yii::$app->request->isPost && $model->load(Yii::$app->request->post())) {

            //Выбор цсценария валидации в зависимости от типа
            $model->scenario = $model->type_id == Constants::SHORT_LINK_REGULAR ? 'editing_re' : 'editing_wa';

            //Загрузка изображение
            $model->image = UploadedFile::getInstance($model,'image');

            // Если это watsApp - сформировать спец-ссылку
            if($model->type_id == Constants::SHORT_LINK_WHATSAPP){
                $phone = str_replace('+','',$model->phone);
                $model->original_link = "https://api.whatsapp.com/send?phone={$phone}&text={$model->message}";
            }else{
                $model->phone = null;
            }

            //если все данные в итоге корректны
            if($model->validate()){

                //Сохранение изображения
                FileLoad::loadAndClearOld($model,'image','image_file',!empty($model->image_file));

                //базовые параметры
                $model->created_at = date('Y-m-d H:i:s',time());

                //если не удалось сохранить
                if(!$model->save()){
                    //логировать ошибки валидации модели
                    Yii::info($model->getFirstErrors(),'info');
                }

                //Получение ключа
                $model->ObtainKey();

                //к списку
                $this->redirect(Url::to(['/short-links/index']));
            }
        }

        //вывести форму редактирования
        return $this->renderAjax('_edit',compact('model'));
    }

    /**
     * Редактировать
     * @param $id
     * @return array|string
     * @throws NotFoundHttpException
     */
    public function actionUpdate($id)
    {
        /* @var $model ShortLink */
        $model = ShortLink::find()->where(['id' => (int)$id])->one();

        if(empty($model)){
            throw new NotFoundHttpException(Yii::t('app','Not found'),404);
        }

        //AJAX валидация
        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            $model->scenario = $model->type_id == Constants::SHORT_LINK_REGULAR ? 'editing_re' : 'editing_wa';
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }

        //если пришли данные из POST и они успешно заружены в объект
        if (Yii::$app->request->isPost && $model->load(Yii::$app->request->post())) {

            //Выбор цсценария валидации в зависимости от типа
            $model->scenario = $model->type_id == Constants::SHORT_LINK_REGULAR ? 'editing_re' : 'editing_wa';

            //Загрузка изображение
            $model->image = UploadedFile::getInstance($model,'image');

            // Если это watsApp - сформировать спец-ссылку
            if($model->type_id == Constants::SHORT_LINK_WHATSAPP){
                $phone = str_replace('+','',$model->phone);
                $model->original_link = "https://api.whatsapp.com/send?phone={$phone}&text={$model->message}";
            }else{
                $model->phone = null;
            }

            //Получение ключа
            $model->ObtainKey();

            //если все данные в итоге корректны
            if($model->validate()){

                //Сохранение изображения
                FileLoad::loadAndClearOld($model,'image','image_file',!empty($model->image_file));

                //если не удалось сохранить
                if(!$model->save()){
                    //логировать ошибки валидации модели
                    Yii::info($model->getFirstErrors(),'info');
                }

                //к списку
                $this->redirect(Url::to(['/short-links/index']));
            }
        }

        //вывести форму редактирования
        return $this->renderAjax('_edit',compact('model'));
    }

    /**
     * Удаление уведомления
     * @param int $id
     * @return \yii\web\Response
     * @throws NotFoundHttpException
     */
    public function actionDelete($id)
    {
        /* @var $model ShortLink */
        $model = ShortLink::find()
            ->where(['id' => (int)$id, 'user_id' => Yii::$app->user->id])
            ->one();

        if(empty($model)){
            throw new NotFoundHttpException(Yii::t('app','Page not found'),404);
        }

        $model->delete();
        return $this->redirect(Yii::$app->request->referrer);
    }

    /**
     * Смена статуса из списка (ajax)
     * @param $id
     * @param $status
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionStatusChange($id, $status)
    {
        Yii::$app->response->format = Response::FORMAT_RAW;

        /* @var $model ShortLink */
        $model = ShortLink::find()->where(['id' => (int)$id, 'user_id' => Yii::$app->user->id])->one();

        //если не найден
        if(empty($model)){
            throw new NotFoundHttpException(Yii::t('app','Page not found'),404);
        }

        //установить в зависимости от значения status
        $model->status_id = $status == 'true' ? Constants::STATUS_ENABLED : Constants::STATUS_DISABLED;
        if($model->save()){
            return 'OK';
        }

        return 'FAILED';
    }

    /**
     * Удалить изображение
     * @param $id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionDeleteImage($id)
    {
        Yii::$app->response->format = Response::FORMAT_RAW;

        /* @var $model ShortLink */
        $model = ShortLink::find()->where(['id' => (int)$id, 'user_id' => Yii::$app->user->id])->one();

        //если не найден
        if(empty($model)){
            throw new NotFoundHttpException('Page not found',404);
        }

        FileLoad::deleteFile($model,'image_file');
        $model->image_file = null;
        $model->save();

        return 'OK';
    }
}