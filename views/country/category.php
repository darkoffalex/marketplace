<?php
/* @var $this yii\web\View */
/* @var $controller \app\controllers\MainController */
/* @var $user \yii\web\User */
/* @var $country \app\models\Country */
/* @var $category \app\models\Category */

$controller = $this->context;
$user = Yii::$app->user->identity;

$controller->title = Yii::$app->name.' | '.Yii::t('app',$country->name);
$this->title = Yii::t('app',$category->name);
$this->params['breadcrumbs'] = $category->getBreadCrumbs($country);
?>

<div class="row">
    <div class="col-md-12">
        <?php if(!empty($category->childrenActive)): ?>
            <p>
                <?php foreach ($category->childrenActive as $index => $childCategory): ?>
                    <?php if($index != 0): ?>|<?php endif; ?>
                    <a href="<?= $childCategory->getUrl($country->domain_alias); ?>"><?= $childCategory->name; ?></a>
                <?php endforeach; ?>
            </p>
        <?php endif; ?>

        <div class="box">
            <div class="box-body">
            </div>
        </div>
    </div>
</div>
