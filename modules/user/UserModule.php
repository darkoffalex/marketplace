<?php

namespace app\modules\user;

use Yii;
use app\models\User;
use app\models\Language;
use yii\helpers\Url;

/**
 * user module definition class
 */
class UserModule extends \yii\base\Module
{
    /**
     * {@inheritdoc}
     */
    public $controllerNamespace = 'app\modules\user\controllers';

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        Yii::$app->errorHandler->errorAction = 'user/main/error';
        $this->layoutPath = "@app/modules/user/views/layouts";
        $this->viewPath = "@app/modules/user/views";
        $this->layout = 'main';
        parent::init();
    }

    /**
     * Переопределение before action метода (выполнение перед каждым действием)
     * @param \yii\base\Action $action
     * @return bool
     */
    public function beforeAction($action)
    {
        if(!parent::beforeAction($action)){
            return false;
        }

        //Временная зона
        date_default_timezone_set('Europe/Moscow');

        //Текущий пользователь (пустой если не авторизован)
        /* @var $user User */
        $user = !Yii::$app->user->isGuest ? Yii::$app->user->identity : null;

        /* @var $defaultLanguage Language */
        //Получить префикс языка по умолчанию (учитывая предпочитаемый я
        $defaultLanguage = Language::find()->where(['is_default' => (int)true])->one();
        $defaultLanguagePrefix = !empty($user->preferred_language) ? $user->preferred_language : (!empty($defaultLanguage) ? $defaultLanguage->prefix : Yii::$app->language);

        //Установить язык из GET либо по умолчнию
        Yii::$app->language = Yii::$app->request->get('language',$defaultLanguagePrefix);

        //Контроллер и экшен
        $ca = Yii::$app->controller->id.'/'.Yii::$app->controller->action->id;

        //Открыте пути
        $open = [
            'main/login',
        ];

        //Если не авторизован и это не открытый путь - редирект
        if(Yii::$app->user->isGuest && !in_array($ca,$open)){
            Yii::$app->response->redirect(Url::to(['/user/main/login']));
            return false;
        }
        //Если авторизован
        elseif(!empty($user)){
            $user->last_online_at = date('Y-m-d H:i:s',time());
            $user->preferred_language = Yii::$app->language;
            $user->update();
        }

        return true;
    }
}
