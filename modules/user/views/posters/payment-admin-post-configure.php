<?php
use yii\helpers\Url;
use app\models\User;
use yii\bootstrap\ActiveForm;
use kartik\widgets\DateTimePicker;
use app\helpers\Constants;

/* @var $model \app\models\Poster*/
/* @var $this \yii\web\View */
/* @var $user User */


$user = Yii::$app->user->identity;

$this->title = Yii::t('app','Configure publication time');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app','Advertisements'), 'url' => Url::to(['/user/posters/index'])];
$this->params['breadcrumbs'][] = ['label' => Yii::t('app','Edit advertisement'), 'url' => Url::to(['/user/posters/update', 'id' => $model->id])];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row">
    <div class="col-md-12">

        <div class="box box-primary">
            <div class="box-header with-border"><h3 class="box-title"><?= Yii::t('app','Payment information'); ?></h3></div>
            <div class="box-body">
                <p><strong><?= Yii::t('app','Tariff'); ?>:</strong> <?= $model->marketplaceTariff->getNameWithDetails(true); ?></p>
                <p><strong><?= Yii::t('app','Subscription'); ?>:</strong>
                    <?= $model->marketplaceTariff->tariff->subscription ? '<span class="label label-success">'.Yii::t('app','Yes').'</span>' : '<span class="label label-danger">'.Yii::t('app','No').'</span>'; ?>
                </p>
                <hr>

                <div class="alert alert-warning alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    <h4><i class="icon fa fa-warning"></i> <?= Yii::t('app','Alert!'); ?></h4>
                    <?= Yii::t('app','If you have chosen the tariff "Post from admin", you should specify the preferred time of placement and wait for confirmation. Only after your ad be approved - you can pay for it.'); ?>
                </div>

                <p>
                    <strong><?= Yii::t('app','Review status'); ?>:</strong>
                    <?php if($model->admin_post_time_approve_status == Constants::ADMIN_POST_TIME_AT_REVIEW): ?>
                        <?= '<span class="label label-warning">'.Yii::t('app','At review').'</span>'; ?>
                    <?php elseif($model->admin_post_time_approve_status == Constants::ADMIN_POST_TIME_APPROVED): ?>
                        <?= '<span class="label label-success">'.Yii::t('app','Approved').'</span>'; ?>
                    <?php else: ?>
                        <?= '<span class="label label-danger">'.Yii::t('app','Disapproved').'</span>'; ?>
                    <?php endif; ?>
                </p>

                <?php if($model->admin_post_time_approve_status == Constants::ADMIN_POST_TIME_DISAPPROVED): ?>
                    <p><strong><?= Yii::t('app','Disapprove reason'); ?>:</strong> <?= !empty($model->admin_post_disapprove_reason) ? $model->admin_post_disapprove_reason : Yii::t('app','Not set'); ?></p>
                <?php endif; ?>

                <?php $form = ActiveForm::begin([
                    'id' => 'configure-admin-post-time-form',
                    'options' => ['role' => 'form', 'method' => 'post', 'enctype' => 'multipart/form-data'],
                    'enableClientValidation'=>false,
                    'enableAjaxValidation' => true,
                    'fieldConfig' => [
                        'template' => "{label}\n{input}\n{error}\n",
                        //'labelOptions' => ['class' => 'col-lg-1 control-label'],
                    ],
                ]); ?>

                <?= $form->field($model,'admin_post_time')->widget(DateTimePicker::class, [
                    'options' => ['placeholder' => Yii::t('app','Enter publication time')],
                    'pluginOptions' => [
                        'autoclose' => true,
                        'format' => 'yyyy-mm-dd hh:ii',
                        'startDate' => date('Y-m-d H:i'),
                        'todayHighlight' => true,
                    ]
                ]); ?>

                <button type="submit" class="btn btn-primary"><?= Yii::t('app','Update'); ?></button>
                <a class="btn btn-primary" href="<?= Url::to(['/user/posters/payment', 'id' => $model->id]); ?>"><?= Yii::t('app','Refresh'); ?></a>

                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>