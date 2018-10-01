<?php
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use Facebook\Facebook;
use app\helpers\Help;
use yii\helpers\ArrayHelper;

/* @var $model \app\models\forms\SettingsForm */
/* @var $this \yii\web\View */
/* @var $controller \app\modules\admin\controllers\SettingsController */

$controller = $this->context;

$this->title = Yii::t('app','Settings');
$this->params['breadcrumbs'][] = $this->title;

if(!empty($model->fb_auth_client_id) && !empty($model->fb_auth_client_id)){
    $fb = new Facebook([
        'app_id' => $model->fb_auth_client_id,
        'app_secret' => $model->fb_auth_client_id,
    ]);
    $fbLoginUrl = $fb->getRedirectLoginHelper()->getLoginUrl(Url::to(['/admin/settings/refresh-token'],true), ['email']);
}else{
    $fbLoginUrl = null;
}

?>

<div class="row">
    <div class="col-md-12">
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
                <li class="<?= empty($_GET['tab']) ? 'active' : ''; ?>"><a href="#tab_facebook" data-toggle="tab"><?= Yii::t('app','Facebook') ?></a></li>
                <li class="<?= ArrayHelper::getValue($_GET,'tab') == 'email' ? 'active' : ''; ?>"><a href="#tab_email" data-toggle="tab"><?= Yii::t('app','Email') ?></a></li>
                <li class="<?= ArrayHelper::getValue($_GET,'tab') == 'notifications' ? 'active' : ''; ?>"><a href="#tab_notifications" data-toggle="tab"><?= Yii::t('app','Notifications'); ?></a></li>
                <li class="<?= ArrayHelper::getValue($_GET,'tab') == 'common' ? 'active' : ''; ?>"><a href="#tab_common" data-toggle="tab"><?= Yii::t('app','Common'); ?></a></li>
                <li class="pull-right"><a href="#" class="text-muted"><i class="fa fa-gear"></i></a></li>
            </ul>
            <div class="tab-content">
                <div class="tab-pane <?= empty($_GET['tab']) ? 'active' : ''; ?>" id="tab_facebook">
                    <?= $this->render('_facebook',['model' => $model]); ?>
                </div>

                <div class="tab-pane <?= ArrayHelper::getValue($_GET,'tab') == 'email' ? 'active' : ''; ?>" id="tab_email">
                    <?= $this->render('_email',['model' => $model]); ?>
                </div>

                <div class="tab-pane <?= ArrayHelper::getValue($_GET,'tab') == 'notifications' ? 'active' : ''; ?>" id="tab_notifications">
                    <?= $this->render('_notifications',['model' => $model]); ?>
                </div>

                <div class="tab-pane <?= ArrayHelper::getValue($_GET,'tab') == 'common' ? 'active' : ''; ?>" id="tab_common">
                    <?= $this->render('_common',['model' => $model]); ?>
                </div>
            </div>
        </div>
    </div>
</div>