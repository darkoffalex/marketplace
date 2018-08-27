<?php

use kartik\grid\GridView;
use yii\helpers\Url;
use app\helpers\Constants;
use yii\bootstrap\Html;
use app\modules\admin\helpers\Access;

/* @var $searchModel \app\models\CountrySearch */
/* @var $dataProvider \yii\data\ActiveDataProvider */
/* @var $this \yii\web\View */
/* @var $controller \app\modules\admin\controllers\CountriesController */
/* @var $user \app\models\User */

$controller = $this->context;
$user = Yii::$app->user->identity;

$this->title = Yii::t('app','Countries');
$this->params['breadcrumbs'][] = $this->title;

$gridColumns = [
    [
        'attribute' => 'id',
        'contentOptions' => ['style' => 'width:70px;']
    ],

    [
        'attribute' => 'name',
        'enableSorting' => false,
    ],

    [
        'attribute' => 'domain_alias',
        'enableSorting' => false,
    ],

    [
        'attribute' => 'status_id',
        'filter' => [
            Constants::STATUS_ENABLED => Yii::t('app','Enabled'),
            Constants::STATUS_DISABLED => Yii::t('app','Disabled'),
        ],
        'enableSorting' => false,
        'format' => 'raw',
        'value' => function ($model, $key, $index, $column){
            /* @var $model \app\models\Country */
            $names = [
                Constants::STATUS_ENABLED => '<span class="label label-success">'.Yii::t('app','Enabled').'</span>',
                Constants::STATUS_DISABLED => '<span class="label label-success">'.Yii::t('app','Disabled').'</span>',
            ];
            return !empty($names[$model->status_id]) ? $names[$model->status_id] : null;
        },
    ],
    
    [
        'class' => 'yii\grid\ActionColumn',
        'contentOptions'=>['style'=>'width: 140px; text-align: center;'],
        'header' => Yii::t('app','Actions'),
        'template' => '{update} &nbsp; {delete} &nbsp; {move-up} &nbsp; {move-down}',
        'buttons' => [
            'update' => function ($url,$model,$key) {
                /* @var $model \app\models\Country */
                return Html::a('<span class="glyphicon glyphicon-pencil"></span>', Url::to(['/admin/countries/update', 'id' => $model->id]), ['title' => Yii::t('app','Edit'), 'data-target' => '.modal-main', 'data-toggle'=>'modal']);
            },
            'move-up' => function ($url,$model,$key) {
                /* @var $model \app\models\Country */
                return Html::a('<span class="glyphicon glyphicon-arrow-up"></span>', Url::to(['/admin/countries/move', 'id' => $model->id, 'dir' => 'up']), ['title' => Yii::t('app','Move up')]);
            },
            'move-down' => function ($url,$model,$key) {
                /* @var $model \app\models\Country */
                return Html::a('<span class="glyphicon glyphicon-arrow-down"></span>', Url::to(['/admin/countries/move', 'id' => $model->id, 'dir' => 'down']), ['title' => Yii::t('app','Move bottom')]);
            },
        ],
        'visibleButtons' => [
            'delete' => function ($model, $key, $index) use ($user) {/* @var $model \app\models\Country */ return Access::has($user,'countries','delete') && empty($model->is_default);},
            'update' => function ($model, $key, $index) use ($user) {/* @var $model \app\models\Country */ return Access::has($user,'countries','update');},
            'move-up' => function ($model, $key, $index) use ($user) {/* @var $model \app\models\Country */ return Access::has($user,'countries','move');},
            'move-down' => function ($model, $key, $index) use ($user) {/* @var $model \app\models\Country */ return Access::has($user,'countries','move');},
        ],
    ],
];

?>

<div class="row">
    <div class="col-xs-12">
        <div class="box">
            <div class="box-header"></div>
            <div class="box-body">
                <?= GridView::widget([
                    'filterModel' => $searchModel,
                    'dataProvider' => $dataProvider,
                    'columns' => $gridColumns,
                    'pjax' => false,
                ]); ?>
            </div>
            <?php if(Access::has($user,'countries','create')): ?>
                <div class="box-footer">
                    <a href="<?php echo Url::to(['/admin/countries/create']); ?>" data-toggle="modal" data-target=".modal-main" class="btn btn-primary"><?= Yii::t('app','Create'); ?></a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
