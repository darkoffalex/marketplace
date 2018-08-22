<?php

use kartik\grid\GridView;
use yii\helpers\Url;
use app\modules\admin\helpers\Access;
use app\helpers\Help;
use app\helpers\Constants;

/* @var $searchModel \app\models\UserSearch */
/* @var $dataProvider \yii\data\ActiveDataProvider */
/* @var $this \yii\web\View */
/* @var $controller \app\modules\admin\controllers\UsersController */
/* @var $user \app\models\User */

$controller = $this->context;
$user = Yii::$app->user->identity;

$this->title = Yii::t('app','Users');
$this->params['breadcrumbs'][] = $this->title;

$gridColumns = [
//    ['class' => 'yii\grid\SerialColumn'],
    [
        'attribute' => 'id',
        'contentOptions' => ['style' => 'width:70px;']
    ],

    ['attribute' => 'username'],

    [
        'attribute' => 'name',
    ],

    [
        'attribute' => 'last_online_at',
        'filter' => \kartik\daterange\DateRangePicker::widget([
            'model' => $searchModel,
            'convertFormat' => true,
            'attribute' => 'last_online_at',
            'pluginOptions' => [
                'locale' => [
                    'format'=>'d.m.Y',
                    'separator'=>' - ',
                ],
            ],
        ]),
        'enableSorting' => true,
        'format' => 'raw',
        'value' => function ($model, $key, $index, $column){
            /* @var $model \app\models\User */
            return !empty($model->last_online_at) ? Help::dateReformat($model->last_online_at) : null;
        },
    ],

    [
        'attribute' => 'created_at',
        'filter' => \kartik\daterange\DateRangePicker::widget([
            'model' => $searchModel,
            'convertFormat' => true,
            'attribute' => 'created_at',
            'pluginOptions' => [
                'locale' => [
                    'format'=>'d.m.Y',
                    'separator'=>' - ',
                ],
            ],
        ]),
        'enableSorting' => true,
        'format' => 'raw',
        'value' => function ($model, $key, $index, $column){
            /* @var $model \app\models\User */
            return !empty($model->created_at) ? Help::dateReformat($model->created_at) : null;
        },
    ],

    [
        'attribute' => 'role_id',
        'filter' => [
            Constants::ROLE_ADMIN => Yii::t('app','Admin')
        ],
        'enableSorting' => false,
        'format' => 'raw',
        'value' => function ($model, $key, $index, $column){
            $roles = [
                Constants::ROLE_ADMIN => Yii::t('app','Admin')
            ];

            /* @var $model \app\models\User */
            return !empty($roles[$model->role_id]) ? $roles[$model->role_id] : null;
        },
    ],


    [
        'attribute' => 'status_id',
        'filter' => [
            Constants::USR_STATUS_ENABLED => Yii::t('app','Enabled'),
            Constants::USR_STATUS_DISABLED => Yii::t('app','Disabled'),
        ],
        'enableSorting' => false,
        'format' => 'raw',
        'value' => function ($model, $key, $index, $column){
            /* @var $model \app\models\User */
            $names = [
                Constants::USR_STATUS_ENABLED => Yii::t('app','Enabled'),
                Constants::USR_STATUS_DISABLED => Yii::t('app','Disabled'),
            ];
            return !empty($names[$model->status_id]) ? $names[$model->status_id] : null;
        },
    ],

    [
        'class' => 'yii\grid\ActionColumn',
        'contentOptions'=>['style'=>'width: 140px; text-align: center;'],
        'header' => Yii::t('app','Actions'),
        'template' => '{update} &nbsp; {delete}',
        'visibleButtons' => [
            'delete' => function ($model, $key, $index) use ($user) {/* @var $model \app\models\User */ return Access::has($user,'users','delete') && Access::higher($user->role_id,$model->role_id);},
            'update' => function ($model, $key, $index) use ($user) {/* @var $model \app\models\User */ return Access::has($user,'users','update') && Access::higher($user->role_id,$model->role_id,true);},
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
            <div class="box-footer">
                <?php if(Access::has($user,'users','create')): ?>
                    <a href="<?php echo Url::to(['/admin/users/create-ajax']); ?>" data-toggle="modal" data-target=".modal-main" class="btn btn-primary"><?= Yii::t('app','Create'); ?></a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
