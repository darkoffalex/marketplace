<?php
use yii\bootstrap\ActiveForm;
use app\helpers\Constants;
use yii\helpers\Url;
use app\helpers\Help;
use branchonline\lightbox\Lightbox;

/* @var $model \app\models\PayoutProposal*/
/* @var $this \yii\web\View */
/* @var $controller \app\modules\admin\controllers\PayoutProposalsController */
/* @var $flags array[] */

$this->registerJsFile(Url::to('@web/common/js/show-hide.js'));
$controller = $this->context;
?>

    <div class="modal-header">
        <h4 class="modal-title"><?= Yii::t('app','Review payout proposal'); ?></h4>
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
        <p><strong><?= Yii::t('app','Description'); ?></strong> : <span><?= $model->description; ?></span></p>
        <p><strong><?= Yii::t('app','Sum'); ?></strong> : <span><?= Help::toPrice($model->amount); ?></span></p>
        <p><strong><?= Yii::t('app','Status'); ?></strong> :
            <?php if($model->status_id == Constants::PAYMENT_STATUS_NEW): ?>
                <span class="label label-warning"><?= Yii::t('app','New'); ?></span>
            <?php elseif($model->status_id == Constants::PAYMENT_STATUS_DONE): ?>
                <span class="label label-success"><?= Yii::t('app','Done'); ?></span>
            <?php else: ?>
                <span class="label label-danger"><?= Yii::t('app','Refused'); ?></span>
            <?php endif; ?>
        </p>

        <?php if(!empty($model->discard_reason)): ?>
            <p><strong><?= Yii::t('app','Refuse reason'); ?></strong> : <span><?= $model->discard_reason; ?></span></p>
        <?php endif; ?>

        <?php if(!empty($model->payoutProposalImages)): ?>
            <p><strong><?= Yii::t('app','Proof images'); ?></strong> :</p>
            <?= Lightbox::widget([
                'files' => $model->getImageAttachmentUrlsForLightBox(150)
            ]); ?>
        <?php endif; ?>
    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-default pull-left" data-dismiss="modal"><?= Yii::t('app','Cancel'); ?></button>
        <button type="submit" class="btn btn-primary"><?= $model->isNewRecord ? Yii::t('app','Create') : Yii::t('app','Save'); ?></button>
    </div>

<?php ActiveForm::end(); ?>