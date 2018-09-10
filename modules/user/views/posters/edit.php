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
$this->params['breadcrumbs'][] = ['label' => Yii::t('app','Advertisements'), 'url' => Url::to(['/user/posters/index'])];
$this->params['breadcrumbs'][] = $this->title;


$categories = ArrayHelper::map(Category::getRecursiveCats(),'id',function($current,$defaultValue){
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
                <?= $form->field($model,'description')->textarea(); ?>
                <?= $form->field($model,'phone')->textInput(); ?>
                <?= $form->field($model,'whats_app')->textInput(); ?>

                <hr>
                <?= $form->field($model,'status_id')->dropDownList([
                    Constants::STATUS_ENABLED => Yii::t('app','Enabled'),
                    Constants::STATUS_DISABLED => Yii::t('app','Disabled'),
                ]); ?>
                <?= $form->field($model,'category_id')->dropDownList($categories); ?>

                <?php if($model->status_id == Constants::STATUS_TEMPORARY): ?>
                    <?= $form->field($model,'marketplace_tariff_id')->dropDownList($tariffs); ?>
                <?php endif; ?>

                <hr>
                <h4><?= Yii::t('app','Images'); ?>:</h4>
                <?php echo FileUploadUI::widget([
                    'name' => 'filename',
                    'url' => ['/user/posters/upload-image', 'id' => $model->id],
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
                $this->registerJs('$("#w0-fileupload").fileupload("option", "done").call($("#w0-fileupload"), $.Event("done"), {result: {files: '.$model->getImagesListed(null,true).'}})',\yii\web\View::POS_READY);
                ?>
                <?php if($model->status_id == Constants::STATUS_TEMPORARY): ?>
                    <hr>
                    <?php $privacyTermsLink = Html::a(Yii::t('app','privacy terms'),'#',['target' => '_blank']); ?>
                    <?php $userAgreementLink = Html::a(Yii::t('app','user agreement'),'#',['target' => '_blank']); ?>
                    <?php $publishingTerms = Html::a(Yii::t('app','publishing terms'),['/user/marketplaces/terms', 'id' => $model->marketplace_id],['data-target' => '.modal-main','data-toggle' => 'modal']); ?>
                    <?php $text = Yii::t('app','I agree with {privacy}, {user} and {publishing}', ['privacy' => $privacyTermsLink, 'user' => $userAgreementLink, 'publishing' => $publishingTerms]); ?>
                    <?= $form->field($model,'agreement')->checkbox()->label($text); ?>
                <?php endif; ?>
            </div>

            <div class="box-footer">
                <a class="btn btn-primary" href="<?= Url::to(['/user/posters/index']); ?>"><?= Yii::t('app','Back to list'); ?></a>
                <?php if($model->status_id != Constants::STATUS_TEMPORARY && !$model->isPaid()): ?>
                    <a href="<?= Url::to(['/user/posters/payment', 'id' => $model->id]); ?>" class="btn btn-primary"><?= Yii::t('app','Pay'); ?></a>
                <?php endif; ?>
                <button type="submit" class="btn btn-primary"><?= Yii::t('app',$model->status_id == Constants::STATUS_TEMPORARY ? 'Create & pay' : 'Update'); ?></button>
                <a data-target=".modal-main" data-original-url="<?= Url::to(['/user/posters/preview', 'id' => $model->id]) ?>" data-add-form-params="#edit-poster-form" data-toggle="modal" class="btn btn-primary" href="<?= Url::to(['/user/posters/preview', 'id' => $model->id]) ?>"><?= Yii::t('app','Preview'); ?></a>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>