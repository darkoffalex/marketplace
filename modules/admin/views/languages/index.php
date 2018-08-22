<?php

use kartik\grid\GridView;
use yii\helpers\Url;
use app\helpers\Constants;
use yii\bootstrap\Html;
use app\modules\admin\helpers\Access;
use app\helpers\Trl;

/* @var $searchModel \app\models\LanguageSearch */
/* @var $dataProvider \yii\data\ActiveDataProvider */
/* @var $this \yii\web\View */
/* @var $controller \app\modules\admin\controllers\UsersController */
/* @var $user \app\models\User */

$controller = $this->context;
$user = Yii::$app->user->identity;

$this->title = Yii::t('app','Languages');
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
        'attribute' => 'self_name',
        'enableSorting' => false,
    ],

    [
        'attribute' => 'prefix',
        'enableSorting' => false,
    ],

    [
        'attribute' => 'status_id',
        'filter' => [
            Constants::STATUS_ENABLED => Constants::GetStatusName(Constants::STATUS_ENABLED),
            Constants::STATUS_DISABLED => Constants::GetStatusName(Constants::STATUS_DISABLED)
        ],
        'enableSorting' => false,
        'format' => 'raw',
        'value' => function ($model, $key, $index, $column){
            /* @var $model \app\models\Language */
            $names = [
                Constants::STATUS_ENABLED => '<span class="label label-success">'.Constants::GetStatusName(Constants::STATUS_ENABLED).'</span>',
                Constants::STATUS_DISABLED => '<span class="label label-success">'.Constants::GetStatusName(Constants::STATUS_DISABLED).'</span>',
            ];
            return !empty($names[$model->status_id]) ? $names[$model->status_id] : null;
        },
    ],

    [
        'attribute' => 'is_default',
        'enableSorting' => false,
        'filter' => [
            (int)true => Yii::t('app','Find default')
        ],
        'format' => 'raw',
        'value' => function ($model, $key, $index, $column){
            /* @var $model \app\models\Language */
            return !empty($model->is_default) ? '<span class="label label-success">'.Yii::t('app','Yes').'</span>' : '<span class="label label-danger">'.Yii::t('app','No').'</span>';
        },
    ],

    [
        'class' => 'yii\grid\ActionColumn',
        'contentOptions'=>['style'=>'width: 140px; text-align: center;'],
        'header' => Yii::t('app','Actions'),
        'template' => '{update} &nbsp; {delete} &nbsp; {move-up} &nbsp; {move-down}',
        'buttons' => [
            'update' => function ($url,$model,$key) {
                /* @var $model \app\models\Language */
                return Html::a('<span class="glyphicon glyphicon-pencil"></span>', Url::to(['/admin/languages/update', 'id' => $model->id]), ['title' => Yii::t('app','Edit'), 'data-target' => '.modal-main', 'data-toggle'=>'modal']);
            },
            'move-up' => function ($url,$model,$key) {
                /* @var $model \app\models\Language */
                return Html::a('<span class="glyphicon glyphicon-arrow-up"></span>', Url::to(['/admin/languages/move', 'id' => $model->id, 'dir' => 'up']), ['title' => Yii::t('app','Move up')]);
            },
            'move-down' => function ($url,$model,$key) {
                /* @var $model \app\models\Language */
                return Html::a('<span class="glyphicon glyphicon-arrow-down"></span>', Url::to(['/admin/languages/move', 'id' => $model->id, 'dir' => 'down']), ['title' => Yii::t('app','Move bottom')]);
            },
        ],
        'visibleButtons' => [
            'delete' => function ($model, $key, $index) use ($user) {/* @var $model \app\models\Language */ return Access::has($user,'languages','delete') && empty($model->is_default);},
            'update' => function ($model, $key, $index) use ($user) {/* @var $model \app\models\Language */ return Access::has($user,'languages','update');},
            'move-up' => function ($model, $key, $index) use ($user) {/* @var $model \app\models\Language */ return Access::has($user,'languages','move');},
            'move-down' => function ($model, $key, $index) use ($user) {/* @var $model \app\models\Language */ return Access::has($user,'languages','move');},
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
            <?php if(Access::has($user,'languages','create')): ?>
                <div class="box-footer">
                    <a href="<?php echo Url::to(['/admin/languages/create']); ?>" data-toggle="modal" data-target=".modal-main" class="btn btn-primary"><?= Yii::t('app','Create'); ?></a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
