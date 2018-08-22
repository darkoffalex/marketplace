<?php
/* @var $this yii\web\View */
/* @var $controller \app\controllers\MainController */
/* @var $user \yii\web\User */

$controller = $this->context;
$user = Yii::$app->user->identity;

$controller->title = Yii::$app->name.' | '.Yii::t('app','Home');
$this->title = Yii::t('app','Home');
//$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row">
    <div class="col-md-12">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title"><?= Yii::t('app','Some title'); ?></h3>
            </div>
            <div class="box-body">
                <?= Yii::t('app','Some content'); ?>
            </div>
        </div>
    </div>
</div>
