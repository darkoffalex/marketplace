<?php
use yii\bootstrap\ActiveForm;
use app\helpers\Constants;
use app\helpers\Trl;

/* @var $model \app\models\Language*/
/* @var $this \yii\web\View */
/* @var $controller \app\modules\admin\controllers\LanguagesController */

$controller = $this->context;
?>

    <div class="modal-header">
        <h4 class="modal-title"><?= $model->isNewRecord ? Yii::t('app','Create language') : Yii::t('app','Edit language'); ?></h4>
    </div>

<?php $form = ActiveForm::begin([
    'id' => 'create-form',
    'options' => ['role' => 'form', 'method' => 'post', 'enctype' => 'multipart/form-data'],
    'enableClientValidation'=>false,
    'enableAjaxValidation' => true,
    'fieldConfig' => [
        'template' => "{label}\n{input}\n{error}\n",
        //'labelOptions' => ['class' => 'col-lg-1 control-label'],
    ],
]); ?>

    <div class="modal-body">
        <?= $form->field($model, 'name')->textInput(); ?>
        <?= $form->field($model, 'self_name')->textInput(); ?>
        <?= $form->field($model, 'prefix')->textInput(); ?>
        <?= $form->field($model, 'status_id')->dropDownList([
            Constants::STATUS_ENABLED => Constants::GetStatusName(Constants::STATUS_ENABLED),
            Constants::STATUS_DISABLED => Constants::GetStatusName(Constants::STATUS_DISABLED)
        ]); ?>
        <?= $form->field($model, 'is_default')->checkbox(); ?>
    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-default pull-left" data-dismiss="modal"><?= Yii::t('app','Cancel'); ?></button>
        <button type="submit" class="btn btn-primary"><?= $model->isNewRecord ? Yii::t('app','Create') : Yii::t('app','Update'); ?></button>
    </div>

<?php ActiveForm::end(); ?>