<?php
use yii\bootstrap\ActiveForm;

/* @var $model \app\models\MoneyTransaction*/
/* @var $this \yii\web\View */
/* @var $user \app\models\User */
/* @var $controller \app\modules\admin\controllers\AccountsController */

$controller = $this->context;
$user = Yii::$app->user->identity;
?>

    <div class="modal-header">
        <h4 class="modal-title"><?= Yii::t('app','Withdrawal logging'); ?></h4>
    </div>

<?php $form = ActiveForm::begin([
    'id' => 'create-edit-form',
    'options' => ['role' => 'form', 'method' => 'post', 'enctype' => 'multipart/form-data'],
    'enableClientValidation'=>false,
    'enableAjaxValidation' => true,
    'fieldConfig' => [
        'template' => "{label}\n{input}\n{error}\n",
        //'labelOptions' => ['class' => 'col-lg-1 control-label'],
    ],
]); ?>

    <div class="modal-body">
        <?php $model->amount = \app\helpers\Help::toPrice($model->amount); ?>
        <?= $form->field($model,'amount')->textInput(['disabled' => !$model->isNewRecord]); ?>

        <?= $form->field($model,'description')->textarea(); ?>

        <?= $form->field($model,'note')->textarea(); ?>
    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-default pull-left" data-dismiss="modal"><?= Yii::t('app','Cancel'); ?></button>
        <button type="submit" class="btn btn-primary"><?= Yii::t('app','Save'); ?></button>
    </div>

<?php ActiveForm::end(); ?>