<?php

use kartik\grid\GridView;
use yii\helpers\Url;
use yii\bootstrap\Html;
use app\modules\admin\helpers\Access;
use kartik\select2\Select2;
use yii\web\JsExpression;
use app\helpers\Constants;
use yii\helpers\ArrayHelper;

/* @var $searchModel \app\models\MoneyTransactionSearch */
/* @var $dataProvider \yii\data\ActiveDataProvider */
/* @var $this \yii\web\View */
/* @var $controller \app\modules\admin\controllers\AccountsController */
/* @var $user \app\models\User */

$controller = $this->context;
$user = Yii::$app->user->identity;

$this->title = Yii::t('app','Accounts');
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
            if(in_array($model->account_type_id,[
                Constants::SYSTEM_OUTGO_ACCOUNT,
                Constants::SYSTEM_INCOME_ACCOUNT
            ])){
                return Yii::t('app','SYSTEM');
            }

            return !empty($model->user) ? $model->user->name : null;
        },
    ],

    [
        'attribute' => 'account_type_id',
        'format' => 'raw',
        'filter' => [
            Constants::SYSTEM_INCOME_ACCOUNT => Yii::t('app','System income account'),
            Constants::SYSTEM_OUTGO_ACCOUNT => Yii::t('app','System outgo account'),
            Constants::GROUP_ADMIN_ACCOUNT => Yii::t('app','Group admin account'),
            Constants::MEMBER_ACCOUNT => Yii::t('app','Advertiser account'),
            Constants::MANAGER_ACCOUNT => Yii::t('app','Manager account')
        ],
        'value' => function ($model, $key, $index, $column){
            /* @var $model \app\models\MoneyAccount */
            $names = [
                Constants::SYSTEM_INCOME_ACCOUNT => Yii::t('app','System income account'),
                Constants::SYSTEM_OUTGO_ACCOUNT => Yii::t('app','System outgo account'),
                Constants::GROUP_ADMIN_ACCOUNT => Yii::t('app','Group admin account'),
                Constants::MEMBER_ACCOUNT => Yii::t('app','Advertiser account'),
                Constants::MANAGER_ACCOUNT => Yii::t('app','Manager account')
            ];
            return ArrayHelper::getValue($names,$model->account_type_id,Yii::t('app','Unknown'));
        },
    ],

    [
        'class' => 'yii\grid\ActionColumn',
        'contentOptions'=>['style'=>'width: 140px; text-align: center;'],
        'header' => Yii::t('app','Actions'),
        'template' => '{operations} {withdrawal}',
        'buttons' => [
            'operations' => function ($url,$model,$key) {
                /* @var $model \app\models\MoneyAccount */
                return Html::a('<span class="glyphicon glyphicon-hourglass"></span>', Url::to(['/admin/accounts/history', 'id' => $model->id]), ['data-toggle' => 'modal', 'data-target' => '.modal-main', 'title' => Yii::t('app','View operations')]);
            },
            'withdrawal' => function ($url,$model,$key) {
                /* @var $model \app\models\MoneyAccount */
                return Html::a('<span class="glyphicon glyphicon-credit-card"></span>', Url::to(['/admin/accounts/withdrawal', 'id' => $model->id]), ['data-toggle' => 'modal', 'data-target' => '.modal-main', 'title' => Yii::t('app','Log the withdrawal')]);
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
