<?php
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
use app\models\MonitoredGroup;
use app\helpers\Constants;

/* @var $model \app\models\Dictionary*/
/* @var $this \yii\web\View */
/* @var $controller \app\modules\groupAdmin\controllers\DictionariesController */

$controller = $this->context;
?>

    <div class="modal-header">
        <h4 class="modal-title"><?= $model->isNewRecord ? Yii::t('app','Create dictionary') : Yii::t('app','Edit dictionary'); ?></h4>
    </div>

<?php $form = ActiveForm::begin([
    'id' => 'create-edit-form',
    'options' => ['role' => 'form', 'method' => 'post', 'enctype' => 'multipart/form-data'],
    'enableClientValidation'=>false,
    'enableAjaxValidation' => true,
    'fieldConfig' => [
        'template' => "{label}\n{input}\n{error}\n",
    ],
]); ?>

    <div class="modal-body">
        <?php if(!$model->isNewRecord): ?>
            <?= $form->field($model, 'key')->textInput(['disabled' => true]); ?>
        <?php endif; ?>

        <?= $form->field($model, 'name')->textInput(); ?>

        <?= $form->field($model, 'words')->textarea()->label(Yii::t('app','Words')." <span class='text-muted'>(".Yii::t('app','One word per line').")</span>"); ?>

        <?= $form->field($model, 'status_id')->dropDownList([
            Constants::USR_STATUS_ENABLED => Yii::t('app','Enabled'),
            Constants::USR_STATUS_DISABLED => Yii::t('app','Disabled'),
        ]); ?>

        <?php $model->groups_arr = array_values(ArrayHelper::map($model->monitoredGroups,'id','id')); ?>
        <?= $form->field($model, 'groups_arr')->checkboxList(ArrayHelper::map(MonitoredGroup::find()->where(['user_id' => Yii::$app->user->id])->all(),'id','name')) ?>
    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-default pull-left" data-dismiss="modal"><?= Yii::t('app','Cancel'); ?></button>
        <button type="submit" class="btn btn-primary"><?= Yii::t('app','Save'); ?></button>
    </div>

<?php ActiveForm::end(); ?>