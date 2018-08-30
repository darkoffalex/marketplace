<?php

use kartik\grid\GridView;
use yii\helpers\Url;
use app\helpers\Help;
use yii\bootstrap\Html;
use app\modules\admin\helpers\Access;
use kartik\select2\Select2;
use yii\web\JsExpression;
use app\helpers\Constants;

/* @var $searchModel \app\models\CvSearch */
/* @var $dataProvider \yii\data\ActiveDataProvider */
/* @var $this \yii\web\View */
/* @var $controller \app\modules\admin\controllers\CvsController*/
/* @var $user \app\models\User */

$controller = $this->context;
$user = Yii::$app->user->identity;

$this->title = Yii::t('app','CVs');
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
        'attribute' => 'group_name',
        'enableSorting' => false,
    ],

    [
        'attribute' => 'status_id',
        'filter' => [
            Constants::CV_STATUS_NEW => Yii::t('app','New'),
            Constants::CV_STATUS_APPROVED => Yii::t('app','Approved'),
            Constants::CV_STATUS_REJECTED => Yii::t('app','Rejected'),
        ],
        'enableSorting' => false,
        'format' => 'raw',
        'value' => function ($model, $key, $index, $column){
            /* @var $model \app\models\CvSearch */
            $names = [
                Constants::CV_STATUS_NEW => '<span class="label label-warning">'.Yii::t('app','New').'</span>',
                Constants::CV_STATUS_APPROVED => '<span class="label label-success">'.Yii::t('app','Approved').'</span>',
                Constants::CV_STATUS_REJECTED => '<span class="label label-danger">'.Yii::t('app','Rejected').'</span>'
            ];
            return !empty($names[$model->status_id]) ? $names[$model->status_id] : null;
        },
    ],

    [
        'attribute' => 'created_at',
        'filter' => \kartik\daterange\DateRangePicker::widget([
            'model' => $searchModel,
            'convertFormat' => true,
            'attribute' => 'created_at',
            'pluginOptions' => [
                'locale' => [
                    'format'=>'d.m.Y',
                    'separator'=>' - ',
                ],
            ],
        ]),
        'enableSorting' => true,
        'format' => 'raw',
        'value' => function ($model, $key, $index, $column){
            /* @var $model \app\models\CvSearch */
            return Help::dateReformat($model->created_at);
        },
    ],
    
    [
        'attribute' => 'group_popularity',
        'enableSorting' => false,
    ],
    
    [
        'class' => 'yii\grid\ActionColumn',
        'contentOptions'=>['style'=>'width: 140px; text-align: center;'],
        'header' => Yii::t('app','Actions'),
        'template' => '{delete} {view}',
        'buttons' => [
            'view' => function ($url,$model,$key) {
                /* @var $model \app\models\Cv */
                return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', Url::to(['/group-admin/cvs/view', 'id' => $model->id]), ['title' => Yii::t('app','View'), 'data-target' => '.modal-main', 'data-toggle'=>'modal']);
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
            <div class="box-footer">
                <a href="<?php echo Url::to(['/group-admin/cvs/create']); ?>" data-toggle="modal" data-target=".modal-main" class="btn btn-primary"><?= Yii::t('app','Create'); ?></a>
            </div>
        </div>
    </div>
</div>
