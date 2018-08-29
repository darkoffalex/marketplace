<?php
/* @var $this yii\web\View */
/* @var $controller \app\controllers\MainController */
/* @var $user \yii\web\User */
/* @var $country \app\models\Country */
/* @var $categories \app\models\Category[] */

$controller = $this->context;
$user = Yii::$app->user->identity;

$controller->title = Yii::$app->name.' | '.Yii::t('app',$country->name);
$this->title = Yii::t('app',$country->name);
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row">
    <div class="col-md-12">
        <?php if(!empty($categories)): ?>
            <p>
                <?php foreach ($categories as $index => $category): ?>
                    <?php if($index != 0): ?>|<?php endif; ?>
                    <a href="<?= $category->getUrl($country->domain_alias); ?>"><?= $category->name; ?></a>
                <?php endforeach; ?>
            </p>
        <?php endif; ?>

        <div class="box">
            <div class="box-body">
                ЗДЕСЬ БУДУТ ОБЪЯВЛЕНИЯ
            </div>
        </div>
    </div>
</div>
