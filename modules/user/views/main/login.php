<?php
use yii\helpers\Url;
use Facebook\Facebook;
use app\models\forms\SettingsForm;

/* @var $this \yii\web\View */
/* @var $controller \app\modules\user\controllers\MainController */
/* @var $user \app\models\User */

$controller = $this->context;
$user = Yii::$app->user->identity;

$this->title = Yii::t('app','Sign in as member-user');
$this->params['breadcrumbs'][] = $this->title;

if (!session_id()) {
    session_start();
}

if(!empty(SettingsForm::getInstance()->fb_auth_client_id) && !empty(SettingsForm::getInstance()->fb_auth_app_secret)){
    $fb = new Facebook([
        'app_id' => SettingsForm::getInstance()->fb_auth_client_id,
        'app_secret' => SettingsForm::getInstance()->fb_auth_app_secret,
    ]);
    $fbLoginUrl = $fb->getRedirectLoginHelper()->getLoginUrl(Url::to(['/main/auth-fb-user', 'language' => null],'https'), ['email']);
}else{
    $fbLoginUrl = null;
}
?>

<div class="login-box">
    <div class="login-logo">
        <a href="/">
            <b>Marketplace.Guide</b>
            <br>
            <?= Yii::t('app','User Account Panel'); ?>
        </a>
    </div>
    <div class="login-box-body">
        <p class="login-box-msg"><?= Yii::t('app','Please authorize with facebook'); ?></p>
        <div class="social-auth-links text-center">
            <?php if(empty($fbLoginUrl)): ?>
                <div class="alert alert-danger alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    <h4><i class="icon fa fa-ban"></i> <?= Yii::t('app','Alert!'); ?></h4>
                    <?= Yii::t('app','Seems like facebook application is not configured. Please go to admin-panel, settings section, and provide correct auth application settings.') ?>
                </div>
            <?php endif; ?>
            <a href="<?= $fbLoginUrl; ?>" class="btn btn-block btn-social btn-facebook btn-flat"><i class="fa fa-facebook"></i> <?= Yii::t('app','Sign in using Facebook'); ?></a>
        </div>
        <a href="<?= Url::to(['/group-admin/main/login']); ?>"><?= Yii::t('app','Sign in as group admin'); ?></a><br>
    </div>
</div>