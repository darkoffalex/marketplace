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

$this->title = Yii::t('app','Countries');
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
        'attribute' => 'user_id',
        'enableSorting' => false,
        'filter' => Select2::widget([
            'model' => $searchModel,
            'attribute' => 'user_id',
            'initValueText' => !empty($searchModel->user) ? $searchModel->user->name : '',
            'options' => ['placeholder' => Yii::t('app','Search for a user...')],
            'language' => Yii::$app->language,
            'theme' => Select2::THEME_DEFAULT,
            'pluginOptions' => [
                'allowClear' => true,
                'minimumInputLength' => 2,
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
            ],
        ]),
        'format' => 'raw',
        'value' => function ($model, $key, $index, $column){
            /* @var $model \app\models\CvSearch */
            return !empty($model->user) ? $model->user->name : null;
        },
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
        'template' => '{update} &nbsp; {delete} &nbsp; {move-up} &nbsp; {move-down}',
        'buttons' => [
            'update' => function ($url,$model,$key) {
                /* @var $model \app\models\Cv */
                return Html::a('<span class="glyphicon glyphicon-pencil"></span>', Url::to(['/admin/cvs/update', 'id' => $model->id]), ['title' => Yii::t('app','Edit'), 'data-target' => '.modal-main', 'data-toggle'=>'modal']);
            },
        ],
        'visibleButtons' => [
            'delete' => function ($model, $key, $index) use ($user) {/* @var $model \app\models\Cv */ return Access::has($user,'cvs','delete');},
            'update' => function ($model, $key, $index) use ($user) {/* @var $model \app\models\Cv */ return Access::has($user,'cvs','update');},
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
            <?php if(Access::has($user,'cvs','create')): ?>
                <div class="box-footer">
                    <a href="<?php echo Url::to(['/admin/cvs/create']); ?>" data-toggle="modal" data-target=".modal-main" class="btn btn-primary"><?= Yii::t('app','Create'); ?></a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
