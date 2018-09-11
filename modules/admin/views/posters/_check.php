<?php
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;

/* @var $model \app\models\Poster*/
/* @var $this \yii\web\View */
/* @var $controller \app\modules\admin\controllers\PostersController */

$this->registerJsFile(Url::to('@web/common/js/show-hide.js'));
$controller = $this->context;
?>

    <div class="modal-header">
        <h4 class="modal-title"><?= Yii::t('app','Check the advertisement'); ?></h4>
    </div>

<?php $form = ActiveForm::begin([
    'id' => 'check-poster-status',
    'options' => ['role' => 'form', 'method' => 'post', 'enctype' => 'multipart/form-data'],
    'enableClientValidation'=>false,
    'enableAjaxValidation' => true,
    'fieldConfig' => [
        'template' => "{label}\n{input}\n{error}\n",
        //'labelOptions' => ['class' => 'col-lg-1 control-label'],
    ],
]); ?>
    <div class="modal-body">

        <h4><?= Yii::t('app','Preview'); ?></h4>
        <div class="row classify-wrapper">
            <div class="col-md-12">
                <div class="panel panel-default" style="max-width: 460px; margin: 0 auto;">
                    <?php if(!empty($model->mainImage) && $model->mainImage->hasFile()): ?>
                        <img class="img-responsive" src="<?= $model->mainImage->getThumbnailUrl(); ?>" alt="...">
                    <?php endif; ?>
                    <div class="panel-body"><h3 class="panel-title"><?= $model->title; ?></h3>
                        <p><?= $model->description; ?></p>
                    </div>
                    <div class="panel-footer">
                        <div class="row">
                            <div class="col-sm-6"><p><a  href="tel:<?= $model->phone; ?>"  class="btn btn-default classyfy-btn"><i class="fa fa-phone"></i> <?= $model->phone; ?> </a></p></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <hr>

        <?= $form->field($model,'approved_by_sa')->dropDownList([
            1 => Yii::t('app','Yes'),
            0 => Yii::t('app','No'),
        ],['data-activate' => "#reason-block:0"])->label(Yii::t('app','Approved (can be published)')); ?>

        <div id="reason-block" class="hidden">
            <?= $form->field($model,'refuse_reason')->textarea(); ?>
        </div>
    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-default pull-left" data-dismiss="modal"><?= Yii::t('app','Cancel'); ?></button>
        <button type="submit" class="btn btn-primary"><?= Yii::t('app','Save'); ?></button>
    </div>

<?php ActiveForm::end(); ?>