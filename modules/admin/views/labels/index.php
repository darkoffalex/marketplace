<?php

use kartik\grid\GridView;
use yii\bootstrap\Html;
use yii\helpers\Url;
use app\modules\admin\helpers\Access;

/* @var $searchModel \app\models\MessageSearch */
/* @var $dataProvider \yii\data\ActiveDataProvider */
/* @var $this \yii\web\View */
/* @var $controller \app\modules\admin\controllers\LabelsController */
/* @var $user \app\models\User */

$controller = $this->context;
$user = Yii::$app->user->identity;

$this->title = Yii::t('app','Labels list');
$this->params['breadcrumbs'][] = $this->title;

$gridColumns = [
    ['class' => 'yii\grid\SerialColumn'],
    ['attribute' => 'message'],
];

/* @var $languages \app\models\Language[] */
$languages = \app\models\Language::find()->all();

foreach($languages as $lng){
    $gridColumns[] = [
        'attribute' => "translated_{$lng->prefix}",
        'label' => $lng->self_name.' ('.$lng->prefix.')',
        'format' => 'raw',
        'value' => function ($model, $key, $index, $column) use ($lng){
            /* @var $model \app\models\MessageSearch */
            $value = $model->getTranslatedText($lng->prefix);
            $updateUrl = Url::to(['/admin/labels/list-update', 'id' => $model->id, 'lng' => $lng->prefix]);
            $result = "<div class='form-group'>";
            $result.= "<textarea style='resize: none' class='form-control' id='trl_{$model->id}_{$lng->prefix}'>{$value}</textarea>";
            $result.= "<button class='btn btn-primary btn-xs btn-block' data-update data-update-field='#trl_{$model->id}_{$lng->prefix}' data-update-url='{$updateUrl}' type='button'>".Yii::t('app','Save')."</button>";
            $result.= "</div>";
            return $result;
        },
    ];
}

$gridColumns[] = [
    'class' => 'yii\grid\ActionColumn',
    'contentOptions'=>['style'=>'width: 100px; text-align: center;'],
    'header' => Yii::t('app','Actions'),
    'template' => '{delete} &nbsp; {update}',
    'buttons' => [
        'update' => function ($url,$model,$key) {
            /* @var $model \app\models\MessageSearch */
            return Html::a('<span class="glyphicon glyphicon-pencil"></span>', Url::to(['/admin/labels/update', 'id' => $model->id]), ['data-toggle'=>'modal', 'data-target'=>'.modal-main', 'title' => Yii::t('app','Edit')]);
        },
    ],
    'visibleButtons' => [
        'delete' => function ($model, $key, $index) use ($user) {/* @var $model \app\models\User */ return Access::has($user,'labels','delete');},
        'update' => function ($model, $key, $index) use ($user) {/* @var $model \app\models\User */ return Access::has($user,'labels','update');},
    ],
];

?>

<div class="row">
    <div class="col-xs-12">
        <div class="box">
            <div class="box-header">
                <h3 class="box-title"><?= Yii::t('app','List'); ?></h3>
            </div>
            <div class="box-body">
                <?= GridView::widget([
                    'filterModel' => $searchModel,
                    'dataProvider' => $dataProvider,
                    'columns' => $gridColumns,
                    'pjax' => false,
                ]); ?>
            </div>
            <?php if(Access::has($user,'labels','create')): ?>
                <div class="box-footer">
                    <a data-target=".modal-main" data-toggle="modal" href="<?php echo Url::to(['/admin/labels/create']); ?>" class="btn btn-primary"><?= Yii::t('app','Create'); ?></a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
       $('[data-update]').click(function () {

           var url = $(this).data('update-url');
           var value = $($(this).data('update-field')).val();

           if(url){
               $.ajax({
                   async: false,
                   url : url,
                   type: "POST",
                   data: {translation:value},
                   success: function (response) {
                       if(response === 'OK'){
                           alert('<?= Yii::t('app','Changes successfully accepted'); ?>');
                       }
                   },
                   error: function () {
                       alert('<?= Yii::t('app','Internal server error. Cannot update'); ?>');
                   }
               });
           }
       }) ;
    });
</script>
