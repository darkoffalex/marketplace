<?php
use yii\bootstrap\ActiveForm;
use app\helpers\Constants;
use yii\helpers\Url;

/* @var $model \app\models\Tariff */
/* @var $this \yii\web\View */
/* @var $controller \app\modules\admin\controllers\MarketplacesController */
$controller = $this->context;
?>

    <div class="modal-header">
        <h4 class="modal-title"><?= Yii::t('app', 'Creating new special tariff'); ?></h4>
    </div>

<?php $form = ActiveForm::begin([
    'id' => 'create-new-tariff',
    'options' => ['role' => 'form', 'method' => 'post', 'enctype' => 'multipart/form-data'],
    'action' => Url::to(['/admin/marketplaces/create-new-tariff']),
    'enableClientValidation'=> false,
    'enableAjaxValidation' => false,
    'fieldConfig' => [
        'template' => "{label}\n{input}\n{error}\n",
    ],
]); ?>

    <div class="modal-body">
        <?= $form->field($model,'name')->textInput(); ?>
        <?= $form->field($model,'description')->textarea(); ?>

        <?= $form->field($model,'period_unit_type')->dropDownList([
            Constants::PERIOD_DAYS => Yii::t('app','Days'),
            Constants::PERIOD_WEEKS => Yii::t('app','Weeks'),
            Constants::PERIOD_MONTHS => Yii::t('app','Months'),
        ]); ?>

        <?= $form->field($model, 'period_amount')->textInput(); ?>

        <?php $model->base_price = \app\helpers\Help::toPrice($model->base_price); ?>
        <?php $model->discounted_price = \app\helpers\Help::toPrice($model->discounted_price); ?>
        <?= $form->field($model,'base_price')->textInput(); ?>
        <?= $form->field($model,'discounted_price')->textInput(); ?>

        <?= $form->field($model,'subscription')->checkbox(); ?>

        <?= $form->field($model,'special_type')->dropDownList([
            Constants::TARIFF_SUB_TYPE_REGULAR => Yii::t('app','Regular'),
            Constants::TARIFF_SUB_TYPE_ADMIN_POST => Yii::t('app','Admin\'s post'),
        ]); ?>
    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-default pull-left" data-dismiss="modal"><?= Yii::t('app','Cancel'); ?></button>
        <button type="button" class="btn btn-primary submit-ajax-btn" data-ajax-submit="#create-new-tariff"><?= Yii::t('admin','Save') ?></button>
    </div>

<?php ActiveForm::end(); ?>
