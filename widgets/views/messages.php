<?php
use yii\helpers\Url;

/* @var $this \yii\web\View */
/* @var $widget \app\widgets\MessagesWidget */

/* @var $title string */
/* @var $messages array */
/* @var $count int */

$widget = $this->context;
?>

<li class="dropdown messages-menu">
    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
        <i class="fa fa-envelope-o"></i>

        <?php if($count > 0): ?>
            <span class="label label-success"><?= $count; ?></span>
        <?php endif; ?>
    </a>
    <ul class="dropdown-menu">
        <li class="header"><?= $title; ?></li>
        <li>
            <ul class="menu">
                <li>
                    <a href="#">
                        <div class="pull-left">
                            <img src="<?= Url::to('@web/frontend/img/profile_128.png') ?>" class="img-circle" alt="User Image">
                        </div>
                        <h4>
                            Support Team
                            <small><i class="fa fa-clock-o"></i> 5 mins</small>
                        </h4>
                        <p>Why not buy a new awesome theme?</p>
                    </a>
                </li>
            </ul>
        </li>
        <li class="footer"><a href="#"><?= Yii::t('app','See all'); ?></a></li>
    </ul>
</li>
