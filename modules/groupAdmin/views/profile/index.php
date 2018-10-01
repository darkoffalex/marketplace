<?php
use app\helpers\Constants;
use app\helpers\Help;
use yii\bootstrap\ActiveForm;

/* @var $this \yii\web\View */
/* @var $controller \app\modules\groupAdmin\controllers\ProfileController */
/* @var $user \app\models\User */
/* @var $model \app\models\forms\ProfileForm */

$controller = $this->context;
$user = Yii::$app->user->identity;

$this->title = Yii::t('app','My profile');
$this->params['breadcrumbs'][] = $this->title;

$this->registerJsFile('@web/common/js/clipboard.js');

$roles = [];
if($user->isApprovedGroupAdmin()){
    $roles[] = Yii::t('app','Group admin');
}
if($user->isApprovedMember()){
    $roles[] = Yii::t('app','Advertiser');
}
?>

<div class="row">
    <div class="col-md-3">
        <!-- Profile Image -->
        <div class="box box-primary">
            <div class="box-body box-profile">
                <img class="profile-user-img img-responsive img-circle" src="<?= $user->getAvatar(100,100); ?>" alt="User profile picture">

                <h3 class="profile-username text-center"><?= $user->name; ?></h3>

                <p class="text-muted text-center">
                    <?php if(!empty($roles)): ?>
                        <?= implode(', ',$roles); ?>
                    <?php else: ?>
                        <?= Yii::t('app','New member'); ?>
                    <?php endif; ?>
                </p>

                <ul class="list-group list-group-unbordered">
                    <li class="list-group-item">
                        <b><?= Yii::t('app','Manager account balance') ?></b> <a class="pull-right"><?= Help::toPrice($user->getMoneyAccount(Constants::GROUP_ADMIN_ACCOUNT)->amount).' ₽'; ?></a>
                    </li>
                    <li class="list-group-item">
                        <b><?= Yii::t('app','Owned marketplaces') ?></b> <a class="pull-right"><?= $user->getMarketplaces()->count(); ?></a>
                    </li>
                    <li class="list-group-item">
                        <b><?= Yii::t('app','Bind marketplaces') ?></b> <a class="pull-right"><?= $user->getMarketplaceKeys()->count(); ?></a>
                    </li>
                    <li class="list-group-item">
                        <b><?= Yii::t('app','Total advertisements') ?></b> <a class="pull-right"><?= $user->getPosters()->count(); ?></a>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <div class="col-md-9">
        <div class="box box-primary">
            <div class="box-body">

                <div class="alert alert-info alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    <h4><i class="icon fa fa-info"></i> <?= Yii::t('app','How to subscribe ?'); ?></h4>
                    <?= Yii::t('app','Copy the dictionary unique key, and go to the facebook {page}. Then send message to page with command <code>SUB {copied key}</code>',['page' => '<a target="_blank" href="https://www.facebook.com/297161740892390/">mg page</a>']); ?>
                </div>

                <div class="form-group">
                    <label class="control-label" for="copy-link"><?= Yii::t('app','Notification subscription code'); ?></label>
                    <div class="input-group">
                        <input placeholder="" id="copy-link" class="form-control" readonly="readonly" value="<?= $user->fb_msg_sub_code; ?>" type="text">
                        <span class="input-group-btn"><button title="Copy" type="button" data-clipboard-target="#copy-link" class="btn btn-info btn-flat copy-text"><i class="fa fa-fw fa-clipboard"></i></button></span>
                    </div>
                </div>
                <p>
                    <strong><?= Yii::t('app','Subscribed'); ?>:</strong>
                    <?= !empty($user->fb_msg_uid) ? '<span class="label label-success">'.Yii::t('app','Yes').'</span>' : '<span class="label label-danger">'.Yii::t('app','No').'</span>'; ?>
                </p>
                <?php $form = ActiveForm::begin([
                    'id' => 'edit-marketplace-form',
                    'options' => ['role' => 'form', 'method' => 'post', 'enctype' => 'multipart/form-data'],
                    'enableClientValidation'=>false,
                    'fieldConfig' => [
                        'template' => "{label}\n{input}\n{error}\n",
                        //'labelOptions' => ['class' => 'col-lg-1 control-label'],
                    ],
                ]); ?>
                <?= $form->field($model,'fb_notification_types')->checkboxList([
                    Constants::NOTIFY_NEW_ADVERTISEMENTS => Yii::t('app','New advertisements'),
                    Constants::NOTIFY_MARKETPLACE_CONFIRMATION => Yii::t('app','Marketplace confirmation'),
                    Constants::NOTIFY_ADVERTISEMENTS_CONFIRMATION => Yii::t('app','Advertisement confirmation'),
                    Constants::NOTIFY_PAYOUTS_CONFIRMATION => Yii::t('app','Payouts confirmation')
                ]); ?>
                <hr>
                <?= $form->field($model,'email')->textInput(); ?>
                <?= $form->field($model,'email_notifications_enabled')->checkbox(); ?>
                <?= $form->field($model,'email_notification_types')->checkboxList([
                    Constants::NOTIFY_NEW_ADVERTISEMENTS => Yii::t('app','New advertisements'),
                    Constants::NOTIFY_MARKETPLACE_CONFIRMATION => Yii::t('app','Marketplace confirmation'),
                    Constants::NOTIFY_ADVERTISEMENTS_CONFIRMATION => Yii::t('app','Advertisement confirmation'),
                    Constants::NOTIFY_PAYOUTS_CONFIRMATION => Yii::t('app','Payouts confirmation')
                ]); ?>
                <hr>
                <?= $form->field($model,'name')->textInput(); ?>
                <button type="submit" class="btn btn-primary"><?= Yii::t('app','Save'); ?></button>
                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function(){
        new Clipboard('.copy-text');
    });
</script>
