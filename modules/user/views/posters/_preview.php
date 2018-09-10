<?php
/* @var $model \app\models\Poster */
?>

<div class="modal-header">
    <h4 class="modal-title"><?= Yii::t('app','Preview'); ?></h4>
</div>
<div class="modal-body">
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
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default pull-left" data-dismiss="modal"><?= Yii::t('app','Close'); ?></button>
</div>
