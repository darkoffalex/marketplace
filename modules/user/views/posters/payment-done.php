<?php
use yii\helpers\Url;
use app\models\User;
use app\helpers\Help;

/* @var $model \app\models\Poster*/
/* @var $this \yii\web\View */
/* @var $user User */


$user = Yii::$app->user->identity;

$this->title = Yii::t('app','Successful payment');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app','Advertisements'), 'url' => Url::to(['/user/posters/index'])];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row">
    <div class="col-md-12">

        <div class="box box-primary">
            <div class="box-header with-border"><h3 class="box-title"><?= Yii::t('app','Payment information'); ?></h3></div>
            <div class="box-body">
                <h4><?= Yii::t('app','Your advertisement «{title}» ({id}) successfully paid. Please wait until it be checked and moderated', ['title' => $model->title, 'id' => $model->id]); ?></h4>
            </div>
            <div class="box-footer">
                <a class="btn btn-primary" href="<?= Url::to(['/user/posters/index']); ?>"><?= Yii::t('app','Back to list'); ?></a>
            </div>
        </div>
    </div>
</div>