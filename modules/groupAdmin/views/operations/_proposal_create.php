<?php
use yii\bootstrap\ActiveForm;

/* @var $model \app\models\PayoutProposal*/
/* @var $this \yii\web\View */
/* @var $user \app\models\User */
/* @var $controller \app\modules\admin\controllers\OperationsController */

$controller = $this->context;
$user = Yii::$app->user->identity;
?>

    <div class="modal-header">
        <h4 class="modal-title"><?= Yii::t('app','Create proposal for withdrawal'); ?></h4>
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
        <?php $model->amount = \app\helpers\Help::toPrice($model->amount); ?>
        <?= $form->field($model,'amount')->textInput(['disabled' => !$model->isNewRecord]); ?>
        <?= $form->field($model,'description')->textarea(); ?>
    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-default pull-left" data-dismiss="modal"><?= Yii::t('app','Cancel'); ?></button>
        <button type="submit" class="btn btn-primary"><?= Yii::t('app','Create'); ?></button>
    </div>

<?php ActiveForm::end(); ?>