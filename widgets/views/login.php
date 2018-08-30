<?php
use yii\helpers\Url;

/* @var $this \yii\web\View */
/* @var $widget \app\widgets\MessagesWidget */
/* @var $user \app\models\User */
/* @var $settings \app\models\forms\SettingsForm */
?>

<li class="dropdown user user-menu">
    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
        <?php if(empty($user)): ?>
            <img src="<?= Url::to('@web/frontend/img/profile_128.png'); ?>" class="user-image" alt="User Image">
            <span class="hidden-xs"><?= Yii::t('app','Guest'); ?></span>
        <?php else: ?>
            <img src="<?= $user->getAvatar(); ?>" class="user-image" alt="User Image">
            <span class="hidden-xs"><?= $user->name; ?></span>
        <?php endif; ?>
    </a>
    <ul class="dropdown-menu">
        <li class="user-header">
            <?php if(empty($user)): ?>
                <img src="<?= Url::to('@web/frontend/img/profile_128.png'); ?>" class="img-circle" alt="User Image">
                <p><?= Yii::t('app','Guest'); ?></p>
            <?php else: ?>
                <img src="<?= $user->getAvatar(); ?>" class="img-circle" alt="User Image">
                <p><?= $user->name; ?></p>
            <?php endif; ?>
        </li>
        <li class="user-footer">
            <div class="pull-left">
                <a href="<?= Url::to(['/group-admin/main/index']); ?>" class="btn btn-default btn-flat"><?= Yii::t('app','Admin'); ?></a>
            </div>
            <div class="pull-left">
                <a href="#" class="btn btn-default btn-flat"><?= Yii::t('app','Member'); ?></a>
            </div>
            <?php if(!empty($user)): ?>
                <div class="pull-right">
                    <a href="<?= Url::to(['/main/logout']); ?>" class="btn btn-default btn-flat"><?= Yii::t('app','Logout'); ?></a>
                </div>
            <?php endif; ?>
        </li>
    </ul>
</li>
