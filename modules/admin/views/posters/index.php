<?php

use kartik\grid\GridView;
use app\models\Marketplace;
use yii\helpers\ArrayHelper;
use app\models\Tariff;
use yii\helpers\Html;
use yii\helpers\Url;
use app\modules\admin\helpers\Access;
use yii\web\JsExpression;
use kartik\select2\Select2;
use app\helpers\Constants;

/* @var $searchModel \app\models\PosterSearch */
/* @var $dataProvider \yii\data\ActiveDataProvider */
/* @var $this \yii\web\View */
/* @var $controller \app\modules\admin\controllers\PostersController */
/* @var $user \app\models\User */

$controller = $this->context;
$user = Yii::$app->user->identity;

$this->title = Yii::t('app','Advertisements');
$this->params['breadcrumbs'][] = $this->title;


/* @var $marketplaces Marketplace[] */
$marketplaces = Marketplace::find()->all();

/* @var $tariffs Tariff[] */
$tariffs = Tariff::find()->all();

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
        'attribute' => 'user_id',
        'label' => Yii::t('app','User'),
        'filter' => Select2::widget([
            'model' => $searchModel,
            'attribute' => 'user_id',
            'initValueText' => !empty($searchModel->user) ? $searchModel->user->name.' ('.$searchModel->user_id.')' : '',
            'options' => ['placeholder' => Yii::t('app','Search for a user...')],
            'language' => Yii::$app->language,
            'theme' => Select2::THEME_DEFAULT,
            'pluginOptions' => [
                'allowClear' => true,
                'minimumInputLength' => 1,
                'language' => [
                    'noResults' => new JsExpression("function () { return '".Yii::t('app','No results found')."'; }"),
                    'searching' => new JsExpression("function () { return '".Yii::t('app','Searching...')."'; }"),
                    'inputTooShort' => new JsExpression("function(args) {return '".Yii::t('app','Type more characters')."'}"),
                    'errorLoading' => new JsExpression("function () { return '".Yii::t('app','Waiting for results')."'; }"),
                ],
                'ajax' => [
                    'url' => Url::to(['/admin/users/ajax-search']),
                    'dataType' => 'json',
                    'data' => new JsExpression('function(params) { return {q:params.term}; }')
                ],
                'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                'templateResult' => new JsExpression('function(user) { return user.text; }'),
                'templateSelection' => new JsExpression('function (user) { return user.text; }'),
            ]
        ]),
        'enableSorting' => false,
        'format' => 'raw',
        'value' => function ($model, $key, $index, $column){
            /* @var $model \app\models\PosterSearch */
            return !empty($model->user) ? $model->user->name : null;
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
        'attribute' => 'tariff_id',
        'label' => Yii::t('app','Tariff'),
        'filter' => ArrayHelper::map($tariffs,'id','name'),
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
        'class' => 'yii\grid\ActionColumn',
        'contentOptions'=>['style'=>'width: 140px; text-align: center;'],
        'header' => Yii::t('app','Actions'),
        'template' => '{update} {delete} {check}',
        'buttons' => [
            'check' => function ($url,$model,$key) {
                /* @var $model \app\models\PosterSearch */
                return Html::a('<span class="glyphicon glyphicon-check"></span>', Url::to(['/admin/posters/check', 'id' => $model->id]), ['title' => Yii::t('app','Check'), 'data-target' => '.modal-main', 'data-toggle'=>'modal']);
            },
        ],
        'visibleButtons' => [
            'delete' => function ($model, $key, $index) use ($user) {/* @var $model \app\models\Tariff */ return Access::has($user,'posters','delete');},
            'update' => function ($model, $key, $index) use ($user) {/* @var $model \app\models\Tariff */ return Access::has($user,'posters','update');},
            'check' => function ($model, $key, $index) use ($user) {/* @var $model \app\models\Tariff */ return Access::has($user,'posters','check');},
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
            <?php if(Access::has($user,'posters','create')): ?>
                <div class="box-footer">
                    <a data-toggle="modal" data-target=".modal-main" href="<?php echo Url::to(['/admin/posters/create']); ?>" class="btn btn-primary"><?= Yii::t('app','Create'); ?></a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
