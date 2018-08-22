<?php

namespace app\widgets;

use app\models\forms\SettingsForm;
use app\models\User;
use yii\base\Widget;
use Yii;

class LoginWidget extends Widget
{
    /**
     * @var null|User
     */
    private $user = null;
    private $settings = null;

    public function init()
    {
        parent::init();
        $this->settings = SettingsForm::getInstance();
        $this->user = !Yii::$app->user->isGuest ? Yii::$app->user->identity : null;
    }

    public function run()
    {
        return $this->render('login',['user' => $this->user, 'settings' => $this->settings]);
    }
}