<?php
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use app\helpers\Help;

/* @var $model \app\models\forms\SettingsForm */
?>


<?php $form = ActiveForm::begin([
    'action' => Url::to(['/admin/settings/index', 'tab' => 'common']),
    'id' => 'settings-common',
    'options' => ['role' => 'form', 'method' => 'post', 'enctype' => 'multipart/form-data'],
    'enableClientValidation' => false,
    'fieldConfig' => [
        'template' => "{label}\n{input}\n{error}\n",
        //'labelOptions' => ['class' => 'col-lg-1 control-label'],
    ],
]); ?>

<?php $model->payout_min_sum = Help::toPrice($model->payout_min_sum); ?>
<?= $form->field($model,'payout_min_sum')->textInput(); ?>

    <button type="submit" class="btn btn-primary"><?= Yii::t('app','Save'); ?></button>

<?php ActiveForm::end(); ?>