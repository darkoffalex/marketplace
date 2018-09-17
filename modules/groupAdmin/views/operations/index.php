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
            <div class="box-footer">
                <a class="btn btn-primary" data-target=".modal-main" data-toggle="modal" href="<?= Url::to(['/group-admin/operations/new-proposal']); ?>"><?= Yii::t('app','Withdrawal'); ?></a>
            </div>
        </div>

        <?php if($user->getPayoutProposals()->count() > 0): ?>
            <div class="box">
                <div class="box-header"><h3 class="box-title"><?= Yii::t('app','Payout proposals'); ?></h3></div>
                <div class="box-body table-responsive">
                    <table class="table">
                        <tbody>
                        <tr>
                            <th style="width: 10px">#</th>
                            <th><?= Yii::t('app','Amount'); ?></th>
                            <th><?= Yii::t('app','Created'); ?></th>
                            <th style="width: 40px"><?= Yii::t('app','Status'); ?></th>
                            <th><?= Yii::t('app','Actions'); ?></th>
                        </tr>
                        <?php foreach ($user->payoutProposals as $payout): ?>
                            <tr>
                                <td><?= $payout->id; ?></td>
                                <td><?= Help::toPrice($payout->amount); ?></td>
                                <td><?= Help::dateReformat($payout->created_at); ?></td>
                                <td>
                                    <?php if($payout->status_id == Constants::PAYMENT_STATUS_NEW): ?>
                                        <span class="label label-warning"><?= Yii::t('app','New'); ?></span>
                                    <?php elseif($payout->status_id == Constants::PAYMENT_STATUS_DONE): ?>
                                        <span class="label label-success"><?= Yii::t('app','Done'); ?></span>
                                    <?php else: ?>
                                        <span class="label label-danger"><?= Yii::t('app','Refused'); ?></span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?= Html::a('<span class="glyphicon glyphicon-info-sign"></span>', Url::to(['/group-admin/operations/proposal-info', 'id' => $payout->id]), ['title' => Yii::t('app','View information'), 'data-target' => '.modal-main', 'data-toggle'=>'modal']); ?>
                                    <?php if($payout->status_id != Constants::PAYMENT_STATUS_DONE): ?>
                                        <?= Html::a('<span class="glyphicon glyphicon-trash"></span>', Url::to(['/group-admin/operations/proposal-delete', 'id' => $payout->id]), ['title' => Yii::t('app','Delete proposal'), 'data-confirm' => Yii::t('app','Are you sure? Payout will be cancelled and your money be returned to your account.')]); ?>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>

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
