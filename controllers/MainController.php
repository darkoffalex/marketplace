<?php

namespace app\controllers;

use app\helpers\Constants;
use app\models\forms\SettingsForm;
use app\models\User;
use app\components\Controller;
use Facebook\Facebook;
use Yii;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\web\ServerErrorHttpException;
use yii\db\Exception;

/**
 * Базовый контроллер для приложения
 *
 * @copyright 	2017 Alex Nem
 * @link 		https://github.com/darkoffalex
 * @author 		Alex Nem
 *
 * @package app\controllers
 */
class MainController extends Controller
{
    /**
     * Обработчик ошибок (эмуляция action'а error)
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    /**
     * Главная страница
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * Выход
     * @return \yii\web\Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();
        return $this->redirect(Url::to(['/main/index']));
    }

    /**
     * Авторизация через Facebook
     * @return \yii\web\Response
     * @throws ServerErrorHttpException
     */
    public function actionAuthFbGroupAdmin()
    {
        if (!session_id()) {
            session_start();
        }

        try{
            $fb = new Facebook([
                'app_id' => SettingsForm::getInstance()->fb_auth_client_id,
                'app_secret' => SettingsForm::getInstance()->fb_auth_app_secret,
            ]);

            $helper = $fb->getRedirectLoginHelper();

            //Хотфикс
            //if (isset($_GET['state'])) {
            //    $helper->getPersistentDataHandler()->set('state', $_GET['state']);
            //}

            $accessToken = $helper->getAccessToken(Url::to(['/main/auth-fb-group-admin', 'language' => null], true));

            if(empty($accessToken)){
                throw new Exception("Can't retrieve token",500);
            }

            //Запросить базовые данные пользователя
            $response = $fb->get('/me?fields=id,name,first_name,last_name,email,third_party_id,picture,groups.limit(100),accounts.limit(100)', $accessToken);
            $baseUserData = $response->getGraphUser()->asArray();
            $avatarData = $response->getGraphUser()->getPicture();

            //ID пользователя в области видимости приложения (не глобальный)
            $appScopedUID = ArrayHelper::getValue($baseUserData, 'id');

            //Если ID по каким-то мистическим причинам пуст - ошибка
            if (empty($appScopedUID)) {
                throw new \Exception("Cant't find UID in received data", 500);
            }

            /* @var $user User */
            $user = User::find()->where(['facebook_id' => $appScopedUID])->one();

            if(empty($user)){
                $user = new User();
                $user->username = $appScopedUID;
                $user->facebook_id = $appScopedUID;
                $user->name = ArrayHelper::getValue($baseUserData, 'name');
                $user->role_id = Constants::ROLE_USER;
                $user->status_id = Constants::STATUS_ENABLED;
                $user->created_at = date('Y-m-d H:i:s');
                $user->created_by_id = 0;
            }

            $user->avatar_url = $avatarData->getUrl();
            $user->updated_at = date('Y-m-d H:i:s');
            $user->last_login_at = date('Y-m-d H:i:s');
            $user->updated_by_id = 0;
            $user->facebook_token = $accessToken->getValue();
            $user->save();

            Yii::$app->user->login($user);

            return $this->redirect(Url::to(['/group-admin/main/index']));

        }catch (\Exception $ex){
            throw new ServerErrorHttpException($ex->getMessage(),500);
        }
    }
}
