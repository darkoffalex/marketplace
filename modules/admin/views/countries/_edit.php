<?php
use yii\bootstrap\ActiveForm;
use app\helpers\Constants;
use kartik\select2\Select2;
use yii\web\JsExpression;
use yii\helpers\Url;

/* @var $model \app\models\Country*/
/* @var $this \yii\web\View */
/* @var $controller \app\modules\admin\controllers\CountriesController */
/* @var $flags array[] */

$controller = $this->context;
?>

    <div class="modal-header">
        <h4 class="modal-title"><?= $model->isNewRecord ? Yii::t('app','Create country') : Yii::t('app','Edit country'); ?></h4>
    </div>

<?php $form = ActiveForm::begin([
    'id' => 'create-edit-form',
    'options' => ['role' => 'form', 'method' => 'post', 'enctype' => 'multipart/form-data'],
    'enableClientValidation'=> false,
    'enableAjaxValidation' => true,
    'fieldConfig' => [
        'template' => "{label}\n{input}\n{error}\n",
        //'labelOptions' => ['class' => 'col-lg-1 control-label'],
    ],
]); ?>

    <div class="modal-body">
        <?= $form->field($model, 'name')->textInput(); ?>

        <?= $form->field($model, 'domain_alias')->textInput(); ?>

        <?= $form->field($model, 'status_id')->dropDownList([
            Constants::STATUS_ENABLED => Yii::t('app','Enabled'),
            Constants::STATUS_DISABLED => Yii::t('app','Disabled')
        ]); ?>

        <?= $form->field($model, 'description')->textarea(); ?>

        <?= $form->field($model, 'flag_filename')->widget(Select2::class,[
            'data' => $flags,
            'theme' => Select2::THEME_BOOTSTRAP,
            'options' => ['placeholder' => Yii::t('app','Choose flag for this country')],
            'pluginOptions' => [
                'allowClear' => false,
                'templateResult' => new JsExpression('function(option){return "<img width=\'30px;\' src=\''.Url::to('@web/common/img/flags/').'" + option.id + "\'> " + option.text;}'),
                'templateSelection' => new JsExpression('function(option){return option.id ? ("<img width=\'30px;\' src=\''.Url::to('@web/common/img/flags/').'" + option.id + "\'> " + option.text) : "";}'),
                'escapeMarkup' => new JsExpression('function(m){return m;}')
            ],
        ])->label(Yii::t('app','Flag')); ?>
    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-default pull-left" data-dismiss="modal"><?= Yii::t('app','Cancel'); ?></button>
        <button type="submit" class="btn btn-primary"><?= $model->isNewRecord ? Yii::t('app','Create') : Yii::t('app','Update'); ?></button>
    </div>

<?php ActiveForm::end(); ?>