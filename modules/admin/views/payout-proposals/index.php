<?php

use kartik\grid\GridView;
use yii\helpers\Url;
use yii\bootstrap\Html;
use app\modules\admin\helpers\Access;
use kartik\select2\Select2;
use yii\web\JsExpression;
use app\helpers\Constants;

/* @var $searchModel \app\models\PayoutProposalSearch */
/* @var $dataProvider \yii\data\ActiveDataProvider */
/* @var $this \yii\web\View */
/* @var $controller \app\modules\admin\controllers\PayoutProposalsController */
/* @var $user \app\models\User */

$controller = $this->context;
$user = Yii::$app->user->identity;

$this->title = Yii::t('app','Payout proposals');
$this->params['breadcrumbs'][] = $this->title;

$gridColumns = [
    [
        'attribute' => 'id',
        'contentOptions' => ['style' => 'width:70px;']
    ],

    [
        'attribute' => 'amount',
        'format' => 'raw',
        'value' => function ($model, $key, $index, $column){
            /* @var $model \app\models\MoneyAccount */
            return \app\helpers\Help::toPrice($model->amount).' ₽';
        },
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
            /* @var $model \app\models\MoneyAccount */
            return !empty($model->user) ? $model->user->name : null;
        },
    ],

    [
        'attribute' => 'status_id',
        'filter' => [
            Constants::PAYMENT_STATUS_NEW => Yii::t('app','New (in progress)'),
            Constants::PAYMENT_STATUS_DONE => Yii::t('app','Done'),
            Constants::PAYMENT_STATUS_CANCELED => Yii::t('app','Canceled'),
        ],
        'enableSorting' => false,
        'format' => 'raw',
        'value' => function ($model, $key, $index, $column){
            /* @var $model \app\models\MoneyTransactionSearch */
            $names = [
                Constants::PAYMENT_STATUS_NEW => '<span class="label label-warning">'.Yii::t('app','New (in progress)').'</span>',
                Constants::PAYMENT_STATUS_DONE => '<span class="label label-success">'.Yii::t('app','Done').'</span>',
                Constants::PAYMENT_STATUS_CANCELED => '<span class="label label-danger">'.Yii::t('app','Canceled').'</span>',
            ];
            return !empty($names[$model->status_id]) ? $names[$model->status_id] : null;
        },
    ],


    [
        'class' => 'yii\grid\ActionColumn',
        'contentOptions'=>['style'=>'width: 140px; text-align: center;'],
        'header' => Yii::t('app','Actions'),
        'template' => '{update}',
        'buttons' => [
            'update' => function ($url,$model,$key) {
                /* @var $model \app\models\MoneyAccount */
                return Html::a('<span class="glyphicon glyphicon-check"></span>', Url::to(['/admin/payout-proposals/update', 'id' => $model->id]), ['data-toggle' => 'modal', 'data-target' => '.modal-main', 'title' => Yii::t('app','Review')]);
            },
        ],
        'visibleButtons' => [
            'operations' => function ($model, $key, $index) use ($user) {/* @var $model \app\models\MoneyAccount */ return Access::has($user,'operations','index');},
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
