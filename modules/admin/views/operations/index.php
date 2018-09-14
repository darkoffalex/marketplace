<?php

use kartik\grid\GridView;
use yii\helpers\Url;
use yii\bootstrap\Html;
use app\modules\admin\helpers\Access;
use kartik\select2\Select2;
use yii\web\JsExpression;
use app\helpers\Constants;
use yii\helpers\ArrayHelper;
use app\helpers\Help;

/* @var $searchModel \app\models\MoneyTransactionSearch */
/* @var $dataProvider \yii\data\ActiveDataProvider */
/* @var $this \yii\web\View */
/* @var $controller \app\modules\admin\controllers\OperationsController */
/* @var $user \app\models\User */

$controller = $this->context;
$user = Yii::$app->user->identity;

$this->title = Yii::t('app','Operations');
$this->params['breadcrumbs'][] = $this->title;

$gridColumns = [
    [
        'attribute' => 'id',
        'contentOptions' => ['style' => 'width:70px;']
    ],

    [
        'attribute' => 'amount',
        'format' => 'raw',
        'value' => function ($model, $key, $index, $column){
            /* @var $model \app\models\MoneyTransactionSearch */
            return \app\helpers\Help::toPrice($model->amount).' ₽';
        },
    ],

    [
        'attribute' => 'from_account_id',
        'filter' => Select2::widget([
            'model' => $searchModel,
            'attribute' => 'from_account_id',
            'initValueText' => !empty($searchModel->fromAccount) ? $searchModel->fromAccount->getFullName() : '',
            'options' => ['placeholder' => Yii::t('app','Search for account..')],
            'language' => Yii::$app->language,
            'theme' => Select2::THEME_DEFAULT,
            'pluginOptions' => [
                'allowClear' => true,
                'minimumInputLength' => 1,
                'language' => [
                    'noResults' => new JsExpression("function () { return '".Yii::t('app','No results found')."'; }"),
                    'searching' => new JsExpression("function () { return '".Yii::t('app','Searching...')."'; }"),
                    'inputTooShort' => new JsExpression("function(args) {return '".Yii::t('app','Type more characters')."'}"),
                    'errorLoading' => new JsExpression("function () { return '".Yii::t('app','Waiting for results')."'; }"),
                ],
                'ajax' => [
                    'url' => Url::to(['/admin/accounts/ajax-search']),
                    'dataType' => 'json',
                    'data' => new JsExpression('function(params) { return {q:params.term}; }')
                ],
                'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                'templateResult' => new JsExpression('function(item) { return item.text; }'),
                'templateSelection' => new JsExpression('function (item) { return item.text; }'),
            ],
        ]),
        'label' => Yii::t('app','From'),
        'format' => 'raw',
        'value' => function ($model, $key, $index, $column){
            /* @var $model \app\models\MoneyTransactionSearch */
            return !empty($model->fromAccount) ? $model->fromAccount->getFullName() : null;
        },
    ],

    [
        'attribute' => 'to_account_id',
        'filter' => Select2::widget([
            'model' => $searchModel,
            'attribute' => 'to_account_id',
            'initValueText' => !empty($searchModel->toAccount) ? $searchModel->toAccount->getFullName() : '',
            'options' => ['placeholder' => Yii::t('app','Search for account..')],
            'language' => Yii::$app->language,
            'theme' => Select2::THEME_DEFAULT,
            'pluginOptions' => [
                'allowClear' => true,
                'minimumInputLength' => 1,
                'language' => [
                    'noResults' => new JsExpression("function () { return '".Yii::t('app','No results found')."'; }"),
                    'searching' => new JsExpression("function () { return '".Yii::t('app','Searching...')."'; }"),
                    'inputTooShort' => new JsExpression("function(args) {return '".Yii::t('app','Type more characters')."'}"),
                    'errorLoading' => new JsExpression("function () { return '".Yii::t('app','Waiting for results')."'; }"),
                ],
                'ajax' => [
                    'url' => Url::to(['/admin/accounts/ajax-search']),
                    'dataType' => 'json',
                    'data' => new JsExpression('function(params) { return {q:params.term}; }')
                ],
                'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                'templateResult' => new JsExpression('function(item) { return item.text; }'),
                'templateSelection' => new JsExpression('function (item) { return item.text; }'),
            ],
        ]),
        'label' => Yii::t('app','To'),
        'format' => 'raw',
        'value' => function ($model, $key, $index, $column){
            /* @var $model \app\models\MoneyTransactionSearch */
            return !empty($model->toAccount) ? $model->toAccount->getFullName() : null;
        },
    ],

    [
        'attribute' => 'description'
    ],

    [
        'attribute' => 'type_id',
        'label' => Yii::t('app','Type'),
        'format' => 'raw',
        'filter' => [
            Constants::PAYMENT_WEB_INITIATED => Yii::t('app','Web (manual)'),
            Constants::PAYMENT_WEB_RECURRENT_INITIATED => Yii::t('app','Web (recurrent)'),
            Constants::PAYMENT_INTERNAL_INITIATED => Yii::t('app','Internal (by system or admin)'),
        ],
        'value' => function ($model, $key, $index, $column){
            /* @var $model \app\models\MoneyTransactionSearch */
            $names = [
                Constants::PAYMENT_WEB_INITIATED => Yii::t('app','Web (manual)'),
                Constants::PAYMENT_WEB_RECURRENT_INITIATED => Yii::t('app','Web (recurrent)'),
                Constants::PAYMENT_INTERNAL_INITIATED => Yii::t('app','Internal (by system or admin)'),
            ];
            return ArrayHelper::getValue($names,$model->type_id,Yii::t('app','Unknown'));
        },
    ],

    [
        'attribute' => 'status_id',
        'filter' => [
            Constants::PAYMENT_STATUS_NEW => Yii::t('app','New (in progress)'),
            Constants::PAYMENT_STATUS_DONE => Yii::t('app','Done'),
            Constants::PAYMENT_STATUS_CANCELED => Yii::t('app','Canceled'),
        ],
        'enableSorting' => false,
        'format' => 'raw',
        'value' => function ($model, $key, $index, $column){
            /* @var $model \app\models\MoneyTransactionSearch */
            $names = [
                Constants::PAYMENT_STATUS_NEW => '<span class="label label-warning">'.Yii::t('app','New (in progress)').'</span>',
                Constants::PAYMENT_STATUS_DONE => '<span class="label label-success">'.Yii::t('app','Done').'</span>',
                Constants::PAYMENT_STATUS_CANCELED => '<span class="label label-danger">'.Yii::t('app','Canceled').'</span>',
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
            /* @var $model \app\models\User */
            return !empty($model->created_at) ? Help::dateReformat($model->created_at) : null;
        },
    ],

    [
        'class' => 'yii\grid\ActionColumn',
        'contentOptions'=>['style'=>'width: 140px; text-align: center;'],
        'header' => Yii::t('app','Actions'),
        'template' => '{update} &nbsp; {delete}',
        'buttons' => [
            'update' => function ($url,$model,$key) {
                /* @var $model \app\models\MoneyTransactionSearch */
                return Html::a('<span class="glyphicon glyphicon-pencil"></span>', Url::to(['/admin/operations/update', 'id' => $model->id]), ['title' => Yii::t('app','Edit'), 'data-target' => '.modal-main', 'data-toggle'=>'modal']);
            },
        ],
        'visibleButtons' => [
            'delete' => function ($model, $key, $index) use ($user) {/* @var $model \app\models\MoneyTransactionSearch */ return Access::has($user,'operations','delete');},
            'update' => function ($model, $key, $index) use ($user) {/* @var $model \app\models\MoneyTransactionSearch */ return Access::has($user,'operations','update');},
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
            <?php if(Access::has($user,'operations','create')): ?>
                <div class="box-footer">
                    <a href="<?php echo Url::to(['/admin/operations/create']); ?>" data-toggle="modal" data-target=".modal-main" class="btn btn-primary"><?= Yii::t('app','Create'); ?></a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
