<?php
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use app\models\User;
use branchonline\lightbox\LightboxAsset;
use app\helpers\Help;
use app\helpers\Constants;
use app\models\Country;
use app\helpers\FileLoad;
use app\helpers\CropHelper;
use yii\helpers\Html;
use yii\widgets\MaskedInput;
use kartik\editable\Editable;

/* @var $model \app\models\Marketplace */
/* @var $this \yii\web\View */
/* @var $user User */
/* @var $tariffs \app\models\Tariff[] */

$this->registerAssetBundle(LightboxAsset::class);

$user = Yii::$app->user->identity;

$this->title = Yii::t('app','Edit marketplace');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app','Marketplaces'), 'url' => Url::to(['/group-admin/marketplaces/index'])];
$this->params['breadcrumbs'][] = $this->title;

$countries = Country::find()
    ->where(['status_id' => Constants::STATUS_ENABLED])
    ->orderBy('priority ASC')
    ->all();

$this->registerCssFile('@web/common/cropper/cropper.css');
$this->registerJsFile('@web/common/cropper/cropper.js', ['position' => \yii\web\View::POS_HEAD]);
$this->registerJsFile('@web/common/js/clipboard.js');
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

                <?= $form->field($model,'name')->textInput()->label(Yii::t('app','Name (reference to group name)')); ?>

                <?= $form->field($model,'group_url')->widget(MaskedInput::class,[
                    'mask' => 'https://www.f\acebook.com/*{*}',
                ]); ?>

                <?= $form->field($model,'group_admin_profile')->widget(MaskedInput::class,[
                    'mask' => 'https://www.f\acebook.com/*{*}',
                ]); ?>

                <?= $form->field($model,'timezone')->dropDownList(\app\helpers\Help::getTimeZoneArray()); ?>

                <?= $form->field($model,'selling_rules')->textarea(); ?>

                <?= $form->field($model,'display_empty_categories')->checkbox(); ?>

                <?= $form->field($model,'pm_theme_description')->textarea(); ?>

                <?= $form->field($model,'admin_phone_wa')->textInput(); ?>

                <?= $form->field($model,'group_description')->textarea(); ?>
            </div>

            <div class="box-footer">
                <a class="btn btn-primary" href="<?= Url::to(['/group-admin/marketplaces/index']); ?>"><?= Yii::t('app','Back to list'); ?></a>
                <button type="submit" class="btn btn-primary"><?= Yii::t('app','Save'); ?></button>
            </div>
            <?php ActiveForm::end(); ?>
        </div>


        <?php if(!empty($tariffs)): ?>
            <a id="rates" name="rates"></a>
            <div class="box box-primary">
                <div class="box-header with-border"><h3 class="box-title"><?= Yii::t('app','Available tariffs'); ?></h3></div>
                <div class="box-body no-padding table-responsive">
                    <table class="table">
                        <tbody>
                        <tr>
                            <th><?= Yii::t('app','Name'); ?></th>
                            <th><?= Yii::t('app','Price'); ?></th>
                            <th><?= Yii::t('app','Period time'); ?></th>
                            <th><?= Yii::t('app','Recurring'); ?></th>
                        </tr>
                        <?php foreach($tariffs as $tariff): ?>
                            <?php if($model->getTariffPrice($tariff->id) !== null): ?>
                                <tr>
                                    <td><?= Yii::t('app',$tariff->name); ?></td>
                                    <td><?= Help::toPrice($model->getTariffPrice($tariff->id,true)->price); ?></td>
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
                            <?php endif; ?>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>



        <a id="rates" name="picture"></a>
        <div class="box box-primary">
            <div class="box-header with-border"><h3 class="box-title"><?= Yii::t('app','Picture'); ?></h3></div>
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
                                <img style="max-width: 100%;" src="<?= CropHelper::GetCroppedUrl($model,'header_image_filename','header_image_crop_settings',Yii::$app->params['visual']['marketplaceHeaderSizes']); ?>">
                            </div>
                        </div>
                    </div>
                    <a class="btn btn-primary btn-xs" href="<?= Url::to(['/group-admin/marketplaces/delete-image', 'id' => $model->id]); ?>"><?= Yii::t('app','Delete picture'); ?></a>
                <?php endif; ?>
            </div>
            <div class="box-footer">
                <button type="submit" class="btn btn-primary"><?= Yii::t('app','Upload / Save changes'); ?></button>
            </div>
            <?php ActiveForm::end(); ?>
        </div>


        <a id="keys" name="keys"></a>
        <div class="box box-primary">
            <div class="box-header with-border"><h3 class="box-title"><?= Yii::t('app','Keys'); ?></h3></div>
            <div class="box-body no-padding">
                <table class="table">
                    <tbody>
                    <tr>
                        <th style="width: 10px">#</th>
                        <th><?= Yii::t('app','Key'); ?></th>
                        <th><?= Yii::t('app','Note'); ?></th>
                        <th><?= Yii::t('app','Used'); ?></th>
                        <th><?= Yii::t('app','User'); ?></th>
                        <th><?= Yii::t('app','Actions'); ?></th>
                    </tr>

                    <?php if(!empty($model->marketplaceKeys)): ?>
                        <?php foreach ($model->marketplaceKeys as $key): ?>
                            <tr>
                                <td><?= $key->id; ?></td>
                                <td><?= '<div class="input-group input-group-sm" style="width: 100%; max-width: 200px;"><input id="copy-link-'.$key->id.'" class="form-control" readonly type="text" value="'.$key->code.'"><span class="input-group-btn"><button title="'.Yii::t('app','Copy').'" type="button" data-clipboard-target="#copy-link-'.$key->id.'" class="btn btn-info btn-flat copy-text"><i class="fa fa-fw fa-clipboard"></i></button></span></div>'; ?></td>
                                <td>
                                    <?= Editable::widget([
                                        'formOptions' => ['action' => Url::to(['/group-admin/marketplaces/update-key-note','id' => $key->id])],
                                        'name'=>'MarketplaceKey[note]',
                                        'asPopover' => true,
                                        'inputType' => Editable::INPUT_TEXTAREA,
                                        'value' => $key->note,
                                        'displayValue' => !empty($key->note) ? $key->note : Yii::t('app','(Not set)'),
                                        'header' => Yii::t('app','note'),
                                        'size'=>'md',
                                        'options' => ['class'=>'form-control', 'placeholder'=>Yii::t('app','Type your note here')]
                                    ]); ?>
                                </td>
                                <td><?= !empty($key->usedBy) ? '<span class="label label-success">'.Yii::t('app','Yes').'</span>'  : '<span class="label label-warning">'.Yii::t('app','No').'</span>'; ?></td>
                                <td><?= !empty($key->usedBy) ? Html::a($key->usedBy->name,'#') : '<span class="label label-warning">'.Yii::t('app','No').'</span>'; ?></td>
                                <td>
                                    <a data-confirm="<?= Yii::t('app','Are you sure?'); ?>" class="btn btn-primary btn-xs" href="<?= Url::to(['/group-admin/marketplaces/delete-key', 'id' => $key->id]); ?>"><?= Yii::t('app','Delete'); ?></a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="10"><?= Yii::t('app','Keys not found'); ?></td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <div class="box-footer">
                <a href="<?= Url::to(['/group-admin/marketplaces/create-key', 'id' => $model->id]); ?>" class="btn btn-primary"><?= Yii::t('app','Add key'); ?></a>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function(){
        new Clipboard('.copy-text');
    });
</script>

<script type="text/javascript">
    $(document).ready(function(){
        /**
         * Обрезка изображений
         */
        var image = document.getElementById('crop-image');
        var ratio = (<?= Yii::$app->params['visual']['marketplaceHeaderSizes'][0]; ?>) / (<?= Yii::$app->params['visual']['marketplaceHeaderSizes'][1]; ?>);
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