<?php
/* @var $model \app\models\Poster */
?>

<div class="modal-header">
    <h4 class="modal-title"><?= Yii::t('app','Refuse reason'); ?></h4>
</div>
<div class="modal-body">
    <p><?= $model->refuse_reason; ?></p>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default pull-left" data-dismiss="modal"><?= Yii::t('app','Close'); ?></button>
</div>
