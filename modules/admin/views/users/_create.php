<?php
use yii\bootstrap\ActiveForm;
use app\helpers\Constants;

/* @var $model \app\models\User*/
/* @var $this \yii\web\View */
/* @var $user \app\models\User */
/* @var $controller \app\modules\admin\controllers\UsersController */

$controller = $this->context;
$user = Yii::$app->user->identity;
?>

    <div class="modal-header">
        <h4 class="modal-title"><?= Yii::t('app','Create new user'); ?></h4>
    </div>

<?php $form = ActiveForm::begin([
    'id' => 'create-post-form',
    'options' => ['role' => 'form', 'method' => 'post', 'enctype' => 'multipart/form-data'],
    'enableClientValidation'=>false,
    'enableAjaxValidation' => true,
    'fieldConfig' => [
        'template' => "{label}\n{input}\n{error}\n",
        //'labelOptions' => ['class' => 'col-lg-1 control-label'],
    ],
]); ?>

    <div class="modal-body">
        <?= $form->field($model, 'username')->textInput(); ?>

        <?= $form->field($model, 'password')->passwordInput(); ?>

        <?= $form->field($model, 'role_id')->dropDownList([
            Constants::ROLE_ADMIN => Constants::GetRoleName(Constants::ROLE_ADMIN),
            Constants::ROLE_BOOKKEEPER => Constants::GetRoleName(Constants::ROLE_BOOKKEEPER),
            Constants::ROLE_ADMIN => Constants::GetRoleName(Constants::ROLE_ADMIN),
        ]); ?>

        <?= $form->field($model, 'status_id')->dropDownList([
            Constants::USR_STATUS_ENABLED => Constants::GetRoleName(Constants::USR_STATUS_ENABLED),
            Constants::USR_STATUS_DISABLED => Constants::GetRoleName(Constants::USR_STATUS_DISABLED),
        ]); ?>

        <?= $form->field($model, 'name')->textInput(); ?>
    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-default pull-left" data-dismiss="modal"><?= Yii::t('app','Cancel'); ?></button>
        <button type="submit" class="btn btn-primary"><?= Yii::t('app','Create'); ?></button>
    </div>

<?php ActiveForm::end(); ?>