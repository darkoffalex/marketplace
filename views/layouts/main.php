<?php

/* @var $this \yii\web\View */
/* @var $content string */
/* @var $user \app\models\User */

use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use app\assets\FrontendAsset;

FrontendAsset::register($this);

$user = Yii::$app->user->identity;
?>

<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>


<?php
$c = Yii::$app->controller->id;
$a = Yii::$app->controller->action->id;
NavBar::begin(['brandLabel' => Yii::$app->name]);
echo Nav::widget([
    'items' =>
    [
        [
            'label' => 'Главная',
            'url' => ['/main/index', 'language' => Yii::$app->language],
            'active' => $c == 'main' && $a == 'index',
        ],
    ],
    'options' => ['class' => 'navbar-nav'],
]);
NavBar::end();
?>


<div class="wrap">
    <div class="container">
        <?= Breadcrumbs::widget(['links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : []]); ?>
        <?= $content ?>
    </div>
</div>

<footer class="footer">
    <div class="container">
        <p class="pull-left">&copy; My Company <?= date('Y') ?></p>

        <p class="pull-right"><?= Yii::powered() ?></p>
    </div>
</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
