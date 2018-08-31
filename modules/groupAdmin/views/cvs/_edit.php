<?php
use yii\bootstrap\ActiveForm;
use app\helpers\Constants;
use yii\helpers\Url;
use app\models\Country;
use yii\helpers\ArrayHelper;
use kartik\time\TimePicker;

/* @var $model \app\models\Cv*/
/* @var $this \yii\web\View */
/* @var $controller \app\modules\admin\controllers\CvsController */
/* @var $flags array[] */
/* @var $user \app\models\User */

$this->registerJsFile(Url::to('@web/common/js/show-hide.js'));
$user= Yii::$app->user->identity;
$controller = $this->context;

$countries = Country::find()
    ->where(['status_id' => Constants::STATUS_ENABLED])
    ->orderBy('priority ASC')
    ->all();
?>

    <div class="modal-header">
        <h4 class="modal-title"><?= $model->isNewRecord ? Yii::t('app','Create CV') : Yii::t('app','Check CV'); ?></h4>
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
        <h4><?= Yii::t('app','Group information'); ?></h4>
        <?= $form->field($model,'group_name')->textInput(); ?>
        <?= $form->field($model,'group_url')->textInput(); ?>
        <?= $form->field($model,'group_admin_profile')->textInput(); ?>
        <?= $form->field($model,'group_thematics')->textInput(); ?>
        <?= $form->field($model,'group_popularity')->textInput(); ?>
        <?= $form->field($model,'group_description')->textarea(); ?>
        <?= $form->field($model,'group_geo')->dropDownList(ArrayHelper::map($countries,'name','name')); ?>
        <hr>


        <h4><?= Yii::t('app','Your contact information'); ?></h4>
        <?php $model->name = empty($model->name) ? $user->name : $model->name; ?>
        <?= $form->field($model,'name')->textInput(); ?>
        <?= $form->field($model,'is_member')->checkbox()->label( Yii::t('app','Member of "Admin.School"')); ?>
        <?= $form->field($model,'country_id')->dropDownList(ArrayHelper::map($countries,'id','name')); ?>
        <?= $form->field($model,'email')->textInput(); ?>
        <?= $form->field($model,'phone')->textInput(); ?>
        <?= $form->field($model,'has_viber')->checkbox()->label('I have Viber'); ?>
        <?= $form->field($model,'has_whatsapp')->checkbox()->label('I have WhatsApp'); ?>

        <?= $form->field($model,'timezone')->dropDownList(\app\helpers\Help::getTimeZoneArray()); ?>

        <?= $form->field($model,'comfortable_call_time')->widget(TimePicker::class,[
            'name' => 'comfortable_call_time',
            'value' => '12:00',
            'pluginOptions' => [
                'showSeconds' => false,
                'showMeridian' => false,
                'minuteStep' => 5,
            ]
        ]); ?>
    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-default pull-left" data-dismiss="modal"><?= Yii::t('app','Cancel'); ?></button>
        <button type="submit" class="btn btn-primary"><?= $model->isNewRecord ? Yii::t('app','Create') : Yii::t('app','Create'); ?></button>
    </div>

<?php ActiveForm::end(); ?>