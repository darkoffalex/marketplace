<?php

use kartik\grid\GridView;
use yii\helpers\Url;
use yii\bootstrap\Html;
use app\helpers\Constants;

/* @var $searchModel \app\models\ShortLinkSearch */
/* @var $dataProvider \yii\data\ActiveDataProvider */
/* @var $this \yii\web\View */
/* @var $controller \app\modules\user\controllers\ShortLinksController*/
/* @var $user \app\models\User */

$controller = $this->context;
$user = Yii::$app->user->identity;

$this->title = Yii::t('app','Short links');
$this->params['breadcrumbs'][] = $this->title;

$this->registerJsFile('@web/common/js/clipboard.js');
$this->registerJsFile('@web/common/js/show-hide.js');

$gridColumns = [
    [
        'attribute' => 'id',
        'contentOptions' => ['style' => 'width:70px;']
    ],

    [
        'attribute' => 'clicks',
        'enableSorting' => true,
    ],

    [
        'attribute' => 'phone',
        'label' => Yii::t('app','Phone (WhatsApp)'),
        'enableSorting' => false,
    ],

//    [
//        'attribute' => 'original_link',
//        'enableSorting' => false,
//    ],

    [
        'attribute' => 'message',
        'enableSorting' => false,
    ],

//    [
//        'attribute' => 'status_id',
//        'filter' => [
//            Constants::USR_STATUS_ENABLED => Yii::t('app','Enabled'),
//            Constants::USR_STATUS_DISABLED => Yii::t('app','Disabled'),
//        ],
//        'enableSorting' => false,
//        'format' => 'raw',
//        'value' => function ($model, $key, $index, $column) {
//
//            /* @var $model \app\models\ShortLink */
//            $changeUrl = Url::to(['/user/short-links/status-change', 'id' => $model->id]);
//            return SwitchInput::widget([
//                'name'=>'enabled',
//                'value'=>$model->status_id == Constants::STATUS_ENABLED,
//                'pluginOptions' => [
//                    'onColor' => 'success',
//                    'offColor' => 'danger',
//                    'size' => 'mini',
//                    'onText' => 'Да',
//                    'offText' => 'Нет',
//                    'onSwitchChange' => new JsExpression("function(event, state){ $.ajax({url : '{$changeUrl}?status='+state, success: function (response) {}}); return true;}")
//                ],
//            ]);
//        },
//    ],

    [
        'attribute' => 'type_id',
        'filter' => [
            Constants::SHORT_LINK_REGULAR => Yii::t('app','Regular'),
            Constants::SHORT_LINK_WHATSAPP => Yii::t('app','WhatsApp'),
        ],
        'enableSorting' => false,
        'format' => 'raw',
        'value' => function ($model, $key, $index, $column) {
            /* @var $model \app\models\ShortLink */
            $types = [
                Constants::SHORT_LINK_REGULAR => Yii::t('app','Regular'),
                Constants::SHORT_LINK_WHATSAPP => Yii::t('app','WhatsApp'),
            ];

            return \yii\helpers\ArrayHelper::getValue($types,$model->type_id,Yii::t('app','Unknown'));
        },
    ],

    [
        'attribute' => 'key',
        'label' => Yii::t('app','Link'),
        'format' => 'raw',
        'enableSorting' => false,
        'value' => function($model, $key, $index, $column){
            /* @var $model \app\models\ShortLink*/
            return '<div class="input-group input-group-sm"><input id="copy-link-'.$model->id.'" class="form-control" readonly type="text" value="'.$model->GetLink(false).'"><span class="input-group-btn"><button title="'.Yii::t('app','Copy').'" type="button" data-clipboard-target="#copy-link-'.$model->id.'" class="btn btn-info btn-flat copy-text"><i class="fa fa-fw fa-clipboard"></i></button></span></div>';
        },
        'contentOptions' => ['style' => 'width:230px;'],
        'headerOptions' => ['style' => 'width:230px;']
    ],

    [
        'class' => 'yii\grid\ActionColumn',
        'contentOptions'=>['style'=>'width: 140px; text-align: center;'],
        'header' => Yii::t('app','Actions'),
        'template' => '{update} &nbsp; {delete} &nbsp; {info}',
        'buttons' => [
            'update' => function ($url,$model,$key) {
                /* @var $model \app\models\ShortLink */
                return Html::a('<span class="glyphicon glyphicon-pencil"></span>', Url::to(['/user/short-links/update', 'id' => $model->id]), ['title' => Yii::t('app','Edit'), 'data-target' => '.modal-main', 'data-toggle'=>'modal']);
            },
            'delete' => function ($url,$model,$key) {
                /* @var $model \app\models\ShortLink */
                return Html::a('<span class="glyphicon glyphicon-trash"></span>', Url::to(['/user/short-links/delete', 'id' => $model->id]), ['title' => Yii::t('app','Delete'), 'data-confirm' => Yii::t('app','Delete for eternity?')]);
            },
        ],
    ],
];

?>

<div class="row">
    <div class="col-xs-12">

        <?php if(!$user->isApprovedMember()): ?>
            <div class="alert alert-warning alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                <h4><i class="icon fa fa-warning"></i> <?= Yii::t('app','Alert!');?></h4>
                <?= Yii::t('app','Until you add at least one marketplace, your advertiser account will not be verified and you will not be able to create more than one shortened link.') ?>
            </div>
        <?php endif; ?>

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
            <?php if($user->getShortLinks()->count() == 0 || $user->isApprovedMember()): ?>
                <div class="box-footer">
                    <a href="<?php echo Url::to(['/user/short-links/create']); ?>" data-toggle="modal" data-target=".modal-main" class="btn btn-primary"><?= Yii::t('app','Create'); ?></a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function(){
        new Clipboard('.copy-text');
    });
</script>