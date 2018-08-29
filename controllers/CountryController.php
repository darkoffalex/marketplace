<?php

namespace app\controllers;

use app\components\Controller;
use app\models\Country;
use app\models\Category;
use app\helpers\Constants;
use yii\web\NotFoundHttpException;

/**
 * Контроллер для общего просмотра объявлений по странам
 *
 * @copyright 	2018 Alex Nem
 * @link 		https://github.com/darkoffalex
 * @author 		Alex Nem
 *
 * @package app\controllers
 */
class CountryController extends Controller
{
    /**
     * Просмотр объявлений по стране
     * @param null $subSubDomain
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionIndex($subSubDomain = null)
    {
        /* @var $country Country */
        $country = Country::find()
            ->where(['domain_alias' => $subSubDomain, 'status_id' => Constants::STATUS_ENABLED])
            ->one();

        if(empty($country)){
            throw new NotFoundHttpException(\Yii::t('app','Page not found'));
        }

        /* @var $categories Category[] */
        $categories = Category::find()
            ->where(['status_id' => Constants::STATUS_ENABLED, 'parent_category_id' => 0])
            ->all();

        return $this->render('index',compact('country','categories'));
    }

    /**
     * Просмотр объявлений по категории (и стране)
     * @param $subSubDomain
     * @param $id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionCategory($subSubDomain, $id)
    {
        /* @var $country Country */
        $country = Country::find()
            ->where(['domain_alias' => $subSubDomain, 'status_id' => Constants::STATUS_ENABLED])
            ->one();

        if(empty($country)){
            throw new NotFoundHttpException(\Yii::t('app','Page not found'));
        }

        $category = Category::find()
            ->with(['childrenActive.parent'],['childrenActive.childrenActive'])
            ->where(['id' => (int)$id, 'status_id' => Constants::STATUS_ENABLED])
            ->one();

        if(empty($category)){
            throw new NotFoundHttpException(\Yii::t('app','Page not found'));
        }


        return $this->render('category',compact('country','category'));
    }
}