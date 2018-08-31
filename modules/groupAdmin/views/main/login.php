<?php
use yii\helpers\Url;
use Facebook\Facebook;
use app\models\forms\SettingsForm;

/* @var $this \yii\web\View */
/* @var $controller \app\modules\admin\controllers\MainController */
/* @var $user \app\models\User */

$controller = $this->context;
$user = Yii::$app->user->identity;

$this->title = Yii::t('app','Sign in as group admin');
$this->params['breadcrumbs'][] = $this->title;

if (!session_id()) {
    session_start();
}

$fb = new Facebook([
    'app_id' => SettingsForm::getInstance()->fb_auth_client_id,
    'app_secret' => SettingsForm::getInstance()->fb_auth_app_secret,
]);

$fbLoginUrl = $fb->getRedirectLoginHelper()->getLoginUrl(Url::to(['/main/auth-fb-group-admin', 'language' => null],true), ['email']);
?>

<div class="login-box">
    <div class="login-logo">
        <a href="/">
            <b>Marketplace.Guide</b>
            <br>
            <?= Yii::t('app','Group Admin Panel'); ?>
        </a>
    </div>
    <div class="login-box-body">
        <p class="login-box-msg"><?= Yii::t('app','Please authorize with facebook'); ?></p>
        <div class="social-auth-links text-center">
            <a href="<?= $fbLoginUrl; ?>" class="btn btn-block btn-social btn-facebook btn-flat"><i class="fa fa-facebook"></i> <?= Yii::t('app','Sign in using Facebook'); ?></a>
        </div>
        <a href="#"><?= Yii::t('app','Sign in as user'); ?></a><br>
    </div>
</div>