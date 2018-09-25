<?php
/* @var $model \app\models\MonitoredGroup*/
/* @var $this \yii\web\View */
/* @var $controller \app\modules\groupAdmin\controllers\MonitoredGroupsController */

$controller = $this->context;
?>

<div class="modal-header">
    <h4 class="modal-title"><?= Yii::t('app','System information'); ?></h4>
</div>

<div class="modal-body">
    <p><?= Yii::t('app','Last update'); ?> : <?= !empty($model->sync_done_last_time) ? $model->sync_done_last_time : Yii::t('app','No data'); ?></p>
    <p><?= Yii::t('app','Current parsing interval'); ?> : <?= $model->getSyncSinceOptimal()->format('Y-m-d H:i:s'); ?> - <?= $model->getSyncToOptimal()->format('Y-m-d H:i:s'); ?></p>
    <p><?= Yii::t('app','Current parsing status'); ?> : <?= $model->sync_in_progress ? Yii::t('app','In progress') : Yii::t('app','In queue'); ?></p>
    <hr>
    <div class="form-group">
        <label class="control-label" for="log"><?= Yii::t('app','Error log'); ?></label>
        <textarea class="form-control" id="log"><?= $model->parsing_errors_log; ?></textarea>
    </div>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-default pull-left" data-dismiss="modal"><?= Yii::t('app','Close'); ?></button>
</div>