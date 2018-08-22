<?php
use yii\bootstrap\ActiveForm;

/* @var $model \app\models\SourceMessage */
/* @var $this \yii\web\View */
/* @var $controller \app\modules\admin\controllers\LabelsController */
/* @var $languages \app\models\Language[] */
/* @var $user \app\models\User */

$languages = \app\models\Language::find()->all();
$controller = $this->context;

$user = Yii::$app->user->identity;
?>

    <div class="modal-header">
        <h4 class="modal-title"><?= Yii::t('app','Label translation'); ?></h4>
    </div>

<?php $form = ActiveForm::begin([
    'id' => 'edit-language-label-form',
    'options' => ['role' => 'form', 'method' => 'post', 'enctype' => 'multipart/form-data'],
    'enableAjaxValidation'=>true,
    'fieldConfig' => [
        'template' => "{label}\n{input}\n{error}\n",
    ],
]); ?>

    <div class="modal-body">
        <?= $form->field($model, 'message')->textarea(); ?>

        <?php if(!empty($languages)): ?>
            <?php foreach($languages as $lng): ?>
                <div class="form-group field-label_trl-word">
                    <label class="control-label" for="label_trl-word_<?= $lng->prefix; ?>"><?= $lng->self_name.' ('.$lng->prefix.')'; ?></label>
                    <textarea id="label_trl-word_<?= $lng->prefix; ?>" class="form-control" name="SourceMessage[translations][<?= $lng->prefix; ?>]"><?= $model->getTranslatedText($lng->prefix); ?></textarea>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-default pull-left" data-dismiss="modal"><?= Yii::t('app','Close'); ?></button>
        <button type="submit" class="btn btn-primary"><?= Yii::t('app','Save') ?></button>
    </div>

<?php ActiveForm::end(); ?>