<?php

namespace app\widgets;

use app\models\forms\SettingsForm;
use app\models\User;
use yii\base\Widget;
use Yii;

class ProfileWidget extends Widget
{
    /**
     * @var null|User
     */
    private $user = null;
    public $settings = null;

    public function init()
    {
        parent::init();
        $this->settings = SettingsForm::getInstance();
        $this->user = !Yii::$app->user->isGuest ? Yii::$app->user->identity : null;
    }

    public function run()
    {
        return $this->render('profile',['user' => $this->user, 'settings' => $this->settings]);
    }
}