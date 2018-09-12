<?php

use kartik\grid\GridView;
use app\helpers\Help;
use app\helpers\Constants;
use kartik\daterange\DateRangePicker;
use yii\helpers\Url;

/* @var $searchModel \app\models\MoneyTransactionSearch */
/* @var $dataProvider \yii\data\ActiveDataProvider */
/* @var $this \yii\web\View */
/* @var $controller \app\modules\user\controllers\OperationsController */
/* @var $user \app\models\User */

$controller = $this->context;
$user = Yii::$app->user->identity;

$this->title = Yii::t('app','Payment history');
$this->params['breadcrumbs'][] = $this->title;

$memberAccount = $user->getMoneyAccount(Constants::MEMBER_ACCOUNT);

$gridColumns = [
    [
        'attribute' => 'id',
        'contentOptions' => ['style' => 'width:70px;'],
        'headerOptions' => ['style' => 'width:70px;'],
    ],
    [
        'attribute' => 'amount',
        'format' => 'raw',
        'value' => function ($model, $key, $index, $column)use($memberAccount){
            /* @var $model \app\models\MoneyTransaction */
            return ($model->isIncomeFor($memberAccount->id) ? '+' : '-').Help::toPrice($model->amount).' ₽';
        },
    ],
    [
        'attribute' => 'description',
    ],
    [
        'attribute' => 'web_payment_type_id',
        'format' => 'raw',
        'value' => function ($model, $key, $index, $column)use($memberAccount){
            /* @var $model \app\models\MoneyTransaction */
            return !empty($model->webPaymentType) ? $model->webPaymentType->getYmName() : null;
        },
    ],
    [
        'attribute' => 'type_id',
        'filter' => [
            Constants::PAYMENT_WEB_INITIATED => Yii::t('app','Web payment (manual)'),
            Constants::PAYMENT_WEB_RECURRENT_INITIATED => Yii::t('app','Web payment (auto)'),
        ],
        'format' => 'raw',
        'value' => function ($model, $key, $index, $column)use($memberAccount){
            /* @var $model \app\models\MoneyTransaction */
            $names = [
                Constants::PAYMENT_WEB_INITIATED => Yii::t('app','Web payment (manual)'),
                Constants::PAYMENT_WEB_RECURRENT_INITIATED => Yii::t('app','Web payment (auto)'),
            ];
            return \yii\helpers\ArrayHelper::getValue($names,$model->type_id,Yii::t('app','Unknown'));
        },
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
            <div class="box-header"><h3 class="box-title"><?= Yii::t('app','Web-payment methods'); ?></h3></div>
            <div class="box-body">
                <table class="table table-responsive">
                    <tr>
                        <th><?= Yii::t('app','Method name'); ?></th>
                        <th><?= Yii::t('app','Number of account / card / wallet'); ?></th>
                        <th><?= Yii::t('app','Action'); ?></th>
                    </tr>
                    <?php if(!empty($user->usedWebPaymentTypes)): ?>
                        <?php foreach ($user->usedWebPaymentTypes as $paymentType): ?>
                            <tr>
                                <th><?= $paymentType->getYmName(); ?></th>
                                <th><?= $paymentType->cdd_pan_mask; ?></th>
                                <th><a href="<?= Url::to(['/user/operations/remove-method', 'id' => $paymentType->id]); ?>" class="btn btn-primary"><?= Yii::t('app','Remove Payment Method'); ?></a></th>
                            </tr>
                        <?php endforeach;?>
                    <?php endif; ?>
                </table>
                <div class="alert alert-warning alert-dismissible">
                    <h4><i class="icon fa fa-warning"></i><?= Yii::t('app','Alert!'); ?></h4>
                    <?= Yii::t('app','You currently don\'t have any valid payment methods. Your active placements (if any) will be automatically deactivated at the end of the placement period'); ?>
                </div>
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
