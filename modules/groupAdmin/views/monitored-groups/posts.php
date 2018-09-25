<?php
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
use app\models\MonitoredGroup;
use yii\widgets\LinkPager;
use app\helpers\Help;
use yii\helpers\Url;

/* @var $this \yii\web\View */
/* @var $controller \app\modules\groupAdmin\controllers\MonitoredGroupsController */

/* @var $searchModel \app\models\MonitoredGroupPost */
/* @var $dataProvider \yii\data\ActiveDataProvider */


$controller = $this->context;
$this->title = Yii::t('app','Posts');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app','Groups'), 'url' => Url::to(['/group-admin/monitored-groups/index'])];
$this->params['breadcrumbs'][] = $this->title;
?>

<style type="text/css">
    .video-img-link
    {
        position: relative;
    }
    .video-img-link::before{
        color: white;
        content: "\25B6";
        opacity: 0.5;
        position: absolute;
        text-shadow: 0 3px black;
        z-index: 100;
        font-size:50px;
        display: block;
        width: 100%;
        text-align: center;
        top: -30px;
    }
</style>

<div class="row">
    <div class="col-md-12">
        <div class="box box-danger">
            <div class="box-header with-border">
                <h3 class="box-title"><?= Yii::t('app','Filtration'); ?></h3>
            </div>
            <div class="box-body">
                <?php $form = ActiveForm::begin([
                    'id' => 'filtration-form',
                    'method' => 'get',
                    'options' => ['role' => 'form', 'method' => 'post', 'enctype' => 'multipart/form-data'],
                    'enableClientValidation'=>false,
                    'enableAjaxValidation' => false,
                    'fieldConfig' => [
                        'template' => "{label}\n{input}\n{error}\n",
                    ],
                ]); ?>
                <div class="row">
                    <div class="col-md-3">
                        <?= $form->field($searchModel, 'facebook_id')->textInput(); ?>
                    </div>
                    <div class="col-md-3">
                        <?= $form->field($searchModel, 'group_id')->dropDownList(ArrayHelper::map(MonitoredGroup::find()->all(),'id','name')); ?>
                    </div>
                    <div class="col-md-3">
                        <?= $form->field($searchModel, 'text')->textInput(); ?>
                    </div>
                    <div class="col-md-3">
                        <button class="btn btn-primary" style="margin-top: 25px;" type="submit"><?= Yii::t('app','Filtration'); ?></button>
                    </div>
                </div>
                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
                <li class="active"><a href="#activity" data-toggle="tab"><?= Yii::t('app','Posts'); ?></a></li>
            </ul>
            <div class="tab-content">
                <div class="active tab-pane" id="activity">
                    <?php if($dataProvider->totalCount > 0): ?>
                        <?php foreach ($dataProvider->models as $post): ?>
                            <?php /* @var $post \app\models\MonitoredGroupPost */ ?>
                            <div class="post">
                                <div class="user-block">
                                    <span class="description no-margin"><?= Help::dateReformat($post->created_at,'d.m.Y H:i'); ?></span>
                                </div>
                                <p><?= $post->text; ?></p>
                                <ul class="list-inline">
                                    <?php $count = $post->getMonitoredGroupPostComments()->count(); ?>
                                    <?php if($count > 0): ?>
                                        <li><a data-toggle="modal" data-target=".modal-main" href="<?= Url::to(['/group-admin/monitored-groups/comments', 'id' => $post->id]); ?>"><i class="fa fa-comments-o margin-r-5"></i><?= Yii::t('app','Comments'); ?>: (<?= $count; ?>)</a></li>
                                    <?php else: ?>
                                        <li><i class="fa fa-comments-o margin-r-5"></i> <?= Yii::t('app','Comments'); ?>: (<?= $count; ?>)</li>
                                    <?php endif; ?>
                                    <li><a target="_blank" href="https://www.facebook.com/<?= $post->facebook_id; ?>/"><i class="fa fa-facebook margin-r-5"></i><?= Yii::t('app','View on facebook ({id})', ['id' => explode('_',$post->facebook_id)[1]]); ?></a></li>
                                </ul>
                            </div>
                        <?php endforeach; ?>
                        <?= LinkPager::widget(['pagination' => $dataProvider->getPagination()]); ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>