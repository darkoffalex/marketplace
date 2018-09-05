<?php
/* @var $this \yii\web\View */
/* @var $user \app\models\User */
/* @var $controller \app\modules\groupAdmin\controllers\MessagesController */
/* @var $message string */

$controller = $this->context;
$user = Yii::$app->user->identity;
?>

<div class="modal-header">
    <h4 class="modal-title"><?= Yii::t('app','Create new user'); ?></h4>
</div>

<div class="modal-body">
    <p><?= $message; ?></p>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-default pull-left" data-dismiss="modal"><?= Yii::t('app','OK'); ?></button>
</div>