<?php

use kartik\grid\GridView;
use app\helpers\Help;
use kartik\daterange\DateRangePicker;

/* @var $searchModel \app\models\MoneyTransactionSearch */
/* @var $dataProvider \yii\data\ActiveDataProvider */
/* @var $this \yii\web\View */
/* @var $controller \app\modules\groupAdmin\controllers\OperationsController */
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

?>

<div class="row">
    <div class="col-xs-12">
        <div class="box">
            <div class="box-header"><h3 class="box-title"><?= Yii::t('app','Account details'); ?></h3></div>
            <div class="box-body">
                 <h4><?= Yii::t('app','Balance'); ?>: <?= Help::toPrice($account->amount).' ₽'; ?></h4>
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
