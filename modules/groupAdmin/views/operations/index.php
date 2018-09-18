<?php

use kartik\grid\GridView;
use app\helpers\Help;
use kartik\daterange\DateRangePicker;
use app\helpers\Constants;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $searchModel \app\models\MoneyTransactionSearch */
/* @var $dataProvider \yii\data\ActiveDataProvider */
/* @var $this \yii\web\View */
/* @var $controller \app\modules\groupAdmin\controllers\OperationsController */
/* @var $dataProviderPayouts \yii\data\ActiveDataProvider */
/* @var $searchModelPayouts \app\models\MoneyTransactionSearch */
/* @var $user \app\models\User */
/* @var $account \app\models\MoneyAccount */

$controller = $this->context;
$user = Yii::$app->user->identity;

$this->title = Yii::t('app','Payment history');
$this->params['breadcrumbs'][] = $this->title;

$gridColumns = [
    [
        'attribute' => 'id',
        'contentOptions' => ['style' => 'width:70px;'],
        'headerOptions' => ['style' => 'width:70px;'],
    ],
    [
        'attribute' => 'amount',
        'format' => 'raw',
        'value' => function ($model, $key, $index, $column)use($account){
            /* @var $model \app\models\MoneyTransaction */
            return ($model->isIncomeFor($account->id) ? '+' : '-').Help::toPrice($model->amount).' ₽';
        },
    ],
    [
        'attribute' => 'description',
    ],
    [
        'attribute' => 'created_at',
        'filter' => DateRangePicker::widget([
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
            /* @var $model \app\models\MoneyTransaction */
            return !empty($model->created_at) ? Help::dateReformat($model->created_at) : null;
        },
    ],
];

$gridColumnsPayouts = [
    [
        'attribute' => 'id',
        'contentOptions' => ['style' => 'width:70px;'],
        'headerOptions' => ['style' => 'width:70px;'],
    ],
    [
        'attribute' => 'amount',
        'format' => 'raw',
        'value' => function ($model, $key, $index, $column)use($account){
            /* @var $model \app\models\MoneyTransaction */
            return Help::toPrice($model->amount).' ₽';
        },
    ],
    [
        'attribute' => 'description',
    ],
    [
        'attribute' => 'created_at',
        'filter' => DateRangePicker::widget([
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
            /* @var $model \app\models\MoneyTransaction */
            return !empty($model->created_at) ? Help::dateReformat($model->created_at) : null;
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
        'template' => '{info} {proposal-delete}',
        'buttons' => [
            'info' => function ($url,$model,$key) {
                /* @var $model \app\models\MoneyTransactionSearch */
                return Html::a('<span class="glyphicon glyphicon-info-sign"></span>', Url::to(['/group-admin/operations/proposal-info', 'id' => $model->id]), ['title' => Yii::t('app','View information'), 'data-target' => '.modal-main', 'data-toggle'=>'modal']);
            },
            'proposal-delete' => function ($url,$model,$key) {
                /* @var $model \app\models\MoneyTransactionSearch */
                return Html::a('<span class="glyphicon glyphicon-trash"></span>', Url::to(['/group-admin/operations/proposal-delete', 'id' => $model->id]), ['title' => Yii::t('app','Delete proposal'), 'data-confirm' => Yii::t('app','Are you sure? Payout will be cancelled and your money be returned to your account.')]);
            },
        ],
    ],
];


?>

<div class="row">
    <div class="col-xs-12">
        <div class="box">
            <div class="box-header"><h3 class="box-title"><?= Yii::t('app','Account details'); ?></h3></div>
            <div class="box-body">
                 <h4><?= Yii::t('app','Balance'); ?>: <?= Help::toPrice($account->amount).' ₽'; ?></h4>
            </div>
            <div class="box-footer">
                <a class="btn btn-primary" data-target=".modal-main" data-toggle="modal" href="<?= Url::to(['/group-admin/operations/new-proposal']); ?>"><?= Yii::t('app','Withdrawal'); ?></a>
            </div>
        </div>

        <div class="box">
            <div class="box-header"><h3 class="box-title"><?= Yii::t('app','Payout proposals'); ?></h3></div>
            <div class="box-body">
                <?= GridView::widget([
                    'filterModel' => $searchModelPayouts,
                    'dataProvider' => $dataProviderPayouts,
                    'columns' => $gridColumnsPayouts,
                    'bordered' => false,
                    'pjax' => false,
                ]); ?>
            </div>
        </div>

        <div class="box">
            <div class="box-header"><h3 class="box-title"><?= Yii::t('app','History of operations'); ?></h3></div>
            <div class="box-body">
                <?= GridView::widget([
                    'filterModel' => $searchModel,
                    'dataProvider' => $dataProvider,
                    'columns' => $gridColumns,
                    'bordered' => false,
                    'pjax' => false,
                ]); ?>
            </div>
        </div>
    </div>
</div>
