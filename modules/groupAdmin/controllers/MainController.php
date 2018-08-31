<?php

namespace app\modules\groupAdmin\controllers;

use yii\web\Controller;
/**
 * Default controller for the `group-admin` module
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
     * Главная страница личного кабинета
     * @return string|\yii\web\Response
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * Главная страница личного кабинета
     * @return string
     */
    public function actionLogin()
    {
        $this->layout = 'main-login';
        return $this->render('login');
    }
}
