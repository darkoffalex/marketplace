<?php
/* @var $model \app\models\Marketplace */
?>

<div class="modal-header">
    <h4 class="modal-title"><?= Yii::t('app','Selling rules'); ?></h4>
</div>
<div class="modal-body">
    <?= $model->selling_rules; ?>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default pull-left" data-dismiss="modal"><?= Yii::t('app','Close'); ?></button>
</div>
