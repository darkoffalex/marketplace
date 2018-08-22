<?php

namespace app\modules\admin\controllers;

use yii\web\ForbiddenHttpException;
use app\models\forms\SettingsForm;
use yii\helpers\Url;
use yii\web\Controller;
use Facebook\Facebook;
use Yii;

/**
 * Контроллер для управления общими настройками
 *
 * @copyright 	2018 Alex Nem
 * @link 		https://github.com/darkoffalex
 * @author 		Alex Nem
 *
 * @package app\modules\admin\controllers
 */
class SettingsController extends Controller
{
    /**
     * Общие настройки системы
     * @return string
     */
    public function actionIndex()
    {
        $model = SettingsForm::getInstance();

        if($model->load(Yii::$app->request->post()) && $model->validate()){
            $model->save(false);
        }

        return $this->render('index',compact('model'));
    }

    /**
     * Обновление TOKEN'а
     * @return \yii\web\Response
     * @throws ForbiddenHttpException
     */
    public function actionRefreshToken()
    {
        if(!session_id()) {
            session_start();
        }

        $fb = new Facebook([
            'app_id' => SettingsForm::getInstance()->fb_auth_client_id,
            'app_secret' => SettingsForm::getInstance()->fb_auth_app_secret
        ]);

        $helper = $fb->getRedirectLoginHelper();
        if(isset($_GET['state'])){
            $helper->getPersistentDataHandler()->set('state',$_GET['state']);
        }
        $accessToken = $helper->getAccessToken(Url::to(['/admin/settings/refresh-token'],true));

        if(empty($accessToken)){
            throw new ForbiddenHttpException("Can't retrieve access token",402);
        }

        $settings = SettingsForm::getInstance();
        $settings->fb_app_admin_token = $accessToken->getValue();
        $settings->save();

        return $this->redirect(Url::to(['/admin/settings/index']));
    }
}