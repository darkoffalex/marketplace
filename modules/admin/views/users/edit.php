<?php
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use app\models\User;
use app\helpers\Constants;
use branchonline\lightbox\LightboxAsset;

/* @var $model User */
/* @var $this \yii\web\View */
/* @var $user User */

$this->registerAssetBundle(LightboxAsset::class);

$user = Yii::$app->user->identity;

$this->title = $model->isNewRecord ? Yii::t('app','Create new user') : Yii::t('app','Edit user');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app','Users'), 'url' => Url::to(['/admin/users/index'])];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row">
    <div class="col-md-12">

        <div class="box box-primary">
            <div class="box-header with-border"><h3 class="box-title"><?= Yii::t('app','Settings'); ?></h3></div>

            <?php $form = ActiveForm::begin([
                'id' => 'edit-users-form',
                'options' => ['role' => 'form', 'method' => 'post', 'enctype' => 'multipart/form-data'],
                'enableClientValidation'=>false,
                'fieldConfig' => [
                    'template' => "{label}\n{input}\n{error}\n",
                    //'labelOptions' => ['class' => 'col-lg-1 control-label'],
                ],
            ]); ?>


            <div class="box-body">
                <?php if(!$model->hasErrors() && Yii::$app->request->isPost): ?>
                    <div class="alert alert-success alert-dismissable">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                        <h4><i class="icon fa fa-check"></i><?= Yii::t('app','Saved'); ?></h4>
                        <?= Yii::t('app','Changes saved'); ?>
                    </div>
                <?php endif; ?>

                <?= $form->field($model, 'username')->textInput(); ?>

                <?= $form->field($model, 'password')->passwordInput(); ?>

                <?= $form->field($model, 'role_id')->dropDownList([
                    Constants::ROLE_ADMIN => Constants::GetRoleName(Constants::ROLE_ADMIN),
                    Constants::ROLE_BOOKKEEPER => Constants::GetRoleName(Constants::ROLE_BOOKKEEPER),
                    Constants::ROLE_ADMIN => Constants::GetRoleName(Constants::ROLE_ADMIN),
                ]); ?>

                <?= $form->field($model, 'status_id')->dropDownList([
                    Constants::USR_STATUS_ENABLED => Constants::GetStatusName(Constants::USR_STATUS_ENABLED),
                    Constants::USR_STATUS_DISABLED => Constants::GetStatusName(Constants::USR_STATUS_DISABLED),
                ]); ?>
                <hr>

                <?= $form->field($model, 'name')->textInput(); ?>
            </div>

            <div class="box-footer">
                <a class="btn btn-primary" href="<?= Url::to(['/admin/users/index']); ?>"><?= Yii::t('app','Back to list'); ?></a>
                <button type="submit" class="btn btn-primary"><?= Yii::t('app','Save'); ?></button>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>