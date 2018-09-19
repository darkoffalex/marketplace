<?php

use kartik\grid\GridView;
use app\models\Marketplace;
use yii\helpers\ArrayHelper;
use app\models\MarketplaceTariffPrice;
use yii\helpers\Html;
use yii\helpers\Url;
use app\helpers\Constants;

/* @var $searchModel \app\models\PosterSearch */
/* @var $dataProvider \yii\data\ActiveDataProvider */
/* @var $this \yii\web\View */
/* @var $controller \app\modules\groupAdmin\controllers\PostersController */
/* @var $user \app\models\User */

$controller = $this->context;
$user = Yii::$app->user->identity;

$this->title = Yii::t('app','Advertisements');
$this->params['breadcrumbs'][] = $this->title;


/* @var $marketplaces Marketplace[] */
$marketplaces = Marketplace::find()
    ->where(['user_id' => Yii::$app->user->id])
    ->all();

/* @var $marketplaceTariffs MarketplaceTariffPrice[] */
$marketplaceTariffs = MarketplaceTariffPrice::find()
    ->alias('mpt')
    ->joinWith('marketplace mp')
    ->where(['mp.user_id' => Yii::$app->user->id])
    ->distinct()
    ->all();

$gridColumns = [
    [
        'attribute' => 'id',
        'contentOptions' => ['style' => 'width:70px;'],
        'headerOptions' => ['style' => 'width:70px;'],
    ],

    [
        'attribute' => 'title',
        'enableSorting' => false,
    ],

    [
        'attribute' => 'marketplace_id',
        'label' => Yii::t('app','Marketplace'),
        'filter' => ArrayHelper::map($marketplaces,'id','name'),
        'enableSorting' => false,
        'format' => 'raw',
        'value' => function ($model, $key, $index, $column){
            /* @var $model \app\models\PosterSearch */
            return !empty($model->marketplace) ? $model->marketplace->name.' ('.Yii::t('app',$model->marketplace->country->name).')': null;
        },
    ],

    [
        'attribute' => 'marketplace_tariff_id',
        'label' => Yii::t('app','Tariff'),
        'filter' => ArrayHelper::map($marketplaceTariffs,'id',function($item){/* @var $item MarketplaceTariffPrice */ return $item->getNameWithDetails(); }),
        'enableSorting' => false,
        'format' => 'raw',
        'value' => function ($model, $key, $index, $column){
            /* @var $model \app\models\PosterSearch */
            return $model->marketplaceTariff->getNameWithDetails();
        },
    ],
    
    [
        'attribute' => 'paid_at',
        'label' => Yii::t('app','Paid'),
        'filter' => [
            1 => Yii::t('app','Yes'),
            0 => Yii::t('app','No'),
        ],
        'enableSorting' => false,
        'format' => 'raw',
        'value' => function ($model, $key, $index, $column){
            /* @var $model \app\models\PosterSearch */
            return $model->getPaymentInformation();
        },
    ],

    [
        'attribute' => 'approved_by_ga',
        'label' => Yii::t('app','Approved by group admin'),
        'filter' => [
            1 => Yii::t('app','Yes'),
            0 => Yii::t('app','No'),
        ],
        'enableSorting' => false,
        'format' => 'raw',
        'value' => function ($model, $key, $index, $column){
            /* @var $model \app\models\PosterSearch */
            $names = [
                1 => '<span class="label label-success">'.Yii::t('app','Yes').'</span>',
                0 => '<span class="label label-danger">'.Yii::t('app','No').'</span>',
            ];
            return $names[(int)$model->approved_by_ga];
        },
    ],

    [
        'attribute' => 'approved_by_sa',
        'label' => Yii::t('app','Approved by super admin'),
        'filter' => [
            1 => Yii::t('app','Yes'),
            0 => Yii::t('app','No'),
        ],
        'enableSorting' => false,
        'format' => 'raw',
        'value' => function ($model, $key, $index, $column){
            /* @var $model \app\models\PosterSearch */
            $names = [
                1 => '<span class="label label-success">'.Yii::t('app','Yes').'</span>',
                0 => '<span class="label label-danger">'.Yii::t('app','No').'</span>',
            ];
            return $names[(int)$model->approved_by_sa];
        },
    ],

    [
        'attribute' => 'admin_post_time_approve_status',
        'label' => Yii::t('app','Admin post status'),
        'filter' => [
            Constants::ADMIN_POST_TIME_AT_REVIEW => Yii::t('app','At review'),
            Constants::ADMIN_POST_TIME_APPROVED => Yii::t('app','Approved'),
            Constants::ADMIN_POST_TIME_DISAPPROVED => Yii::t('app','Disapproved'),
        ],
        'enableSorting' => false,
        'format' => 'raw',
        'value' => function ($model, $key, $index, $column){
            /* @var $model \app\models\PosterSearch */
            if($model->marketplaceTariff->tariff->special_type != Constants::TARIFF_SUB_TYPE_ADMIN_POST){
                return '<span class="label label-default">'.Yii::t('app','Not used').'</span>';
            }

            $names = [
                Constants::ADMIN_POST_TIME_AT_REVIEW => '<span class="label label-warning">'.Yii::t('app','At review').'</span>',
                Constants::ADMIN_POST_TIME_APPROVED => '<span class="label label-success">'.Yii::t('app','Approved').'</span>',
                Constants::ADMIN_POST_TIME_DISAPPROVED => '<span class="label label-danger">'.Yii::t('app','Disapproved').'</span>',
            ];
            return $names[(int)$model->admin_post_time_approve_status];
        },
    ],

    [
        'class' => 'yii\grid\ActionColumn',
        'contentOptions'=>['style'=>'width: 140px; text-align: center;'],
        'header' => Yii::t('app','Actions'),
        'template' => '{update} {delete} {check}',
        'buttons' => [
            'check' => function ($url,$model,$key) {
                /* @var $model \app\models\PosterSearch */
                return Html::a('<span class="glyphicon glyphicon-check"></span>', Url::to(['/group-admin/posters/check', 'id' => $model->id]), ['title' => Yii::t('app','Check'), 'data-target' => '.modal-main', 'data-toggle'=>'modal']);
            },
        ],
    ],
];

?>

<div class="row">
    <div class="col-xs-12">
        <div class="box">
            <div class="box-header"></div>
            <div class="box-body">
                <?= GridView::widget([
                    'filterModel' => $searchModel,
                    'dataProvider' => $dataProvider,
                    'columns' => $gridColumns,
                    'pjax' => false,
                ]); ?>
            </div>
        </div>
    </div>
</div>
