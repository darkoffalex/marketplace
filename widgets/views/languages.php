<?php
/* @var $this \yii\web\View */
/* @var $widget \app\widgets\LanguageSwitchWidget */
/* @var $languages \app\models\Language[] */

$widget = $this->context;
?>

<li class="dropdown messages-menu">
    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
        <i class="fa fa-globe"></i>
        <span class="label label-success"><?= Yii::$app->language; ?></span>
    </a>
    <ul class="dropdown-menu" style="width: 170px;">
        <li class="header"><?= Yii::t('app','Language switch'); ?></li>
        <li>
            <ul class="menu">
                <?php foreach ($languages as $language): ?>
                    <li>
                        <a href="<?= $language->getCurrentLink(); ?>"><?= $language->prefix; ?></a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </li>
    </ul>
</li>
