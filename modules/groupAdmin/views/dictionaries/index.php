<?php

use kartik\grid\GridView;
use yii\helpers\Url;
use yii\bootstrap\Html;
use yii\helpers\ArrayHelper;
use app\models\MonitoredGroup;
use app\helpers\Constants;
use kartik\widgets\SwitchInput;
use yii\web\JsExpression;

/* @var $searchModel \app\models\Dictionary*/
/* @var $dataProvider \yii\data\ActiveDataProvider */
/* @var $this \yii\web\View */
/* @var $controller \app\modules\groupAdmin\controllers\DictionariesController */
/* @var $user \app\models\User */

$controller = $this->context;
$user = Yii::$app->user->identity;

$this->title = Yii::t('app','Dictionaries');
$this->params['breadcrumbs'][] = $this->title;

$this->registerJsFile('@web/common/js/clipboard.js');

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
        'attribute' => 'key',
        'format' => 'raw',
        'value' => function($model, $key, $index, $column){
            /* @var $model \app\models\Dictionary */
            return '<div class="input-group input-group-sm"><input id="copy-link-'.$model->id.'" class="form-control" readonly type="text" value="'.$model->key.'"><span class="input-group-btn"><button title="'.Yii::t('app','Copy').'" type="button" data-clipboard-target="#copy-link-'.$model->id.'" class="btn btn-info btn-flat copy-text"><i class="fa fa-fw fa-clipboard"></i></button></span></div>';
        },
        'contentOptions' => ['style' => 'width:150px;'],
        'headerOptions' => ['style' => 'width:150px;']
    ],
    
    [
        'attribute' => 'groups_arr',
        'filter' => ArrayHelper::map(MonitoredGroup::find()->where(['user_id' => $user->id])->all(),'id','name'),
        'enableSorting' => false,
        'format' => 'raw',
        'value' => function ($model, $key, $index, $column){
            /* @var $model \app\models\Dictionary */
            return Html::checkboxList(
                'groups',
                array_keys(ArrayHelper::map($model->monitoredGroups,'id','name')),
                ArrayHelper::map(MonitoredGroup::find()->where(['user_id' => Yii::$app->user->id])->all(),'id','name'),
                ['class' => 'dict-selectors', 'data-url' => Url::to(['/group-admin/dictionaries/set-groups', 'id' => $model->id])]
            );
        },
    ],

    [
        'attribute' => 'words',
        'enableSorting' => false,
        'format' => 'raw',
        'value' => function ($model, $key, $index, $column){
            /* @var $model \app\models\Dictionary */
            return nl2br($model->words);
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
            $changeUrl = Url::to(['/group-admin/dictionaries/status-change', 'id' => $model->id]);
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
        'class' => 'yii\grid\ActionColumn',
        'contentOptions'=>['style'=>'width: 140px; text-align: center;'],
        'header' => Yii::t('app','Actions'),
        'template' => '{update} &nbsp; {delete} &nbsp; {info}',
        'buttons' => [
            'update' => function ($url,$model,$key) {
                /* @var $model \app\models\Dictionary */
                return Html::a('<span class="glyphicon glyphicon-pencil"></span>', Url::to(['/group-admin/dictionaries/update', 'id' => $model->id]), ['title' => Yii::t('app','Edit'), 'data-target' => '.modal-main', 'data-toggle'=>'modal']);
            },
            'delete' => function ($url,$model,$key) {
                /* @var $model \app\models\Dictionary */
                return Html::a('<span class="glyphicon glyphicon-trash"></span>', Url::to(['/group-admin/dictionaries/delete', 'id' => $model->id]), ['title' => Yii::t('app','Delete'), 'data-confirm' => Yii::t('app','Delete for eternity?')]);
            },
        ],
    ],
];

?>

<div class="alert alert-info alert-dismissible">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
    <h4><i class="icon fa fa-info"></i> <?= Yii::t('app','How to subscribe ?'); ?></h4>
    Copy the dictionary unique key, and go to the facebook <a target="_blank" href="https://www.facebook.com/Hype-Today-176997039650359/">hype.today page</a>. Then send message to page with command <code>DICT {copied key}</code>
</div>

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
                <a href="<?php echo Url::to(['/group-admin/dictionaries/create']); ?>" data-toggle="modal" data-target=".modal-main" class="btn btn-primary"><?= Yii::t('app','Create'); ?></a>
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
                data: {'groups':selectedIds}
            }).done(function( msg ) {
                console.log(msg);
            });
        });
    });
</script>

<script type="text/javascript">
    $(document).ready(function(){
        new Clipboard('.copy-text');
    });
</script>
