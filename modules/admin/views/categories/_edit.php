<?php
use yii\bootstrap\ActiveForm;
use app\helpers\Constants;
use yii\helpers\ArrayHelper;
use app\models\Category;
use app\helpers\Trl;

/* @var $model \app\models\Category */
/* @var $this \yii\web\View */
/* @var $controller \app\modules\admin\controllers\CategoriesController */

$controller = $this->context;
?>

    <div class="modal-header">
        <h4 class="modal-title"><?= $model->isNewRecord ? Yii::t('app','Create category') : Yii::t('app','Edit category'); ?></h4>
    </div>

<?php $form = ActiveForm::begin([
    'id' => 'create-category-form',
    'options' => ['role' => 'form', 'method' => 'post', 'enctype' => 'multipart/form-data'],
    'enableClientValidation'=>true,
    'fieldConfig' => [
        'template' => "{label}\n{input}\n{error}\n",
        //'labelOptions' => ['class' => 'col-lg-1 control-label'],
    ],
]); ?>

    <div class="modal-body">
        <?= $form->field($model, 'name')->textInput(); ?>

        <?= $form->field($model, 'status_id')->dropDownList([
            Constants::STATUS_ENABLED => Yii::t('app','Enabled'),
            Constants::STATUS_DISABLED => Yii::t('app','Disabled'),
        ]); ?>

        <?php $arr = ArrayHelper::map(Category::getRecursiveCats(),'id',function($current,$defaultValue){
            /* @var $current Category */
            $result = "";
            for($i=0;$i<$current->getDepth();$i++){$result.= "-";}
            $result.= $current->name;
            return $result;
        }); ?>
        <?php if(!$model->isNewRecord):?>
            <?php ArrayHelper::remove($arr,$model->id); ?>
        <?php endif; ?>

        <?= $form->field($model, 'parent_category_id')->dropDownList([0 => Yii::t('app','[NONE]')] + $arr); ?>
    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-default pull-left" data-dismiss="modal"><?= Yii::t('app','Close'); ?></button>
        <button type="submit" class="btn btn-primary"><?= Yii::t('app','Save') ?></button>
    </div>

<?php ActiveForm::end(); ?>