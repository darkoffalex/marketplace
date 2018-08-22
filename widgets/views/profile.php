<?php
use yii\helpers\Url;
use Facebook\Facebook;

/* @var $this \yii\web\View */
/* @var $widget \app\widgets\MessagesWidget */
/* @var $user \app\models\User */
/* @var $settings \app\models\forms\SettingsForm */

if (!session_id()) {
    session_start();
}

$widget = $this->context;

if(!empty($settings->fb_auth_client_id) && !empty($settings->fb_auth_app_secret)){
    $fb = new Facebook([
        'app_id' => $settings->fb_auth_client_id,
        'app_secret' => $settings->fb_auth_app_secret,
    ]);
    $fbLoginUrl = $fb->getRedirectLoginHelper()->getLoginUrl(Url::to(['/main/auth-fb'],true), ['email']);
}else{
    $fbLoginUrl = null;
}
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
            <?php if(!empty($user)): ?>
                <div class="pull-left">
                    <a href="#" class="btn btn-default btn-flat"><?= Yii::t('app','My profile'); ?></a>
                </div>
                <div class="pull-right">
                    <a href="<?= Url::to(['/main/logout']); ?>" class="btn btn-default btn-flat"><?= Yii::t('app','Logout'); ?></a>
                </div>
            <?php else: ?>
                <div>
                    <a class="btn btn-block btn-social btn-facebook" href="<?= $fbLoginUrl; ?>">
                        <i class="fa fa-facebook"></i> <?= Yii::t('app','Sign in with Facebook'); ?>
                    </a>
                </div>
            <?php endif; ?>
        </li>
    </ul>
</li>
