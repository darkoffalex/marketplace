<?php
use yii\bootstrap\ActiveForm;
use app\helpers\Constants;
use yii\helpers\Url;

/* @var $model \app\models\Cv*/
/* @var $this \yii\web\View */
/* @var $controller \app\modules\admin\controllers\CvsController */
/* @var $flags array[] */

$this->registerJsFile(Url::to('@web/common/js/show-hide.js'));
$controller = $this->context;
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
        <p><strong><?= Yii::t('app','Group\'s name'); ?></strong> : <span><?= $model->group_name; ?></span></p>
        <p><strong><?= Yii::t('app','Group\'s URL'); ?></strong> : <span><?= $model->group_url; ?></span></p>
        <p><strong><?= Yii::t('app','Group admin\'s profile URL'); ?></strong> : <span><?= $model->group_admin_profile; ?></span></p>
        <p><strong><?= Yii::t('app','Group\'s thematics'); ?></strong> : <span><?= $model->group_thematics; ?></span></p>
        <p><strong><?= Yii::t('app','Group\'s popularity'); ?></strong> : <span><?= $model->group_popularity; ?></span></p>
        <p><strong><?= Yii::t('app','Group\'s description'); ?></strong> : <span><?= $model->group_description; ?></span></p>
        <p><strong><?= Yii::t('app','Group\'s geo-orientation'); ?></strong> : <span><?= $model->group_geo; ?></span></p>

        <hr>

        <p><strong><?= Yii::t('app','Name and surname'); ?></strong> : <span><?= $model->name; ?></span></p>
        <p><strong><?= Yii::t('app','Member of "Admin.School"'); ?></strong> : <span><?= Yii::t('app',$model->is_member ? 'Yes' : 'No'); ?></span></p>
        <p><strong><?= Yii::t('app','Country'); ?></strong> : <span><?= $model->country->name; ?></span></p>
        <p><strong><?= Yii::t('app','Email'); ?></strong> : <span><?= $model->email; ?></span></p>
        <p><strong><?= Yii::t('app','Phone'); ?></strong> : <span><?= $model->phone; ?></span></p>
        <p><strong><?= Yii::t('app','Has Viber'); ?></strong> : <span><?= Yii::t('app',$model->has_viber ? 'Yes' : 'No'); ?></span></p>
        <p><strong><?= Yii::t('app','Has WhatsApp'); ?></strong> : <span><?= Yii::t('app',$model->has_whatsapp ? 'Yes' : 'No'); ?></span></p>
        <p><strong><?= Yii::t('app','Timezone'); ?></strong> : <span><?= \app\helpers\Help::floatToTime($model->timezone/3600); ?></span></p>
        <p><strong><?= Yii::t('app','Comfortable call time'); ?></strong> : <span><?= $model->comfortable_call_time; ?></span></p>

        <hr>

        <?= $form->field($model,'status_id')->dropDownList([
            Constants::CV_STATUS_NEW => Yii::t('app','New'),
            Constants::CV_STATUS_APPROVED => Yii::t('app','Approved'),
            Constants::CV_STATUS_REJECTED => Yii::t('app','Rejected'),
        ],['data-activate' => "#discard-reason:".Constants::CV_STATUS_REJECTED]); ?>

        <div id="discard-reason" class="hidden">
            <?= $form->field($model,'discard_reason')->textarea(); ?>
        </div>
    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-default pull-left" data-dismiss="modal"><?= Yii::t('app','Cancel'); ?></button>
        <button type="submit" class="btn btn-primary"><?= $model->isNewRecord ? Yii::t('app','Create') : Yii::t('app','Save'); ?></button>
    </div>

<?php ActiveForm::end(); ?>