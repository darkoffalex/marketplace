<?php
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use app\models\User;
use branchonline\lightbox\LightboxAsset;
use app\helpers\Constants;
use app\models\Category;
use yii\helpers\ArrayHelper;

/* @var $model \app\models\Poster*/
/* @var $this \yii\web\View */
/* @var $user User */

$this->registerAssetBundle(LightboxAsset::class);

$user = Yii::$app->user->identity;

$this->title = Yii::t('app',$model->status_id == Constants::STATUS_TEMPORARY ? 'Create advertisement' : 'Edit advertisement');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app','Marketplaces'), 'url' => Url::to(['/user/posters/index'])];
$this->params['breadcrumbs'][] = $this->title;

$this->registerCssFile('@web/common/cropper/cropper.css');
$this->registerJsFile('@web/common/cropper/cropper.js', ['position' => \yii\web\View::POS_HEAD]);
$this->registerJsFile('@web/common/js/clipboard.js');

$categories = ArrayHelper::map(Category::getRecursiveCats(),'id',function($current,$defaultValue){
    /* @var $current Category */
    $result = "";
    for($i=0;$i<$current->getDepth();$i++){$result.= "-";}
    $result.= $current->name;
    return $result;
});

$tariffs = ArrayHelper::map($model->marketplace->marketplaceTariffPrices,'id',function($item){
    /* @var $item \app\models\MarketplaceTariffPrice */
    return $item->tariff->name.' ('.\app\helpers\Help::toPrice($item->price).')';
});

?>

<div class="row">
    <div class="col-md-12">

        <div class="box box-primary">
            <div class="box-header with-border"><h3 class="box-title"><?= Yii::t('app','Settings'); ?></h3></div>
            <?php $form = ActiveForm::begin([
                'id' => 'edit-poster-form',
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

                <?= $form->field($model,'title')->textInput(); ?>
                <?= $form->field($model,'description')->textarea(); ?>
                <?= $form->field($model,'phone')->textInput(); ?>
                <?= $form->field($model,'whats_app')->textInput(); ?>

                <hr>

                <?= $form->field($model,'category_id')->dropDownList($categories); ?>
                <?= $form->field($model,'marketplace_tariff_id')->dropDownList($tariffs); ?>
            </div>

            <div class="box-footer">
                <a class="btn btn-primary" href="<?= Url::to(['/group-admin/marketplaces/index']); ?>"><?= Yii::t('app','Back to list'); ?></a>
                <button type="submit" class="btn btn-primary"><?= Yii::t('app','Save'); ?></button>
            </div>
            <?php ActiveForm::end(); ?>
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