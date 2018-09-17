<?php
use yii\bootstrap\ActiveForm;
use app\helpers\Constants;
use yii\helpers\Url;
use app\helpers\Help;
use dosamigos\fileupload\FileUploadUI;

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

        <hr>

        <?= $form->field($model,'status_id')->dropDownList([
            Constants::PAYMENT_STATUS_NEW => Yii::t('app','New'),
            Constants::PAYMENT_STATUS_DONE => Yii::t('app','Approved'),
            Constants::PAYMENT_STATUS_CANCELED => Yii::t('app','Rejected'),
        ],['data-activate' => "#discard-reason:".Constants::PAYMENT_STATUS_CANCELED, 'disabled' => $model->status_id == Constants::PAYMENT_STATUS_CANCELED]); ?>

        <div id="discard-reason" class="hidden">
            <?= $form->field($model,'discard_reason')->textarea(); ?>
        </div>

        <hr>
        <h4><?= Yii::t('app','Proofs & details files') ?></h4>
        <?php echo FileUploadUI::widget([
            'name' => 'filename',
            'url' => ['/admin/payout-proposals/upload-image', 'id' => $model->id],
            'gallery' => true,
            'fieldOptions' => [
                'accept' => 'image/*'
            ],
            'clientOptions' => [
                'maxFileSize' => 5000000,
                'previewThumbnail' => true,
            ],
            'clientEvents' => [
                'fileuploaddone' => 'function(e, data) {
                                console.log(e);
                                console.log(data);
                            }',
                'fileuploadfail' => 'function(e, data) {
                                console.log(e);
                                console.log(data);
                            }',
            ],
        ]);
        $this->registerJs('$("#w0-fileupload").fileupload("option", "done").call($("#w0-fileupload"), $.Event("done"), {result: {files: '.$model->getImagesListed(null,true).'}})',\yii\web\View::POS_READY); ?>
    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-default pull-left" data-dismiss="modal"><?= Yii::t('app','Cancel'); ?></button>
        <button type="submit" class="btn btn-primary"><?= $model->isNewRecord ? Yii::t('app','Create') : Yii::t('app','Save'); ?></button>
    </div>

<?php ActiveForm::end(); ?>