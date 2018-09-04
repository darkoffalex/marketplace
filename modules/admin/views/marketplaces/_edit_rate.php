<?php
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use app\helpers\Help;
use app\helpers\Constants;

/* @var $model \app\models\Rate*/
/* @var $this \yii\web\View */
/* @var $controller \app\modules\admin\controllers\MarketplacesController */
/* @var $flags array[] */
$controller = $this->context;
?>

    <div class="modal-header">
        <h4 class="modal-title"><?= Yii::t('app', $model->isNewRecord ? 'Create rate' : 'Edit rate'); ?></h4>
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
        <?= $form->field($model,'name')->textInput(); ?>

        <?php $model->price = Help::toPrice($model->price); ?>
        <?= $form->field($model,'price')->textInput()->label(Yii::t('app','Price')); ?>

        <?= $form->field($model,'single_payment')->checkbox(); ?>

        <?= $form->field($model,'days_count')->textInput(); ?>

        <div class="days">
            <?= $form->field($model,'first_free_days')->textInput(); ?>
        </div>

        <?= $form->field($model,'admin_post_mode')->checkbox(); ?>

        <?= $form->field($model, 'status_id')->dropDownList([
            Constants::USR_STATUS_ENABLED => Constants::GetStatusName(Constants::USR_STATUS_ENABLED),
            Constants::USR_STATUS_DISABLED => Constants::GetStatusName(Constants::USR_STATUS_DISABLED),
        ]); ?>
    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-default pull-left" data-dismiss="modal"><?= Yii::t('app','Cancel'); ?></button>
        <button type="submit" class="btn btn-primary"><?= Yii::t('app','Save'); ?></button>
    </div>

<?php ActiveForm::end(); ?>

<script type="text/javascript">
    var showHide = function () {
        var checked = $('#rate-single_payment').prop('checked');
        if(checked){
            $('.days').addClass('hidden');
        }else{
            $('.days').removeClass('hidden');
        }
    };

    showHide();
    $('#rate-single_payment').change(function () {
        showHide();
    });
</script>
