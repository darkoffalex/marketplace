<?php

namespace app\components;

use app\models\Language;
use app\models\User;
use yii\web\Controller as BaseController;
use yii\base\Module;
use yii\base\Action;
use Yii;

class Controller extends BaseController
{
    /**
     * @var null|string заголовок страницы
     */
    public $title = null;

    /**
     * Переопределить конструктор
     * @param string $id
     * @param Module $module
     * @param array $config
     */
    public function __construct($id, $module, $config = [])
    {
        //Отображаемый заголовок
        $this->view->title = Yii::$app->name;
        //Заголовок окна браузера
        $this->title = $this->view->title;

        //Layout по умолчанию
        $this->layout = 'main_landing';

        //мета-теги
        $this->view->registerMetaTag(['name' => 'description', 'content' => ""]);
        $this->view->registerMetaTag(['name' => 'keywords', 'content' => ""]);

        //open-graph мета-теги
        $this->view->registerMetaTag(['property' => 'og:description', 'content' => ""]);
        $this->view->registerMetaTag(['property' => 'og:url', 'content' => ""]);
        $this->view->registerMetaTag(['property' => 'og:site_name', 'content' => ""]);
        $this->view->registerMetaTag(['property' => 'og:title', 'content' => ""]);
        $this->view->registerMetaTag(['property' => 'og:image', 'content' => ""]);
        $this->view->registerMetaTag(['property' => 'og:image:width', 'content' => '200']);
        $this->view->registerMetaTag(['property' => 'og:image:height', 'content' => '200']);

        //временная зона
        date_default_timezone_set('Europe/Moscow');

        //базовый конструктор
        parent::__construct($id,$module,$config);
    }

    /**
     * Выполнять перед каждым action'ом
     * @param Action $action
     * @return bool
     * @throws \yii\web\BadRequestHttpException
     */
    public function beforeAction($action)
    {
        //текущий пользователь (пустой если не авторизован)
        /* @var $user User */
        $user = !Yii::$app->user->isGuest ? Yii::$app->user->identity : null;

        /* @var $defaultLanguage Language */
        //получить префикс языка по умолчанию (учитывая предпочитаемый я
        $defaultLanguage = Language::find()->where(['is_default' => (int)true])->one();
        $defaultLanguagePrefix = !empty($user->preferred_language) ? $user->preferred_language : (!empty($defaultLanguage) ? $defaultLanguage->prefix : Yii::$app->language);

        //установить язык из GET либо по умолчнию
        Yii::$app->language = Yii::$app->request->get('language',$defaultLanguagePrefix);

        //если пользователь не пуст - обновить его
        if(!empty($user)){
            $user->last_login_at = date('Y-m-d H:i:s',time());
            $user->preferred_language = Yii::$app->language;
            $user->update();
        }

        return parent::beforeAction($action);
    }
}