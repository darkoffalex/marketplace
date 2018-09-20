<?php

use kartik\grid\GridView;
use yii\helpers\Url;
use app\helpers\Help;
use yii\bootstrap\Html;
use app\helpers\Constants;
use app\models\Country;
use yii\helpers\ArrayHelper;

/* @var $searchModel \app\models\MarketplaceSearch */
/* @var $dataProvider \yii\data\ActiveDataProvider */
/* @var $this \yii\web\View */
/* @var $controller \app\modules\groupAdmin\controllers\MarketplacesController */
/* @var $user \app\models\User */

$controller = $this->context;
$user = Yii::$app->user->identity;

$this->registerJsFile('@web/common/js/clipboard.js');

$this->title = Yii::t('app','Marketplaces');
$this->params['breadcrumbs'][] = $this->title;

$countries = Country::find()
    ->where(['status_id' => Constants::STATUS_ENABLED])
    ->orderBy('priority ASC')
    ->all();

$gridColumns = [
    [
        'attribute' => 'id',
        'contentOptions' => ['style' => 'width:70px;'],
        'headerOptions' => ['style' => 'width:70px;'],
    ],

    [
        'attribute' => 'name',
        'enableSorting' => false,
    ],

    [
        'attribute' => 'country_id',
        'filter' => ArrayHelper::map($countries,'id','name'),
        'enableSorting' => false,
        'format' => 'raw',
        'value' => function ($model, $key, $index, $column){
            /* @var $model \app\models\MarketplaceSearch */
           return !empty($model->country) ? $model->country->name : null;
        },
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
            /* @var $model \app\models\MarketplaceSearch */
            $names = [
                Constants::STATUS_ENABLED => '<span class="label label-success">'.Yii::t('app','Enabled').'</span>',
                Constants::STATUS_DISABLED => '<span class="label label-danger">'.Yii::t('app','Disabled').'</span>',
            ];
            return !empty($names[$model->status_id]) ? $names[$model->status_id] : null;
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
            /* @var $model \app\models\MarketplaceSearch */
            return Help::dateReformat($model->created_at);
        },
    ],

    [
        'header' => Yii::t('app','Advertisements'),
        'enableSorting' => false,
        'format' => 'raw',
        'value' => function ($model, $key, $index, $column){
            /* @var $model \app\models\MarketplaceSearch */
            $count = $model
                ->getPosters()
                ->andWhere('status_id != :tempStatus',['tempStatus' => Constants::STATUS_TEMPORARY])
                ->count();
            return Html::a($count ,Url::to(['/group-admin/posters/index', 'PosterSearch[marketplace_id]' => $model->id]));
        },
    ],

    [
        'header' => Yii::t('app','Link'),
        'format' => 'raw',
        'enableSorting' => false,
        'value' => function($model, $key, $index, $column){
            /* @var $model \app\models\MarketplaceSearch*/
            return '<div class="input-group input-group-sm"><input id="copy-link-'.$model->id.'" class="form-control" readonly type="text" value="'.$model->GetLink(false).'"><span class="input-group-btn"><button title="'.Yii::t('app','Copy').'" type="button" data-clipboard-target="#copy-link-'.$model->id.'" class="btn btn-info btn-flat copy-text"><i class="fa fa-fw fa-clipboard"></i></button></span></div>';
        },
        'contentOptions' => ['style' => 'width:230px;'],
        'headerOptions' => ['style' => 'width:230px;']
    ],

    [
        'class' => 'yii\grid\ActionColumn',
        'contentOptions'=>['style'=>'width: 140px; text-align: center;'],
        'header' => Yii::t('app','Actions'),
        'template' => '{update}',
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
                <a href="<?php echo Url::to(['/group-admin/cvs/create']); ?>" data-toggle="modal" data-target=".modal-main" class="btn btn-primary"><?= Yii::t('app','New Proposal'); ?></a>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function(){
        new Clipboard('.copy-text');
    });
</script>