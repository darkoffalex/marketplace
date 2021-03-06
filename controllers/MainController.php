<?php

namespace app\controllers;

use app\helpers\Constants;
use app\helpers\Help;
use app\models\forms\SettingsForm;
use app\models\User;
use app\components\Controller;
use Facebook\Facebook;
use Yii;
use app\models\ShortLink;
use yii\web\NotFoundHttpException;
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
     * Общая авторизация через FB
     * @param $redirectUrl
     * @return bool
     */
    private function facebookAuthentication($redirectUrl)
    {
        try{
            $fb = new Facebook([
                'app_id' => SettingsForm::getInstance()->fb_auth_client_id,
                'app_secret' => SettingsForm::getInstance()->fb_auth_app_secret,
            ]);

            $helper = $fb->getRedirectLoginHelper();

            //Хотфикс
            if (isset($_GET['state'])) {
                $helper->getPersistentDataHandler()->set('state', $_GET['state']);
            }

            $accessToken = $helper->getAccessToken($redirectUrl);

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
                $user->fb_msg_sub_code = Help::randomString(10);
                $user->role_id = Constants::ROLE_USER;
                $user->status_id = Constants::STATUS_ENABLED;
                $user->created_at = date('Y-m-d H:i:s');
                $user->created_by_id = 0;
            }

            $user->email = ArrayHelper::getValue($baseUserData,'email');
            $user->avatar_url = $avatarData->getUrl();
            $user->updated_at = date('Y-m-d H:i:s');
            $user->last_login_at = date('Y-m-d H:i:s');
            $user->updated_by_id = 0;
            $user->facebook_token = $accessToken->getValue();
            $user->save();

            return Yii::$app->user->login($user, 0);
        }catch (\Exception $ex){
            Yii::info($ex->getMessage(),'info');
        }

        return false;
    }

    /**
     * Перенаправление для коротких ссылок
     * @param $key
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionShortLinkRedirect($key)
    {
        /* @var $shortLink ShortLink */
        $shortLink = ShortLink::find()->where(['key' => $key, 'status_id' => Constants::STATUS_ENABLED])->one();

        if(empty($shortLink)){
            throw new NotFoundHttpException('Page not found', 404);
        }

        //Увеличить кол-во кликов
        $shortLink->clicks++;
        $shortLink->update();

        //return $this->redirect($shortLink->original_link);
        return $this->renderPartial('short-link-redirect',['link' => $shortLink]);
    }

    /**
     * Авторизация через Facebook для личного кабинета админа группы
     * @return \yii\web\Response
     * @throws ServerErrorHttpException
     */
    public function actionAuthFbGroupAdmin()
    {
        if($this->facebookAuthentication(Url::to(['/main/auth-fb-group-admin', 'language' => null], 'https'))){
            return $this->redirect(Url::to(['/group-admin/main/index'],'http'));
        }
        throw new ServerErrorHttpException('Facebook authorization failed',500);
    }

    /**
     * Авторизация через Facebook для личного кабинета обычного пользователя
     * @return \yii\web\Response
     * @throws ServerErrorHttpException
     */
    public function actionAuthFbUser()
    {
        if($this->facebookAuthentication(Url::to(['/main/auth-fb-user', 'language' => null], 'https'))){
            return $this->redirect(Url::to(['/user/main/index'],'http'));
        }
        throw new ServerErrorHttpException('Facebook authorization failed',500);
    }
}
