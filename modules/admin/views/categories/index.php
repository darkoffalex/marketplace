<?php

use yii\helpers\Url;

/* @var $searchModel \app\models\CategorySearch */
/* @var $dataProvider \yii\data\ActiveDataProvider */
/* @var $this \yii\web\View */
/* @var $controller \app\modules\admin\controllers\CategoriesController */
/* @var $user \app\models\User */
/* @var $root int */


$controller = $this->context;
$user = Yii::$app->user->identity;

$this->title = Yii::t('app','Category list');
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="row">
    <div class="col-xs-12">
        <div class="box">
            <div class="box-header">
                <h3 class="box-title"><?= Yii::t('app','List'); ?></h3>
            </div>
            <div class="box-body">
                <?= $this->render('_index',compact('searchModel','dataProvider','root')); ?>
            </div>
            <div class="box-footer">
                <a data-toggle="modal" data-target=".modal" href="<?php echo Url::to(['/admin/categories/create']); ?>" class="btn btn-primary"><?= Yii::t('app','Create'); ?></a>
            </div>
        </div>
    </div>
</div>
