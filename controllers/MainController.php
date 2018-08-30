<?php

namespace app\controllers;

use app\components\Controller;
use Yii;
use yii\helpers\Url;

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
}