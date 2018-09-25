<?php

use kartik\grid\GridView;
use yii\helpers\Url;
use yii\bootstrap\Html;
use app\helpers\Constants;
use yii\helpers\ArrayHelper;
use app\models\Dictionary;
use kartik\widgets\SwitchInput;
use yii\web\JsExpression;

/* @var $searchModel \app\models\MonitoredGroup*/
/* @var $dataProvider \yii\data\ActiveDataProvider */
/* @var $this \yii\web\View */
/* @var $controller \app\modules\groupAdmin\controllers\MonitoredGroupsController */
/* @var $user \app\models\User */

$controller = $this->context;
$user = Yii::$app->user->identity;

$this->title = Yii::t('app','Groups');
$this->params['breadcrumbs'][] = $this->title;

$gridColumns = [
    [
        'attribute' => 'id',
        'contentOptions' => ['style' => 'width:70px;']
    ],

    [
        'attribute' => 'facebook_id',
        'enableSorting' => false,
    ],

    [
        'attribute' => 'name',
        'enableSorting' => false,
    ],
    
    [
        'attribute' => 'dictionaries_arr',
        'filter' => ArrayHelper::map(Dictionary::find()->where(['user_id' => Yii::$app->user->id])->all(),'id','name'),
        'enableSorting' => false,
        'format' => 'raw',
        'value' => function ($model, $key, $index, $column){
            /* @var $model \app\models\MonitoredGroup */
            return Html::checkboxList(
                'dictionaries',
                array_keys(ArrayHelper::map($model->dictionaries,'id','name')),
                ArrayHelper::map(Dictionary::find()->where(['user_id' => Yii::$app->user->id])->all(),'id','name'),
                ['class' => 'dict-selectors', 'data-url' => Url::to(['/group-admin/monitored-groups/set-dictionaries', 'id' => $model->id])]
            );
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
        'value' => function ($model, $key, $index, $column) {

            /* @var $model \app\models\MonitoredGroup */
            $changeUrl = Url::to(['/group-admin/monitored-groups/status-change', 'id' => $model->id]);
            return SwitchInput::widget([
                'name'=>'enabled',
                'value'=>$model->status_id == Constants::STATUS_ENABLED,
                'pluginOptions' => [
                    'onColor' => 'success',
                    'offColor' => 'danger',
                    'size' => 'mini',
                    'onText' => 'Да',
                    'offText' => 'Нет',
                    'onSwitchChange' => new JsExpression("function(event, state){ $.ajax({url : '{$changeUrl}?status='+state, success: function (response) {}}); return true;}")
                ],
            ]);
        },
    ],

    [
        'header' => Yii::t('app','Collected posts'),
        'format' => 'raw',
        'value' => function ($model, $key, $index, $column){
            /* @var $model \app\models\MonitoredGroup */
            return Html::a($model->getMonitoredGroupPosts()->count(),['/group-admin/monitored-groups/posts', 'MonitoredGroupPost[group_id]' => $model->id]);
        },
    ],

    [
        'class' => 'yii\grid\ActionColumn',
        'contentOptions'=>['style'=>'width: 140px; text-align: center;'],
        'header' => Yii::t('app','Actions'),
        'template' => '{update} &nbsp; {delete} &nbsp; {info}',
        'buttons' => [
            'update' => function ($url,$model,$key) {
                /* @var $model \app\models\Category */
                return Html::a('<span class="glyphicon glyphicon-pencil"></span>', Url::to(['/group-admin/monitored-groups/update', 'id' => $model->id]), ['title' => Yii::t('app','Edit'), 'data-target' => '.modal-main', 'data-toggle'=>'modal']);
            },
            'delete' => function ($url,$model,$key) {
                /* @var $model \app\models\Category */
                return Html::a('<span class="glyphicon glyphicon-trash"></span>', Url::to(['/group-admin/monitored-groups/delete', 'id' => $model->id]), ['title' => Yii::t('app','Delete'), 'data-confirm' => Yii::t('app','Delete for eternity?')]);
            },
            'info' => function ($url,$model,$key) {
                /* @var $model \app\models\Category */
                return Html::a('<span class="glyphicon glyphicon-info-sign"></span>', Url::to(['/group-admin/monitored-groups/info', 'id' => $model->id]), ['title' => Yii::t('app','Information'), 'data-target' => '.modal-main', 'data-toggle'=>'modal']);
            },
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
                <a href="<?php echo Url::to(['/group-admin/monitored-groups/create']); ?>" data-toggle="modal" data-target=".modal-main" class="btn btn-primary"><?= Yii::t('app','Add group'); ?></a>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        $('.dict-selectors').find('input[type=checkbox]').change(function () {
            var selectedIds = [];
            var url = $(this).parent().parent().parent().data('url');

            $(this).parent().parent().parent().find('input[type=checkbox]').each(function () {
                if($(this).prop('checked')){
                    selectedIds.push($(this).val());
                }
            });

            $.ajax({
                method: "POST",
                url: url,
                data: {'dictionaries':selectedIds}
            }).done(function( msg ) {
                console.log(msg);
            });
        });
    });
</script>
