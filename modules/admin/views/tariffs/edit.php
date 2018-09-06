<?php
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use app\models\User;
use app\helpers\Constants;
use branchonline\lightbox\LightboxAsset;
use app\helpers\FileLoad;
use app\helpers\CropHelper;

/* @var $model \app\models\Tariff */
/* @var $this \yii\web\View */
/* @var $user User */

$this->registerAssetBundle(LightboxAsset::class);

$user = Yii::$app->user->identity;

$this->title = $model->isNewRecord ? Yii::t('app','Create new tariff') : Yii::t('app','Edit tariff');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app','Tariffs'), 'url' => Url::to(['/admin/tariffs/index'])];
$this->params['breadcrumbs'][] = $this->title;

$this->registerCssFile('@web/common/cropper/cropper.css');
$this->registerJsFile('@web/common/cropper/cropper.js', ['position' => \yii\web\View::POS_HEAD]);
?>

<div class="row">
    <div class="col-md-12">

        <div class="box box-primary">
            <div class="box-header with-border"><h3 class="box-title"><?= Yii::t('app','Settings'); ?></h3></div>

            <?php $form = ActiveForm::begin([
                'id' => 'edit-tariff-form',
                'options' => ['role' => 'form', 'method' => 'post', 'enctype' => 'multipart/form-data'],
                'enableClientValidation'=>false,
                'fieldConfig' => [
                    'template' => "{label}\n{input}\n{error}\n",
                    //'labelOptions' => ['class' => 'col-lg-1 control-label'],
                ],
            ]); ?>


            <div class="box-body">
                <?php if(!$model->hasErrors() && Yii::$app->request->isPost): ?>
                    <div class="alert alert-success alert-dismissable">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                        <h4><i class="icon fa fa-check"></i><?= Yii::t('app','Saved'); ?></h4>
                        <?= Yii::t('app','Changes saved'); ?>
                    </div>
                <?php endif; ?>
                <?= $form->field($model,'name')->textInput(); ?>
                <?= $form->field($model,'description')->textarea(); ?>

                <?= $form->field($model,'period_unit_type')->dropDownList([
                    Constants::PERIOD_DAYS => Yii::t('app','Days'),
                    Constants::PERIOD_WEEKS => Yii::t('app','Weeks'),
                    Constants::PERIOD_MONTHS => Yii::t('app','Months'),
                ]); ?>

                <?= $form->field($model, 'period_amount')->textInput(); ?>

                <?php $model->base_price = \app\helpers\Help::toPrice($model->base_price); ?>
                <?php $model->discounted_price = \app\helpers\Help::toPrice($model->discounted_price); ?>
                <?= $form->field($model,'base_price')->textInput(); ?>
                <?= $form->field($model,'discounted_price')->textInput(); ?>
                <?= $form->field($model,'show_on_page')->checkbox(); ?>
                <?= $form->field($model,'subscription')->checkbox(); ?>

                <?= $form->field($model,'special_type')->dropDownList([
                    Constants::TARIFF_SUB_TYPE_REGULAR => Yii::t('app','Regular'),
                    Constants::TARIFF_SUB_TYPE_ADMIN_POST => Yii::t('app','Admin\'s post'),
                ]); ?>

                <hr>

                <?= $form->field($model,'image')->fileInput(); ?>
                <?= $form->field($model,'image_crop_settings')->hiddenInput(['class' => 'crop-data'])->label(false); ?>

                <?php if(FileLoad::hasFile($model,'image_filename')): ?>
                    <div class="row">
                        <div class="col-md-6">
                            <h4><?= Yii::t('app','Original'); ?>:</h4>
                            <div class="pic-container margin-bottom">
                                <img id="crop-image" style="width: 100%;" src="<?= FileLoad::getFileUrl($model,'image_filename'); ?>">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h4><?= Yii::t('app','Preview'); ?>:</h4>
                            <div>
                                <img src="<?= CropHelper::GetCroppedUrl($model,'image_filename','image_crop_settings'); ?>">
                            </div>
                        </div>
                    </div>
                    <a class="btn btn-primary btn-xs" href="<?= Url::to(['/admin/tariffs/delete-image', 'id' => $model->id]); ?>"><?= Yii::t('app','Delete picture'); ?></a>
                <?php endif; ?>
            </div>

            <div class="box-footer">
                <a class="btn btn-primary" href="<?= Url::to(['/admin/tariffs/index']); ?>"><?= Yii::t('app','Back to list'); ?></a>
                <button type="submit" class="btn btn-primary"><?= Yii::t('app','Save'); ?></button>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function(){
        /**
         * Обрезка изображений
         */
        var image = document.getElementById('crop-image');
        var ratio = 468 / 234;
        var cropDataField = $('.crop-data');
        var cropData = undefined;

        try {
            cropData = cropDataField.length > 0 ? JSON.parse(cropDataField.val()) : undefined;
        }catch (err) {
            cropData = undefined;
        }

        if(typeof Cropper !== 'undefined' && image){
            var cropper = new Cropper(image, {
                aspectRatio: ratio,
                viewMode: 2,
                scalable: false,
                rotatable: false,
                zoomable: false,
                zoomOnWheel: false,
                crop: function(e) {
                    var cropData = {};
                    cropData.x = Math.round(e.detail.x);
                    cropData.y = Math.round(e.detail.y);
                    cropData.w = Math.round(e.detail.width);
                    cropData.h = Math.round(e.detail.height);
                    $('.crop-data').val(JSON.stringify(cropData));
                },
                ready: function (e) {
                    if(cropData){
                        cropper.setData({
                            x: cropData.x,
                            y: cropData.y,
                            width: cropData.w,
                            height: cropData.h
                        });
                    }
                }
            });
        }
    });
</script>