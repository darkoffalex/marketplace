<?php
use yii\bootstrap\ActiveForm;
use kartik\select2\Select2;
use yii\web\JsExpression;
use app\helpers\Constants;
use yii\helpers\Url;

/* @var $model \app\models\MoneyTransaction*/
/* @var $this \yii\web\View */
/* @var $user \app\models\User */
/* @var $controller \app\modules\admin\controllers\OperationsController */

$controller = $this->context;
$user = Yii::$app->user->identity;
?>

    <div class="modal-header">
        <h4 class="modal-title"><?= Yii::t('app',$model->isNewRecord ? 'New transaction' : 'Edit transaction'); ?></h4>
    </div>

<?php $form = ActiveForm::begin([
    'id' => 'create-edit-form',
    'options' => ['role' => 'form', 'method' => 'post', 'enctype' => 'multipart/form-data'],
    'enableClientValidation'=>false,
    'enableAjaxValidation' => true,
    'fieldConfig' => [
        'template' => "{label}\n{input}\n{error}\n",
        //'labelOptions' => ['class' => 'col-lg-1 control-label'],
    ],
]); ?>

    <div class="modal-body">
        <?php $model->amount = \app\helpers\Help::toPrice($model->amount); ?>
        <?= $form->field($model,'amount')->textInput(['disabled' => !$model->isNewRecord]); ?>

        <?= $form->field($model,'from_account_id')->widget(Select2::class,[
            'initValueText' => !empty($model->fromAccount) ? $model->fromAccount->getFullName() : '',
            'options' => ['placeholder' => Yii::t('app','Search for account..')],
            'language' => Yii::$app->language,
            'theme' => Select2::THEME_DEFAULT,
            'disabled' => !$model->isNewRecord,
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
                    'url' => Url::to(['/admin/accounts/ajax-search']),
                    'dataType' => 'json',
                    'data' => new JsExpression('function(params) { return {q:params.term}; }')
                ],
                'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                'templateResult' => new JsExpression('function(item) { return item.text; }'),
                'templateSelection' => new JsExpression('function (item) { return item.text; }'),
            ],
        ])->label(Yii::t('app','Source account')); ?>

        <?= $form->field($model,'to_account_id')->widget(Select2::class,[
            'initValueText' => !empty($model->toAccount) ? $model->toAccount->getFullName() : '',
            'options' => ['placeholder' => Yii::t('app','Search for account..')],
            'language' => Yii::$app->language,
            'theme' => Select2::THEME_DEFAULT,
            'disabled' => !$model->isNewRecord,
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
                    'url' => Url::to(['/admin/accounts/ajax-search']),
                    'dataType' => 'json',
                    'data' => new JsExpression('function(params) { return {q:params.term}; }')
                ],
                'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                'templateResult' => new JsExpression('function(item) { return item.text; }'),
                'templateSelection' => new JsExpression('function (item) { return item.text; }'),
            ],
        ])->label(Yii::t('app','Destination account')); ?>

        <?= $form->field($model,'status_id')->dropDownList([
            Constants::PAYMENT_STATUS_NEW => Yii::t('app','New (in progress)'),
            Constants::PAYMENT_STATUS_DONE => Yii::t('app','Done'),
            Constants::PAYMENT_STATUS_CANCELED => Yii::t('app','Canceled'),
        ],['disabled' => $model->status_id == Constants::PAYMENT_STATUS_DONE]); ?>

        <hr>

        <?= $form->field($model,'description')->textarea(); ?>

        <?= $form->field($model,'note')->textarea(); ?>
    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-default pull-left" data-dismiss="modal"><?= Yii::t('app','Cancel'); ?></button>
        <button type="submit" class="btn btn-primary"><?= Yii::t('app','Save'); ?></button>
    </div>

<?php ActiveForm::end(); ?>