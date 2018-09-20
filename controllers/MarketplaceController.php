<?php

namespace app\controllers;

use app\helpers\Constants;
use app\models\Country;
use app\components\Controller;
use app\models\Marketplace;
use app\models\Poster;
use yii\web\NotFoundHttpException;
use yii\data\Pagination;
use app\models\Category;
use Yii;

/**
 * Показ маркетплейса и его содержимого
 *
 * @copyright 	2018 Alex Nem
 * @link 		https://github.com/darkoffalex
 * @author 		Alex Nem
 *
 * @package app\controllers
 */
class MarketplaceController extends Controller
{
    /**
     * Основная страница маркетплейса
     * @param $subSubSubDomain
     * @param $subSubDomain
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionIndex($subSubSubDomain, $subSubDomain)
    {
        /* @var $country Country */
        $country = Country::find()
            ->where(['domain_alias' => $subSubDomain, 'status_id' => Constants::STATUS_ENABLED])
            ->one();

        if(empty($country)){
            throw new NotFoundHttpException(\Yii::t('app','Page not found'));
        }

        /* @var $marketplace Marketplace */
        $marketplace = Marketplace::find()
            ->where(['domain_alias' => $subSubSubDomain, 'status_id' => Constants::STATUS_ENABLED, 'country_id' => $country->id])
            ->one();

        if(empty($marketplace)){
            throw new NotFoundHttpException(\Yii::t('app','Page not found'));
        }

        /* @var $categories Category[] */
        $categories = Category::find()->alias('c')
            ->where(['c.status_id' => Constants::STATUS_ENABLED, 'c.parent_category_id' => 0])
            ->all();

        //Скрыть пустые категории если нужно
        if(!$marketplace->display_empty_categories){
            foreach ($categories as $index => $category){
                if($category->getPublishedPostersForMarketplace($marketplace->id)->count() == 0){
                    unset($categories[$index]);
                }
            }
        }

        //Сформировать запрос на получение всех опубликованных и оплаченных объявлений
        $q = Poster::find()->alias('p')->joinWith('marketplaceTariff.tariff t')->where([
            'p.marketplace_id' => $marketplace->id,
            'p.status_id' => Constants::STATUS_ENABLED,
            'p.published' => (int)true,
        ])->andFilterWhere([
            'or',
            '(p.paid_at + INTERVAL period_seconds SECOND) > NOW()',
            'p.paid_at < NOW() and t.special_type = '.Constants::TARIFF_SUB_TYPE_ADMIN_POST
        ])->distinct();

        //Кол-во
        $count = $q->count();
        //Пагинация
        $pagination = new Pagination(['totalCount' => $count, 'pageSize' => Yii::$app->request->get('per-page',20)]);
        /* @var $posters Poster[] */
        $posters = $q->offset($pagination->offset)->limit($pagination->limit)->all();

        return $this->render('index',compact('country','categories','posters','pagination','marketplace'));
    }

    /**
     * Категория в маркетплейсе
     * @param $subSubSubDomain
     * @param $subSubDomain
     * @param $id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionCategory($subSubSubDomain, $subSubDomain, $id)
    {
        /* @var $country Country */
        $country = Country::find()
            ->where(['domain_alias' => $subSubDomain, 'status_id' => Constants::STATUS_ENABLED])
            ->one();

        if(empty($country)){
            throw new NotFoundHttpException(\Yii::t('app','Page not found'));
        }

        /* @var $marketplace Marketplace */
        $marketplace = Marketplace::find()
            ->where(['domain_alias' => $subSubSubDomain, 'status_id' => Constants::STATUS_ENABLED, 'country_id' => $country->id])
            ->one();

        if(empty($marketplace)){
            throw new NotFoundHttpException(\Yii::t('app','Page not found'));
        }

        /* @var $category Category */
        $category = Category::find()
            ->with(['childrenActive.parent'],['childrenActive.childrenActive'])
            ->where(['id' => (int)$id, 'status_id' => Constants::STATUS_ENABLED])
            ->one();

        if(empty($category)){
            throw new NotFoundHttpException(\Yii::t('app','Category not found'));
        }

        /* @var $categories Category[] */
        $categories = Category::find()->alias('c')
            ->where(['c.status_id' => Constants::STATUS_ENABLED, 'c.parent_category_id' => (int)$category->id])
            ->all();

        //Скрыть пустые категории если нужно
        if(!$marketplace->display_empty_categories){
            foreach ($categories as $index => $categoryItem){
                if($categoryItem->getPublishedPostersForMarketplace($marketplace->id)->count() == 0){
                    unset($categories[$index]);
                }
            }
        }

        //Сформировать запрос на получение всех опубликованных и оплаченных объявлений
        $q = Poster::find()->alias('p')->joinWith('marketplaceTariff.tariff t')->where([
            'p.category_id' => (int)$category->id,
            'p.marketplace_id' => $marketplace->id,
            'p.status_id' => Constants::STATUS_ENABLED,
            'p.published' => (int)true,
        ])->andFilterWhere([
            'or',
            '(p.paid_at + INTERVAL period_seconds SECOND) > NOW()',
            'p.paid_at < NOW() and t.special_type = '.Constants::TARIFF_SUB_TYPE_ADMIN_POST
        ])->distinct();

        //Кол-во
        $count = $q->count();
        //Пагинация
        $pagination = new Pagination(['totalCount' => $count, 'pageSize' => Yii::$app->request->get('per-page',20)]);
        /* @var $posters Poster[] */
        $posters = $q->offset($pagination->offset)->limit($pagination->limit)->all();

        return $this->render('category',compact('country','categories','posters','pagination','marketplace','category'));
    }
}