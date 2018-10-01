<?php
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use vova07\imperavi\Widget as RedactorWidget;

/* @var $model \app\models\forms\SettingsForm */
?>


<?php $form = ActiveForm::begin([
    'action' => Url::to(['/admin/settings/index', 'tab' => 'notifications']),
    'id' => 'settings-notifications',
    'options' => ['role' => 'form', 'method' => 'post', 'enctype' => 'multipart/form-data'],
    'enableClientValidation' => false,
    'fieldConfig' => [
        'template' => "{label}\n{input}\n{error}\n",
        //'labelOptions' => ['class' => 'col-lg-1 control-label'],
    ],
]); ?>

    <div class="alert alert-info alert-dismissible">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
        <h4><i class="icon fa fa-info"></i> <?= Yii::t('app','Information'); ?></h4>
        <?= Yii::t('app','You can use following variables : {variables}', ['variables' => '<code>{id}</code>,<code>{name}</code>,<code>{status}</code>']); ?>
    </div>

<?= $form->field($model,'notification_template_advertisement_confirmation_fb')->textarea(); ?>
<?= $form->field($model,'notification_template_advertisement_confirmation_email')->widget(RedactorWidget::class,[
    'name' => 'notification_template_advertisement_confirmation_email',
    'class' => 'form-control',
    'settings' => [
        'lang' => Yii::$app->language,
        'minHeight' => 200,
        'plugins' => [
            'fullscreen',
            'fontsize',
            'fontcolor',
            'table'
        ],
    ],
]); ?>

<hr>

    <div class="alert alert-info alert-dismissible">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
        <h4><i class="icon fa fa-info"></i> <?= Yii::t('app','Information'); ?></h4>
        <?= Yii::t('app','You can use following variables : {variables}', ['variables' => '<code>{group_name}</code>,<code>{request_id}</code>,<code>{status}</code>']); ?>
    </div>

<?= $form->field($model,'notification_template_marketplace_confirmation_fb')->textarea(); ?>
<?= $form->field($model,'notification_template_marketplace_confirmation_email')->widget(RedactorWidget::class,[
    'name' => 'notification_template_marketplace_confirmation_email',
    'class' => 'form-control',
    'settings' => [
        'lang' => Yii::$app->language,
        'minHeight' => 200,
        'plugins' => [
            'fullscreen',
            'fontsize',
            'fontcolor',
            'table'
        ],
    ],
]); ?>

<hr>

    <div class="alert alert-info alert-dismissible">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
        <h4><i class="icon fa fa-info"></i> <?= Yii::t('app','Information'); ?></h4>
        <?= Yii::t('app','You can use following variables : {variables}', ['variables' => '<code>{id}</code>']); ?>
    </div>

<?= $form->field($model,'notification_template_new_advertisement_fb')->textarea(); ?>
<?= $form->field($model,'notification_template_new_advertisement_email')->widget(RedactorWidget::class,[
    'name' => 'notification_template_new_advertisement_email',
    'class' => 'form-control',
    'settings' => [
        'lang' => Yii::$app->language,
        'minHeight' => 200,
        'plugins' => [
            'fullscreen',
            'fontsize',
            'fontcolor',
            'table'
        ],
    ],
]); ?>

    <button type="submit" class="btn btn-primary"><?= Yii::t('app','Save'); ?></button>

<?php ActiveForm::end(); ?>