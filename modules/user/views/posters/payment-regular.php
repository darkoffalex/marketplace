<?php
use yii\helpers\Url;
use app\models\User;
use app\helpers\Help;

/* @var $model \app\models\Poster*/
/* @var $this \yii\web\View */
/* @var $user User */


$user = Yii::$app->user->identity;

$this->title = Yii::t('app','Pay');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app','Advertisements'), 'url' => Url::to(['/user/posters/index'])];
$this->params['breadcrumbs'][] = ['label' => Yii::t('app','Edit advertisement'), 'url' => Url::to(['/user/posters/update', 'id' => $model->id])];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row">
    <div class="col-md-12">

        <div class="box box-primary">
            <div class="box-header with-border"><h3 class="box-title"><?= Yii::t('app','Payment information'); ?></h3></div>
            <div class="box-body">
                <p><strong><?= Yii::t('app','Tariff'); ?>:</strong> <?= $model->marketplaceTariff->getNameWithDetails(); ?></p>
                <p><strong><?= Yii::t('app','Published until'); ?>:</strong> <?= $model->marketplaceTariff->tariff->getUntilDate(); ?></p>
                <p><strong><?= Yii::t('app','Subscription'); ?>:</strong>
                    <?= $model->marketplaceTariff->tariff->subscription ? '<span class="label label-success">'.Yii::t('app','Yes').'</span>' : '<span class="label label-danger">'.Yii::t('app','No').'</span>'; ?>
                </p>
                <hr>
                <form method="post" action="">
                    <input type="hidden" name="<?= Yii::$app->request->csrfParam; ?>" value="<?= Yii::$app->request->csrfToken; ?>" />
                    <input type="hidden" name="test-mode" value="1">
                    <button type="submit" class="btn btn-primary"><?= Yii::t('app','Pay'); ?></button>
                </form>
            </div>
        </div>
    </div>
</div>