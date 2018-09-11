<?php
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use app\models\User;
use app\helpers\Constants;
use app\models\Category;
use yii\helpers\ArrayHelper;
use dosamigos\fileupload\FileUploadUI;
use yii\helpers\Html;

/* @var $model \app\models\Poster*/
/* @var $this \yii\web\View */
/* @var $user User */

$user = Yii::$app->user->identity;

$this->title = Yii::t('app',$model->status_id == Constants::STATUS_TEMPORARY ? 'Create advertisement' : 'Edit advertisement');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app','Advertisements'), 'url' => Url::to(['/admin/posters/index'])];
$this->params['breadcrumbs'][] = $this->title;


$categories = ArrayHelper::map(Category::getRecursiveCats(),'id',function($current){
    /* @var $current Category */
    $result = "";
    for($i=0;$i<$current->getDepth();$i++){$result.= "-";}
    $result.= $current->name;
    return $result;
});

$tariffs = ArrayHelper::map($model->marketplace->marketplaceTariffPrices,'id',function($item){
    /* @var $item \app\models\MarketplaceTariffPrice */
    return $item->getNameWithDetails();
});

$this->registerJsFile(Url::to('@web/common/js/show-hide.js'));
?>

<div class="row">
    <div class="col-md-12">

        <div class="box box-primary">
            <div class="box-header with-border"><h3 class="box-title"><?= Yii::t('app','Information'); ?></h3></div>
            <div class="box-body">
                <p><strong><?= Yii::t('app','Marketplace'); ?>:</strong> <?= $model->marketplace->name; ?></p>
                <p><strong><?= Yii::t('app','Country'); ?>:</strong> <?= Yii::t('app',$model->marketplace->country->name); ?></p>
                <p><strong><?= Yii::t('app','Payment'); ?>:</strong> <?= $model->getPaymentInformation(); ?></p>
                <p><strong><?= Yii::t('app','Tariff'); ?>:</strong> <?= !empty($model->marketplaceTariff) ? $model->marketplaceTariff->getNameWithDetails() : Yii::t('app','Not selected yet'); ?></p>
                <p><strong><?= Yii::t('app','Owner name'); ?>:</strong> <?= $model->user->name; ?></p>
            </div>
        </div>

        <div class="box box-primary">
            <div class="box-header with-border"><h3 class="box-title"><?= Yii::t('app','Settings'); ?></h3></div>
            <?php $form = ActiveForm::begin([
                'id' => 'edit-poster-form',
                'options' => ['role' => 'form', 'method' => 'post', 'enctype' => 'multipart/form-data'],
                'enableClientValidation'=>false,
                'enableAjaxValidation' => true,
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
                <?php if(!empty($model->title_approved)): ?>
                    <small><?= Yii::t('app','Approved') ?>: <?= $model->title_approved; ?></small>
                    <br>
                <?php endif; ?>

                <?= $form->field($model,'description')->textarea(); ?>
                <?php if(!empty($model->description_approved)): ?>
                    <small><?= Yii::t('app','Approved') ?>: <?= $model->description_approved; ?></small>
                    <br>
                <?php endif; ?>

                <?= $form->field($model,'phone')->textInput(); ?>
                <?php if(!empty($model->phone_approved)): ?>
                    <small><?= Yii::t('app','Approved') ?>: <?= $model->phone_approved; ?></small>
                    <br>
                <?php endif; ?>

                <?= $form->field($model,'whats_app')->textInput(); ?>
                <?php if(!empty($model->whats_app_approved)): ?>
                    <small><?= Yii::t('app','Approved') ?>: <?= $model->whats_app_approved; ?></small>
                    <br>
                <?php endif; ?>

                <hr>
                <?= $form->field($model,'status_id')->dropDownList([
                    Constants::STATUS_ENABLED => Yii::t('app','Enabled'),
                    Constants::STATUS_DISABLED => Yii::t('app','Disabled'),
                ]); ?>
                <?= $form->field($model,'category_id')->dropDownList($categories); ?>

                <?= $form->field($model,'marketplace_tariff_id')->dropDownList($tariffs,['disabled' => $model->isPaid()]); ?>

                <?= $form->field($model,'approved_by_sa')->dropDownList([
                    1 => Yii::t('app','Yes'),
                    0 => Yii::t('app','No'),
                ],['data-activate' => "#reason-block:0"])->label(Yii::t('app','Approved (can be published)')); ?>

                <div id="reason-block" class="hidden">
                    <?= $form->field($model,'refuse_reason')->textarea(); ?>
                </div>

                <hr>
                <h4><?= Yii::t('app','Images'); ?>:</h4>
                <?php echo FileUploadUI::widget([
                    'name' => 'filename',
                    'url' => ['/admin/posters/upload-image', 'id' => $model->id],
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
                $this->registerJs('$("#w0-fileupload").fileupload("option", "done").call($("#w0-fileupload"), $.Event("done"), {result: {files: '.$model->getImagesListed(null,true,'/admin/posters/delete-image').'}})',\yii\web\View::POS_READY);
                ?>
            </div>

            <div class="box-footer">
                <a class="btn btn-primary" href="<?= Url::to(['/group-admin/posters/index']); ?>"><?= Yii::t('app','Back to list'); ?></a>
                <button type="submit" class="btn btn-primary"><?= Yii::t('app','Update'); ?></button>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>