<?php
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use app\models\User;
use branchonline\lightbox\LightboxAsset;
use app\helpers\Constants;
use kartik\select2\Select2;
use yii\web\JsExpression;
use yii\helpers\ArrayHelper;
use app\models\Country;
use app\helpers\FileLoad;
use app\helpers\CropHelper;
use yii\helpers\Html;
use app\helpers\Help;
use yii\widgets\MaskedInput;

/* @var $model \app\models\Marketplace */
/* @var $this \yii\web\View */
/* @var $user User */
/* @var $tariffs \app\models\Tariff[] */

$this->registerAssetBundle(LightboxAsset::class);

$user = Yii::$app->user->identity;

$this->title = Yii::t('app',$model->isNewRecord ? 'Create marketplace' : 'Edit marketplace');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app','Marketplaces'), 'url' => Url::to(['/admin/marketplaces/index'])];
$this->params['breadcrumbs'][] = $this->title;

$countries = Country::find()
    ->where(['status_id' => Constants::STATUS_ENABLED])
    ->orderBy('priority ASC')
    ->all();

$this->registerCssFile('@web/common/cropper/cropper.css');
$this->registerJsFile('@web/common/cropper/cropper.js', ['position' => \yii\web\View::POS_HEAD]);
?>

<div class="row">
    <div class="col-md-12">

        <div class="box box-primary">
            <div class="box-header with-border"><h3 class="box-title"><?= Yii::t('app','Settings'); ?></h3></div>
            <?php $form = ActiveForm::begin([
                'id' => 'edit-marketplace-form',
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

                <hr>

                <?= $form->field($model,'name')->textInput()->label(Yii::t('app','Name (reference to group name)')); ?>

                <?= $form->field($model,'group_url')->widget(MaskedInput::class,[
                    'mask' => 'https://www.f\acebook.com/*{*}',
                ]); ?>

                <?= $form->field($model,'group_admin_profile')->widget(MaskedInput::class,[
                    'mask' => 'https://www.f\acebook.com/*{*}',
                ]); ?>

                <?= $form->field($model,'domain_alias')->textInput(); ?>

                <?= $form->field($model,'country_id')->dropDownList(ArrayHelper::map($countries,'id','name')); ?>

                <?= $form->field($model, 'status_id')->dropDownList([
                    Constants::USR_STATUS_ENABLED => Constants::GetStatusName(Constants::USR_STATUS_ENABLED),
                    Constants::USR_STATUS_DISABLED => Constants::GetStatusName(Constants::USR_STATUS_DISABLED),
                ]); ?>

                <?= $form->field($model,'timezone')->dropDownList(\app\helpers\Help::getTimeZoneArray()); ?>

                <?= $form->field($model,'geo')->dropDownList(ArrayHelper::map($countries,'id','name')); ?>

                <?= $form->field($model,'selling_rules')->textarea(); ?>

                <?= $form->field($model,'display_empty_categories')->checkbox(); ?>

                <?= $form->field($model,'pm_theme_description')->textarea(); ?>

                <?= $form->field($model,'admin_phone_wa')->textInput(); ?>

                <?= $form->field($model,'group_description')->textarea(); ?>

                <?php if(!empty($tariffs)): ?>
                    <hr>
                    <h4><?= Yii::t('app','Tariffs'); ?></h4>
                    <table class="table">
                        <tbody>
                        <tr>
                            <th><?= Yii::t('app','Name'); ?></th>
                            <th><?= Yii::t('app','Enabled'); ?></th>
                            <th><?= Yii::t('app','Price'); ?></th>
                            <th><?= Yii::t('app','Period time'); ?></th>
                            <th><?= Yii::t('app','Recurring'); ?></th>
                        </tr>
                        <?php foreach($tariffs as $tariff): ?>
                            <tr>
                                <td><?= Yii::t('app',$tariff->name); ?><?= Html::hiddenInput('Marketplace[tariffs]['.$tariff->id.'][id]',$tariff->id); ?></td>
                                <td><?= Html::checkbox('Marketplace[tariffs]['.$tariff->id.'][enabled]',$model->getTariffPrice($tariff->id) !== null); ?></td>
                                <td><?= Html::textInput('Marketplace[tariffs]['.$tariff->id.'][price]',Help::toPrice($model->getTariffPrice($tariff->id,true)->price),['class' => 'form-control']); ?></td>
                                <td>
                                    <?php $names = [
                                        Constants::PERIOD_DAYS => Yii::t('app','Day(s)'),
                                        Constants::PERIOD_WEEKS => Yii::t('app','Week(s)'),
                                        Constants::PERIOD_MONTHS => Yii::t('app','Month(s)'),
                                    ];?>
                                    <?= $tariff->period_amount.' '.(!empty($names[$tariff->period_unit_type]) ? $names[$tariff->period_unit_type] : null); ?>
                                </td>
                                <td>
                                    <?php $names = [
                                        1 => '<span class="label label-success">'.Yii::t('app','Yes').'</span>',
                                        0 => '<span class="label label-danger">'.Yii::t('app','No').'</span>',
                                    ]; echo !empty($names[$tariff->subscription]) ? $names[$tariff->subscription] : null; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>

                <hr>
                <a id="picture" name="picture"></a>
                <?= Html::hiddenInput('image_editing',1); ?>
                <?= $form->field($model,'header_image')->fileInput(); ?>
                <?= $form->field($model,'header_image_crop_settings')->hiddenInput(['class' => 'crop-data'])->label(false); ?>

                <?php if(FileLoad::hasFile($model,'header_image_filename')): ?>
                    <div class="row">
                        <div class="col-md-6">
                            <h4><?= Yii::t('app','Original'); ?>:</h4>
                            <div class="pic-container margin-bottom">
                                <img id="crop-image" style="width: 100%;" src="<?= FileLoad::getFileUrl($model,'header_image_filename'); ?>">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h4><?= Yii::t('app','Preview'); ?>:</h4>
                            <div>
                                <img src="<?= CropHelper::GetCroppedUrl($model,'header_image_filename','header_image_crop_settings'); ?>">
                            </div>
                        </div>
                    </div>
                    <a class="btn btn-primary btn-xs" href="<?= Url::to(['/admin/marketplaces/delete-image', 'id' => $model->id]); ?>"><?= Yii::t('app','Delete picture'); ?></a>
                <?php endif; ?>
            </div>

            <div class="box-footer">
                <a class="btn btn-primary" href="<?= Url::to(['/admin/marketplaces/index']); ?>"><?= Yii::t('app','Back to list'); ?></a>
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