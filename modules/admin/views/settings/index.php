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
                <li class="<?= ArrayHelper::getValue($_GET,'tab') == 'common' ? 'active' : ''; ?>"><a href="#tab_common" data-toggle="tab"><?= Yii::t('app','Common'); ?></a></li>
                <li class="pull-right"><a href="#" class="text-muted"><i class="fa fa-gear"></i></a></li>
            </ul>
            <div class="tab-content">
                <div class="tab-pane <?= empty($_GET['tab']) ? 'active' : ''; ?>" id="tab_facebook">
                    <?php $form = ActiveForm::begin([
                        'action' => Url::to(['/admin/settings/index']),
                        'id' => 'data-form-slide-create',
                        'options' => ['role' => 'form', 'method' => 'post', 'enctype' => 'multipart/form-data'],
                        'enableClientValidation' => false,
                        'fieldConfig' => [
                            'template' => "{label}\n{input}\n{error}\n",
                            //'labelOptions' => ['class' => 'col-lg-1 control-label'],
                        ],
                    ]); ?>

                    <div class="alert alert-info alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                        <h4><i class="icon fa fa-info"></i> <?= Yii::t('app','Important!'); ?></h4>
                        <?= Yii::t('app','To receive the token, please add the following URL to the list of allowed URLs in the settings of the Facebook application:'); ?>
                        <code><?= Url::to(['/admin/settings/refresh-token'],true); ?></code>
                    </div>

                    <?= $form->field($model, 'fb_auth_client_id')->textInput(); ?>
                    <?= $form->field($model, 'fb_auth_app_secret')->passwordInput(); ?>
                    <div class="row">
                        <div class="<?= !empty($fbLoginUrl) ? 'col-md-10' : 'col-md-12'; ?>">
                            <?= $form->field($model,'fb_app_admin_token')->textInput(); ?>
                        </div>
                        <?php if(!empty($fbLoginUrl)): ?>
                            <div class="col-md-2">
                                <a style="margin-top: 25px;" class="btn btn-primary btn-block" href="<?= $fbLoginUrl; ?>"><?= Yii::t('app','Refresh'); ?></a>
                            </div>
                        <?php endif; ?>
                    </div>

                    <hr>

                    <div class="alert alert-info alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                        <h4><i class="icon fa fa-info"></i> <?= Yii::t('app','Important!'); ?></h4>
                        <?= Yii::t('app','Please use following URL as web-hook messenger URL in your application settings:'); ?>
                        <code><?= Url::to(['/web-hook/fb-page-hook'],true); ?></code>
                    </div>

                    <?= $form->field($model, 'fb_messenger_hook_verify_token')->textInput(); ?>
                    <?= $form->field($model, 'fb_messenger_client_id')->textInput(); ?>
                    <?= $form->field($model, 'fb_messenger_app_secret')->passwordInput(); ?>

                    <div class="alert alert-info alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                        <h4><i class="icon fa fa-info"></i> <?= Yii::t('app','Important!'); ?></h4>
                        <?= Yii::t('app','Two pages of messenger are required - for notifications about stop words and for system notifications'); ?>
                    </div>

                    <?= $form->field($model, 'fb_messenger_page_monitoring_id')->textInput(); ?>
                    <?= $form->field($model, 'fb_messenger_page_monitoring_token')->passwordInput(); ?>
                    <?= $form->field($model, 'fb_messenger_page_notifications_id')->textInput(); ?>
                    <?= $form->field($model, 'fb_messenger_page_notifications_token')->passwordInput(); ?>

                    <button type="submit" class="btn btn-primary"><?= Yii::t('app','Save'); ?></button>

                    <?php ActiveForm::end(); ?>
                </div>

                <div class="tab-pane <?= ArrayHelper::getValue($_GET,'tab') == 'email' ? 'active' : ''; ?>" id="tab_email">
                    <?php $form = ActiveForm::begin([
                        'action' => Url::to(['/admin/settings/index', 'tab' => 'email']),
                        'id' => 'data-form-slide-create',
                        'options' => ['role' => 'form', 'method' => 'post', 'enctype' => 'multipart/form-data'],
                        'enableClientValidation' => false,
                        'fieldConfig' => [
                            'template' => "{label}\n{input}\n{error}\n",
                            //'labelOptions' => ['class' => 'col-lg-1 control-label'],
                        ],
                    ]); ?>

                    <div class="alert alert-info alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                        <h4><i class="icon fa fa-info"></i> <?= Yii::t('app','Important!'); ?></h4>
                        <?= Yii::t('app','If you want to use SMTP, your sending email must match your SMTP login'); ?>
                    </div>

                    <?= $form->field($model, 'email_for_sending')->textInput(); ?>
                    <?= $form->field($model, 'email_for_notifications')->textInput(); ?>

                    <hr>
                    <?= $form->field($model, 'smtp_enabled')->checkbox(); ?>
                    <?= $form->field($model, 'smtp_host')->textInput(); ?>
                    <?= $form->field($model, 'smtp_login')->textInput(); ?>
                    <?= $form->field($model, 'smtp_password')->passwordInput(); ?>
                    <?= $form->field($model, 'smtp_port')->textInput(); ?>
                    <?= $form->field($model, 'smtp_encryption')->dropDownList([
                        'ssl' => 'SSL',
                        'tls' => 'TLS'
                    ]); ?>

                    <button type="submit" class="btn btn-primary"><?= Yii::t('app','Save'); ?></button>

                    <?php ActiveForm::end(); ?>
                </div>

                <div class="tab-pane <?= ArrayHelper::getValue($_GET,'tab') == 'common' ? 'active' : ''; ?>" id="tab_common">
                    <?php $form = ActiveForm::begin([
                        'action' => Url::to(['/admin/settings/index', 'tab' => 'common']),
                        'id' => 'data-form-slide-create',
                        'options' => ['role' => 'form', 'method' => 'post', 'enctype' => 'multipart/form-data'],
                        'enableClientValidation' => false,
                        'fieldConfig' => [
                            'template' => "{label}\n{input}\n{error}\n",
                            //'labelOptions' => ['class' => 'col-lg-1 control-label'],
                        ],
                    ]); ?>

                    <?php $model->payout_min_sum = Help::toPrice($model->payout_min_sum); ?>
                    <?= $form->field($model,'payout_min_sum')->textInput(); ?>

                    <button type="submit" class="btn btn-primary"><?= Yii::t('app','Save'); ?></button>

                    <?php ActiveForm::end(); ?>
                </div>
            </div>
        </div>
    </div>
</div>