<?php
use yii\bootstrap\ActiveForm;
use app\helpers\Constants;
use app\helpers\FileLoad;
use app\helpers\CropHelper;
use yii\helpers\Url;

/* @var $model \app\models\ShortLink*/
/* @var $this \yii\web\View */
/* @var $controller \app\modules\user\controllers\ShortLinksController */
/* @var $user \app\models\User */

$controller = $this->context;
$user = Yii::$app->user->identity;
?>

    <div class="modal-header">
        <h4 class="modal-title"><?= $model->isNewRecord ? Yii::t('app','Create short link') : Yii::t('app','Edit short link'); ?></h4>
    </div>

<?php $form = ActiveForm::begin([
    'id' => 'create-edit-form',
    'options' => ['role' => 'form', 'method' => 'post', 'enctype' => 'multipart/form-data'],
    'enableClientValidation'=>true,
    'enableAjaxValidation' => true,
    'fieldConfig' => [
        'template' => "{label}\n{input}\n{error}\n",
    ],
]); ?>

    <div class="modal-body">
        <?= $form->field($model,'type_id')->dropDownList([
            Constants::SHORT_LINK_REGULAR => Yii::t('app','Regular'),
            Constants::SHORT_LINK_WHATSAPP => Yii::t('app','WhatsApp'),
        ],['data-activate' => '#link-block:'.Constants::SHORT_LINK_REGULAR.',#phone-block:'.Constants::SHORT_LINK_WHATSAPP]); ?>

        <div id="link-block" class="<?= $model->type_id == Constants::SHORT_LINK_WHATSAPP ? 'hidden' : ''; ?>">
            <?= $form->field($model,'original_link')->textInput(); ?>
        </div>
        <div id="phone-block" class="phone-block <?= $model->type_id == Constants::SHORT_LINK_REGULAR ? 'hidden' : ''; ?>">
            <?= $form->field($model,'phone')->textInput(); ?>
        </div>

        <?= $form->field($model,'custom_key')->checkbox(['id' => 'cb-key']); ?>

        <div id="key-block" class="<?= $model->custom_key ? '' : 'hidden'; ?>">
            <?= $form->field($model, 'key')->textInput(); ?>
        </div>

        <hr>
        <button type="button" class="btn btn-primary btn-xs" data-toggle="collapse" data-target="#advanced"><?= Yii::t('app','Advanced options'); ?> <i class="fa fa-plus"></i></button>

        <div id="advanced" style="margin-top: 10px;" class="collapse">

            <?php if($user->isApprovedMember()): ?>
                <div class="phone-block <?= $model->type_id == Constants::SHORT_LINK_REGULAR ? 'hidden' : ''; ?>">
                    <?= $form->field($model,'message')->textarea(); ?>
                </div>
                <?= $form->field($model,'title'); ?>
                <?= $form->field($model,'description'); ?>
                <?= $form->field($model,'image')->fileInput(); ?>

                <?php if(FileLoad::hasFile($model,'image_file')): ?>
                    <div id="image-container" class="row">
                        <div class="col-md-6">
                            <h4><?= Yii::t('app','Cropped image'); ?></h4>
                            <img style="max-width: 200px; width: 100%" src="<?= CropHelper::GetCroppedUrl($model,'image_file',null,[256,256],false,true); ?>">
                            <a id="del-pic" data-confirm="<?= Yii::t('app','Are you sure ?'); ?>" class="btn btn-primary btn-xs" href="<?= Url::to(['delete-image','id' => $model->id]); ?>"><?= Yii::t('app','Delete-image'); ?></a>
                        </div>
                    </div>
                <?php endif; ?>
            <?php else:?>
                <div class="alert alert-warning alert-dismissible">
                    <h4><i class="icon fa fa-warning"></i><?= Yii::t('app','Warning!'); ?></h4>
                    <?= Yii::t('app','Additional options are not available because you have not added any marketplaces'); ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-default pull-left" data-dismiss="modal"><?= Yii::t('app','Cancel'); ?></button>
        <button type="submit" class="btn btn-primary"><?= Yii::t('app','Save'); ?></button>
    </div>

<?php ActiveForm::end(); ?>

<script type="text/javascript">
    $("#del-pic").click(function () {
        var link = $(this);
        $.ajax({
            url: link.attr('href'),
            type: 'GET',
            async: false,
            success: function(data){
                if(data === 'OK'){
                    $('#image-container').remove();
                }
            }
        });
        return false;
    });

    $("#cb-key").change(function() {
        if($(this).prop('checked')){
            $("#key-block").removeClass('hidden');
        }else{
            $("#key-block").addClass('hidden');
        }
    });
</script>
