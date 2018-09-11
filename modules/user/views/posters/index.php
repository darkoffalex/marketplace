<?php

use kartik\grid\GridView;
use app\helpers\Constants;
use app\models\Marketplace;
use yii\helpers\ArrayHelper;
use app\models\MarketplaceTariffPrice;
use yii\helpers\Html;

/* @var $searchModel \app\models\PosterSearch */
/* @var $dataProvider \yii\data\ActiveDataProvider */
/* @var $this \yii\web\View */
/* @var $controller \app\modules\user\controllers\PostersController */
/* @var $user \app\models\User */

$controller = $this->context;
$user = Yii::$app->user->identity;

$this->title = Yii::t('app','My advertisements');
$this->params['breadcrumbs'][] = $this->title;


/* @var $marketplaces Marketplace[] */
$marketplaces = Marketplace::find()
    ->alias('mp')
    ->joinWith('marketplaceKeys k')
    ->where(['k.used_by_id' => Yii::$app->user->id])
    ->all();

/* @var $marketplaceTariffs MarketplaceTariffPrice[] */
$marketplaceTariffs = MarketplaceTariffPrice::find()
    ->alias('mpt')
    ->joinWith('marketplace.marketplaceKeys k')
    ->where(['k.used_by_id' => Yii::$app->user->id])
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
        'header' => Yii::t('app','Picture'),
        'format' => 'raw',
        'value' => function ($model, $key, $index, $column){
            /* @var $model \app\models\PosterSearch */
            if(!empty($model->mainImage)){
                return Html::img($model->mainImage->getThumbnailUrl(64,64),['class' => 'img thumbnail-img']);
            }
            return null;
        },
    ],

    [
        'attribute' => 'status_id',
        'label' => Yii::t('app','Status'),
        'filter' => [
            Constants::STATUS_ENABLED => Yii::t('app','Enabled'),
            Constants::STATUS_DISABLED => Yii::t('app','Disabled'),
        ],
        'enableSorting' => false,
        'format' => 'raw',
        'value' => function ($model, $key, $index, $column){
            /* @var $model \app\models\PosterSearch */
            $names = [
                Constants::STATUS_ENABLED => '<span class="label label-success">'.Yii::t('app','Enabled').'</span>',
                Constants::STATUS_DISABLED => '<span class="label label-danger">'.Yii::t('app','Disabled').'</span>',
            ];
            return !empty($names[$model->status_id]) ? $names[$model->status_id] : null;
        },
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
        'attribute' => 'approved',
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

            if($model->approved_by_sa && $model->approved_by_ga){
                return $names[1];
            }

            return $names[0].(!empty($model->refuse_reason) ? ' <small><cite>'.Html::a(Yii::t('app','(Reason)'),['/user/posters/reason', 'id' => $model->id],['data-target' => '.modal-main', 'data-toggle' => 'modal']).'</cite></small>' : '');
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
        'class' => 'yii\grid\ActionColumn',
        'contentOptions'=>['style'=>'width: 140px; text-align: center;'],
        'header' => Yii::t('app','Actions'),
        'template' => '{update} {delete}',
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
