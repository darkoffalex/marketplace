<?php
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use app\models\Country;
use app\helpers\Constants;
use kartik\select2\Select2;
use yii\web\JsExpression;

/* @var $model \app\models\Marketplace*/
/* @var $this \yii\web\View */
/* @var $controller \app\modules\admin\controllers\MarketplacesController */
/* @var $flags array[] */

$this->registerJsFile(Url::to('@web/common/js/show-hide.js'));
$controller = $this->context;

$countries = Country::find()
    ->where(['status_id' => Constants::STATUS_ENABLED])
    ->orderBy('priority ASC')
    ->all();
?>

    <div class="modal-header">
        <h4 class="modal-title"><?= Yii::t('app','Create marketplace'); ?></h4>
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
        <?= $form->field($model,'name')->textInput()->label(Yii::t('app','Name (reference to group name)')); ?>

        <?= $form->field($model,'group_url')->textInput(); ?>

        <?= $form->field($model,'group_admin_profile')->textInput(); ?>

        <?= $form->field($model,'domain_alias')->textInput(); ?>

        <?= $form->field($model,'country_id')->dropDownList(ArrayHelper::map($countries,'id','name')); ?>

        <?= $form->field($model, 'status_id')->dropDownList([
            Constants::USR_STATUS_ENABLED => Constants::GetStatusName(Constants::USR_STATUS_ENABLED),
            Constants::USR_STATUS_DISABLED => Constants::GetStatusName(Constants::USR_STATUS_DISABLED),
        ]); ?>

        <?= $form->field($model,'timezone')->dropDownList(\app\helpers\Help::getTimeZoneArray()); ?>

        <?= $form->field($model,'geo')->dropDownList(ArrayHelper::map($countries,'id','name')); ?>

        <?= $form->field($model,'user_id')->widget(Select2::class,[
            'initValueText' => !empty($model->user) ? $model->user->name : '',
            'options' => ['placeholder' => Yii::t('app','Search for a user...')],
            'language' => Yii::$app->language,
            'theme' => Select2::THEME_DEFAULT,
            'pluginOptions' => [
                'allowClear' => true,
                'minimumInputLength' => 1,
                'language' => [
                    'noResults' => new JsExpression("function () { return '".Yii::t('app','No results found')."'; }"),
                    'searching' => new JsExpression("function () { return '".Yii::t('app','Searching...')."'; }"),
                    'inputTooShort' => new JsExpression("function(args) {return '".Yii::t('app','Type more characters')."'}"),
                    'errorLoading' => new JsExpression("function () { return '".Yii::t('app','Waiting for results')."'; }"),
                ],
                'ajax' => [
                    'url' => Url::to(['/admin/users/ajax-search']),
                    'dataType' => 'json',
                    'data' => new JsExpression('function(params) { return {q:params.term}; }')
                ],
                'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                'templateResult' => new JsExpression('function(user) { return user.text; }'),
                'templateSelection' => new JsExpression('function (user) { return user.text; }'),
            ],
        ]); ?>

        <?= $form->field($model,'selling_rules')->textarea(); ?>

        <?= $form->field($model,'display_empty_categories')->checkbox(); ?>

        <?= $form->field($model,'pm_theme_description')->textarea(); ?>

        <?= $form->field($model,'admin_phone_wa')->textInput(); ?>

        <?= $form->field($model,'group_description')->textarea(); ?>
    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-default pull-left" data-dismiss="modal"><?= Yii::t('app','Cancel'); ?></button>
        <button type="submit" class="btn btn-primary"><?= Yii::t('app','Next (configure rates and images)'); ?></button>
    </div>

<?php ActiveForm::end(); ?>