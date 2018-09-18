<?php

use kartik\grid\GridView;
use yii\helpers\Url;
use yii\bootstrap\Html;
use app\helpers\Constants;
use app\modules\admin\helpers\Access;

/* @var $searchModel \app\models\TariffSearch */
/* @var $dataProvider \yii\data\ActiveDataProvider */
/* @var $this \yii\web\View */
/* @var $controller \app\modules\admin\controllers\CountriesController */
/* @var $user \app\models\User */

$controller = $this->context;
$user = Yii::$app->user->identity;

$this->title = Yii::t('app','Tariffs');
$this->params['breadcrumbs'][] = $this->title;

$gridColumns = [
    [
        'attribute' => 'id',
        'contentOptions' => ['style' => 'width:70px;']
    ],

    [
        'attribute' => 'name',
        'enableSorting' => false,
    ],

    [
        'attribute' => 'description',
        'enableSorting' => false,
    ],

    [
        'attribute' => 'base_price',
        'enableSorting' => false,
        'format' => 'raw',
        'value' => function ($model, $key, $index, $column){
            /* @var $model \app\models\Tariff */
            return \app\helpers\Help::toPrice($model->base_price);
        },

    ],

    [
        'attribute' => 'discounted_price',
        'enableSorting' => false,
        'format' => 'raw',
        'value' => function ($model, $key, $index, $column){
            /* @var $model \app\models\Tariff */
            return \app\helpers\Help::toPrice($model->discounted_price);
        },
    ],

    [
        'attribute' => 'show_on_page',
        'filter' => [
            1 => Yii::t('app','Yes'),
            0 => Yii::t('app','No'),
        ],
        'enableSorting' => false,
        'format' => 'raw',
        'value' => function ($model, $key, $index, $column){
            /* @var $model \app\models\Tariff */
            $names = [
                1 => '<span class="label label-success">'.Yii::t('app','Yes').'</span>',
                0 => '<span class="label label-danger">'.Yii::t('app','No').'</span>',
            ];
            return !empty($names[(int)$model->show_on_page]) ? $names[(int)$model->show_on_page] : null;
        },
    ],

//    [
//        'attribute' => 'is_main',
//        'filter' => [
//            1 => Yii::t('app','Yes'),
//            0 => Yii::t('app','No'),
//        ],
//        'enableSorting' => false,
//        'format' => 'raw',
//        'value' => function ($model, $key, $index, $column){
//            /* @var $model \app\models\Tariff */
//            $names = [
//                1 => '<span class="label label-success">'.Yii::t('app','Yes').'</span>',
//                0 => '<span class="label label-danger">'.Yii::t('app','No').'</span>',
//            ];
//            return !empty($names[(int)$model->is_main]) ? $names[(int)$model->is_main] : null;
//        },
//    ],

    [
        'attribute' => 'subscription',
        'filter' => [
            1 => Yii::t('app','Yes'),
            0 => Yii::t('app','No'),
        ],
        'enableSorting' => false,
        'format' => 'raw',
        'value' => function ($model, $key, $index, $column){
            /* @var $model \app\models\Tariff */
            $names = [
                1 => '<span class="label label-success">'.Yii::t('app','Yes').'</span>',
                0 => '<span class="label label-danger">'.Yii::t('app','No').'</span>',
            ];
            return !empty($names[(int)$model->subscription]) ? $names[(int)$model->subscription] : null;
        },
    ],

    [
        'attribute' => 'period_unit_type',
        'filter' => [
            Constants::PERIOD_DAYS => Yii::t('app','Days'),
            Constants::PERIOD_WEEKS => Yii::t('app','Weeks'),
            Constants::PERIOD_MONTHS => Yii::t('app','Months'),
        ],
        'enableSorting' => false,
        'format' => 'raw',
        'value' => function ($model, $key, $index, $column){
            /* @var $model \app\models\Tariff */
            $names = [
                Constants::PERIOD_DAYS => Yii::t('app','Days'),
                Constants::PERIOD_WEEKS => Yii::t('app','Weeks'),
                Constants::PERIOD_MONTHS => Yii::t('app','Months'),
            ];
            return !empty($names[$model->period_unit_type]) ? $names[$model->period_unit_type] : null;
        },
    ],

    [
        'attribute' => 'period_amount',
        'enableSorting' => false,
    ],

    [
        'attribute' => 'special_type',
        'filter' => [
            Constants::TARIFF_SUB_TYPE_REGULAR => Yii::t('app','Regular'),
            Constants::TARIFF_SUB_TYPE_ADMIN_POST => Yii::t('app','Admin\'s post'),
        ],
        'enableSorting' => false,
        'format' => 'raw',
        'value' => function ($model, $key, $index, $column){
            /* @var $model \app\models\Tariff */
            $names = [
                Constants::TARIFF_SUB_TYPE_REGULAR => Yii::t('app','Regular'),
                Constants::TARIFF_SUB_TYPE_ADMIN_POST => Yii::t('app','Admin\'s post'),
            ];
            return !empty($names[$model->special_type]) ? $names[$model->special_type] : null;
        },
    ],

    [
        'class' => 'yii\grid\ActionColumn',
        'contentOptions'=>['style'=>'width: 140px; text-align: center;'],
        'header' => Yii::t('app','Actions'),
        'template' => '{update} &nbsp; {delete}',
        'buttons' => [
            'update' => function ($url,$model,$key) {
                /* @var $model \app\models\Tariff */
                return Html::a('<span class="glyphicon glyphicon-pencil"></span>', Url::to(['/admin/tariffs/update', 'id' => $model->id]), ['title' => Yii::t('app','Edit')]);
            },
        ],
        'visibleButtons' => [
            'delete' => function ($model, $key, $index) use ($user) {/* @var $model \app\models\Tariff */ return Access::has($user,'tariffs','delete');},
            'update' => function ($model, $key, $index) use ($user) {/* @var $model \app\models\Tariff */ return Access::has($user,'tariffs','update');},
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
            <?php if(Access::has($user,'tariffs','create')): ?>
                <div class="box-footer">
                    <a href="<?php echo Url::to(['/admin/tariffs/create']); ?>" class="btn btn-primary"><?= Yii::t('app','Create'); ?></a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
