<?php
use yii\bootstrap\ActiveForm;
use app\helpers\Help;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

/* @var $model \app\models\MarketplaceTariffPrice */
/* @var $this \yii\web\View */
/* @var $controller \app\modules\admin\controllers\MarketplacesController */
/* @var $tariffs \app\models\Tariff[] */
/* @var $preselect int */
/* @var $marketplace \app\models\Marketplace */
$controller = $this->context;
?>

    <div class="modal-header">
        <h4 class="modal-title"><?= Yii::t('app', 'Tariff attachment'); ?></h4>
    </div>

<?php $form = ActiveForm::begin([
    'id' => 'create-edit-form',
    'options' => ['role' => 'form', 'method' => 'post', 'enctype' => 'multipart/form-data'],
    'enableClientValidation'=> false,
    'enableAjaxValidation' => true,
    'fieldConfig' => [
        'template' => "{label}\n{input}\n{error}\n",
        //'labelOptions' => ['class' => 'col-lg-1 control-label'],
    ],
]); ?>

    <div class="modal-body">
        <?= $form->field($model,'tariff_id')->dropDownList(ArrayHelper::map($tariffs,'id','name'))->label('Base tariff'); ?>
        <a data-target=".modal-create-tariff" data-toggle="modal" class="btn btn-primary btn-xs" href="<?= Url::to(['/admin/marketplaces/create-new-tariff', 'normal' => 1]); ?>"><?= Yii::t('app','New tariff'); ?></a>

        <?php $model->price = Help::toPrice($model->price); ?>
        <?= $form->field($model,'price')->textInput(); ?>

        <?php $model->discounted_price = Help::toPrice($model->discounted_price); ?>
        <?= $form->field($model,'discounted_price')->textInput(); ?>
    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-default pull-left" data-dismiss="modal"><?= Yii::t('app','Cancel'); ?></button>
        <button type="submit" class="btn btn-primary"><?= Yii::t('app','Save'); ?></button>
    </div>

<?php ActiveForm::end(); ?>
