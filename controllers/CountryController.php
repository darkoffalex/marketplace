<?php

namespace app\controllers;

use app\components\Controller;
use app\models\Country;
use app\models\Category;
use app\helpers\Constants;
use app\models\Poster;
use yii\data\Pagination;
use yii\web\NotFoundHttpException;
use Yii;

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


        //Сформировать запрос на получение всех опубликованных и оплаченных объявлений
        $q = Poster::find()->alias('p')->joinWith('marketplaceTariff.tariff t')->where([
            'p.country_id' => $country->id,
            'p.status_id' => Constants::STATUS_ENABLED,
            'p.published' => (int)true,
        ])->andFilterWhere([
            'or',
            '(p.paid_at + INTERVAL period_seconds SECOND) > NOW()',
            'p.paid_at < NOW() and t.special_type = '.Constants::TARIFF_SUB_TYPE_ADMIN_POST
        ])->distinct();

        //Кол-во
        $count = $q->count();
        $pagination = new Pagination(['totalCount' => $count, 'pageSize' => Yii::$app->request->get('per-page',20)]);
        /* @var $posters Poster[] */
        $posters = $q->offset($pagination->offset)->limit($pagination->limit)->all();


        return $this->render('index',compact('country','categories','posters','pagination'));
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

        /* @var $category Category */
        $category = Category::find()
            ->with(['childrenActive.parent'],['childrenActive.childrenActive'])
            ->where(['id' => (int)$id, 'status_id' => Constants::STATUS_ENABLED])
            ->one();

        if(empty($category)){
            throw new NotFoundHttpException(\Yii::t('app','Page not found'));
        }

        //Сформировать запрос на получение всех опубликованных и оплаченных объявлений
        $q = Poster::find()->alias('p')->joinWith('marketplaceTariff.tariff t')->where([
            'p.country_id' => $country->id,
            'p.category_id' => $category->id,
            'p.status_id' => Constants::STATUS_ENABLED,
            'p.published' => (int)true,
        ])->andFilterWhere([
            'or',
            '(p.paid_at + INTERVAL period_seconds SECOND) > NOW()',
            'p.paid_at < NOW() and t.special_type = '.Constants::TARIFF_SUB_TYPE_ADMIN_POST
        ])->distinct();

        //Кол-во
        $count = $q->count();
        $pagination = new Pagination(['totalCount' => $count, 'pageSize' => Yii::$app->request->get('per-page',20)]);
        /* @var $posters Poster[] */
        $posters = $q->offset($pagination->offset)->limit($pagination->limit)->all();

        return $this->render('category',compact('country','category','posters','pagination'));
    }
}