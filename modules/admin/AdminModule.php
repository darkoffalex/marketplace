<?php

namespace app\modules\admin;

use app\models\User;
use Yii;
use yii\helpers\Url;
use app\modules\admin\helpers\Access;

/**
 * admin module definition class
 */
class AdminModule extends \yii\base\Module
{
    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'app\modules\admin\controllers';

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        Yii::$app->errorHandler->errorAction = 'admin/main/error';
        $this->layoutPath = "@app/modules/admin/views/layouts";
        $this->viewPath = "@app/modules/admin/views";
        $this->layout = 'main';
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

        //временная зона
        date_default_timezone_set('Europe/Moscow');

        //текущий пользователь (пустой если не авторизован)
        /* @var $user User */
        $user = !Yii::$app->user->isGuest ? Yii::$app->user->identity : null;

        //Если не авторизован либо если нет доступа (не подходящая роль) и текущая страница не в перечне открытых
        if(!Access::has($user,$action->controller->id, $action->id) && !in_array($action->id,['login'])){
            Yii::$app->response->redirect(Url::to(['/admin/main/login']));
            return false;
        }
        //если авторизован и нет проблем с доступом - обновить последнее время визита
        elseif(!Yii::$app->user->isGuest){
            $user->last_online_at = date('Y-m-d H:i:s');
            $user->update();
        }

        return true;
    }
}
