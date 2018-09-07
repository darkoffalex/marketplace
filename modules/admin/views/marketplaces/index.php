<?php

use kartik\grid\GridView;
use yii\helpers\Url;
use app\helpers\Help;
use app\helpers\Constants;
use app\models\Country;
use yii\helpers\ArrayHelper;
use kartik\select2\Select2;
use yii\web\JsExpression;

/* @var $searchModel \app\models\MarketplaceSearch */
/* @var $dataProvider \yii\data\ActiveDataProvider */
/* @var $this \yii\web\View */
/* @var $controller \app\modules\admin\controllers\MarketplacesController */
/* @var $user \app\models\User */

$controller = $this->context;
$user = Yii::$app->user->identity;

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
        'attribute' => 'user_id',
        'enableSorting' => false,
        'filter' => Select2::widget([
            'model' => $searchModel,
            'attribute' => 'user_id',
            'initValueText' => !empty($searchModel->user) ? $searchModel->user->name : '',
            'options' => ['placeholder' => Yii::t('app','Search for a user...')],
            'language' => Yii::$app->language,
            'theme' => Select2::THEME_DEFAULT,
            'pluginOptions' => [
                'allowClear' => true,
                'minimumInputLength' => 2,
                'language' => [
                    'noResults' => new JsExpression("function () { return '".Yii::t('app','No results found')."'; }"),
                    'searching' => new JsExpression("function () { return '".Yii::t('app','Searching...')."'; }"),
                    'inputTooShort' => new JsExpression("function(args) {return '".Yii::t('app','Type more characters')."'}"),
                    'errorLoading' => new JsExpression("function () { return '".Yii::t('app','Waiting for results')."'; }"),
                ],
                'ajax' => [
                    'url' => Url::to(['/admin/users/ajax-search']),
                    'dataType' => 'json',
                    'data' => new JsExpression('function(params) { return {q:params.term}; }')
                ],
                'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                'templateResult' => new JsExpression('function(user) { return user.text; }'),
                'templateSelection' => new JsExpression('function (user) { return user.text; }'),
            ],
        ]),
        'format' => 'raw',
        'value' => function ($model, $key, $index, $column){
            /* @var $model \app\models\CvSearch */
            return !empty($model->user) ? $model->user->name : null;
        },
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
        'class' => 'yii\grid\ActionColumn',
        'contentOptions'=>['style'=>'width: 140px; text-align: center;'],
        'header' => Yii::t('app','Actions'),
        'template' => '{update} &nbsp; {delete}',
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
                <a href="<?php echo Url::to(['/admin/marketplaces/create']); ?>" class="btn btn-primary"><?= Yii::t('app','Create'); ?></a>
            </div>
        </div>
    </div>
</div>
