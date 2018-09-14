<?php
use kartik\grid\GridView;
use app\helpers\Help;
use kartik\daterange\DateRangePicker;

/* @var $searchModel \app\models\MoneyTransactionSearch */
/* @var $this \yii\web\View */
/* @var $user \app\models\User */
/* @var $controller \app\modules\admin\controllers\AccountsController */
/* @var $account \app\models\MoneyAccount */

$controller = $this->context;
$user = Yii::$app->user->identity;
?>

<div class="modal-header">
    <h4 class="modal-title"><?= Yii::t('app','History'); ?></h4>
</div>
<div class="modal-body">
    <?= GridView::widget([
        'filterModel' => null,
        'dataProvider' => $dataProvider,
        'columns' => [
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
        ],
        'bordered' => false,
        'pjax' => true,
    ]); ?>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default pull-left" data-dismiss="modal"><?= Yii::t('app','Close'); ?></button>
</div>