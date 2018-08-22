<?php

namespace app\modules\admin\controllers;

use Yii;
use app\modules\admin\helpers\Access;
use yii\helpers\Url;
use app\models\User;
use app\models\forms\LoginForm;
use yii\web\Controller;

/**
 * Базовый контроллер для модуля "Admin"
 *
 * @copyright 	2017 Alex Nem
 * @link 		https://github.com/darkoffalex
 * @author 		Alex Nem
 *
 * @package app\modules\admin\controllers
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
     * Главная страница админки
     * @return string|\yii\web\Response
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * Логин админ-пользователя при помощи формы
     * @return string
     */
    public function actionLogin()
    {
        $this->layout = 'main-login';
        $this->view->title = Yii::t('app','System admin area');

        /* @var $user User|null */
        $user = Yii::$app->user->identity;

        if (Access::has($user,'main/index')) {
            return $this->redirect(Url::to(['/admin/main/index']));
        }

        $model = new LoginForm();

        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->redirect(Url::to(['/admin/main/index']));
        }

        return $this->render('login', compact('model'));
    }

    /**
     * Логаут
     * @return \yii\web\Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();
        return $this->redirect(Url::to(['/admin/main/login']));
    }
}
