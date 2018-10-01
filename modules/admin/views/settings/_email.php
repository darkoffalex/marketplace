<?php
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;

/* @var $model \app\models\forms\SettingsForm */
?>


<?php $form = ActiveForm::begin([
    'action' => Url::to(['/admin/settings/index', 'tab' => 'email']),
    'id' => 'settings-email',
    'options' => ['role' => 'form', 'method' => 'post', 'enctype' => 'multipart/form-data'],
    'enableClientValidation' => false,
    'fieldConfig' => [
        'template' => "{label}\n{input}\n{error}\n",
        //'labelOptions' => ['class' => 'col-lg-1 control-label'],
    ],
]); ?>

    <div class="alert alert-info alert-dismissible">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
        <h4><i class="icon fa fa-info"></i> <?= Yii::t('app','Important!'); ?></h4>
        <?= Yii::t('app','If you want to use SMTP, your sending email must match your SMTP login'); ?>
    </div>

<?= $form->field($model, 'email_for_sending')->textInput(); ?>
<?= $form->field($model, 'email_for_notifications')->textInput(); ?>

    <hr>
<?= $form->field($model, 'smtp_enabled')->checkbox(); ?>
<?= $form->field($model, 'smtp_host')->textInput(); ?>
<?= $form->field($model, 'smtp_login')->textInput(); ?>
<?= $form->field($model, 'smtp_password')->passwordInput(); ?>
<?= $form->field($model, 'smtp_port')->textInput(); ?>
<?= $form->field($model, 'smtp_encryption')->dropDownList([
    'ssl' => 'SSL',
    'tls' => 'TLS'
]); ?>

    <button type="submit" class="btn btn-primary"><?= Yii::t('app','Save'); ?></button>

<?php ActiveForm::end(); ?>