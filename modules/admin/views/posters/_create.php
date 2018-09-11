<?php
use yii\bootstrap\ActiveForm;
use kartik\select2\Select2;
use yii\web\JsExpression;
use yii\helpers\Url;
use app\models\Marketplace;
use yii\helpers\ArrayHelper;
use app\helpers\Constants;

/* @var $model \app\models\Poster */
/* @var $this \yii\web\View */
/* @var $controller \app\modules\admin\controllers\CountriesController */

/* @var $marketplaces Marketplace[] */
$marketplaces = Marketplace::find()->all();
$controller = $this->context;
?>

    <div class="modal-header">
        <h4 class="modal-title"><?= Yii::t('app','Create advertisement'); ?></h4>
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
        <div class="alert alert-warning alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            <h4><i class="icon fa fa-warning"></i> <?= Yii::t('app','Alert!'); ?></h4>
            <?= Yii::t('app','If selected user have no access to selected marketplace, access will be granted by force'); ?>
        </div>

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

        <?= $form->field($model,'marketplace_id')->dropDownList(ArrayHelper::map($marketplaces,'id','name')); ?>

        <?= $form->field($model,'status_id')->dropDownList([
            Constants::STATUS_ENABLED => Yii::t('app','Enabled'),
            Constants::STATUS_DISABLED => Yii::t('app','Disabled'),
        ]); ?>
    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-default pull-left" data-dismiss="modal"><?= Yii::t('app','Cancel'); ?></button>
        <button type="submit" class="btn btn-primary"><?= Yii::t('app','Next'); ?></button>
    </div>

<?php ActiveForm::end(); ?>