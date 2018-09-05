<?php
use yii\bootstrap\ActiveForm;

/* @var $model \app\models\forms\BindMarketplaceForm*/
/* @var $this \yii\web\View */
/* @var $controller \app\modules\user\controllers\MarketplacesController */
/* @var $user \app\models\User */

$user= Yii::$app->user->identity;
$controller = $this->context;
?>

    <div class="modal-header">
        <h4 class="modal-title"><?= Yii::t('app','Bind new marketplace'); ?></h4>
    </div>

<?php $form = ActiveForm::begin([
    'id' => 'bind-marketplace-form',
    'options' => ['role' => 'form', 'method' => 'post', 'enctype' => 'multipart/form-data'],
    'enableClientValidation'=> false,
    'enableAjaxValidation' => true,
    'fieldConfig' => [
        'template' => "{label}\n{input}\n{error}\n",
        //'labelOptions' => ['class' => 'col-lg-1 control-label'],
    ],
]); ?>

    <div class="modal-body">
        <?= $form->field($model,'code')->textInput(); ?>
    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-default pull-left" data-dismiss="modal"><?= Yii::t('app','Cancel'); ?></button>
        <button type="submit" class="btn btn-primary"><?= Yii::t('app','Bind'); ?></button>
    </div>

<?php ActiveForm::end(); ?>