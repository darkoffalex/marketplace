<?php
use yii\bootstrap\ActiveForm;
use app\helpers\Constants;
use yii\helpers\ArrayHelper;
use app\models\Dictionary;

/* @var $model \app\models\MonitoredGroup*/
/* @var $this \yii\web\View */
/* @var $controller \app\modules\groupAdmin\controllers\MonitoredGroupsController */

$controller = $this->context;
?>

    <div class="modal-header">
        <h4 class="modal-title"><?= $model->isNewRecord ? Yii::t('app','Create group') : Yii::t('app','Edit group'); ?></h4>
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
        <?= $form->field($model, 'facebook_id')->textInput(); ?>
        
        <?= $form->field($model, 'name')->textInput(); ?>

        <?= $form->field($model, 'status_id')->dropDownList([
            Constants::USR_STATUS_ENABLED => Yii::t('app','Enabled'),
            Constants::USR_STATUS_DISABLED => Yii::t('app','Disabled'),
        ]); ?>

        <?php $model->dictionaries_arr = array_values(ArrayHelper::map($model->dictionaries,'id','id')); ?>
        <?= $form->field($model, 'dictionaries_arr')->checkboxList(ArrayHelper::map(Dictionary::find()->where(['user_id' => Yii::$app->user->id])->all(),'id','name')) ?>
    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-default pull-left" data-dismiss="modal"><?= Yii::t('app','Cancel'); ?></button>
        <button type="submit" class="btn btn-primary"><?= Yii::t('app','Save'); ?></button>
    </div>

<?php ActiveForm::end(); ?>