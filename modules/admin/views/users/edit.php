<?php
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use app\models\User;
use app\helpers\Constants;
use branchonline\lightbox\LightboxAsset;
use app\helpers\Help;

/* @var $model User */
/* @var $this \yii\web\View */
/* @var $user User */

$this->registerAssetBundle(LightboxAsset::class);

$user = Yii::$app->user->identity;

$this->title = $model->isNewRecord ? Yii::t('app','Create new user') : Yii::t('app','Edit user');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app','Users'), 'url' => Url::to(['/admin/users/index'])];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row">
    <div class="col-md-12">

        <div class="box box-primary">
            <div class="box-header with-border"><h3 class="box-title"><?= Yii::t('app','Main user settings'); ?></h3></div>

            <?php $form = ActiveForm::begin([
                'id' => 'edit-users-form',
                'options' => ['role' => 'form', 'method' => 'post', 'enctype' => 'multipart/form-data'],
                'enableClientValidation'=>false,
                'fieldConfig' => [
                    'template' => "{label}\n{input}\n{error}\n",
                    //'labelOptions' => ['class' => 'col-lg-1 control-label'],
                ],
            ]); ?>


            <div class="box-body">
                <?php if(!$model->hasErrors() && Yii::$app->request->isPost): ?>
                    <div class="alert alert-success alert-dismissable">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                        <h4><i class="icon fa fa-check"></i><?= Yii::t('app','Saved'); ?></h4>
                        <?= Yii::t('app','Changes saved'); ?>
                    </div>
                <?php endif; ?>

                <?= $form->field($model, 'username')->textInput(); ?>

                <?= $form->field($model, 'password')->passwordInput(); ?>

                <?= $form->field($model, 'role_id')->dropDownList([
                    Constants::ROLE_ADMIN => Constants::GetRoleName(Constants::ROLE_ADMIN),
                    Constants::ROLE_BOOKKEEPER => Constants::GetRoleName(Constants::ROLE_BOOKKEEPER),
                    Constants::ROLE_ADMIN => Constants::GetRoleName(Constants::ROLE_ADMIN),
                ]); ?>

                <?= $form->field($model, 'status_id')->dropDownList([
                    Constants::USR_STATUS_ENABLED => Constants::GetStatusName(Constants::USR_STATUS_ENABLED),
                    Constants::USR_STATUS_DISABLED => Constants::GetStatusName(Constants::USR_STATUS_DISABLED),
                ]); ?>
                <hr>

                <?= $form->field($model, 'name')->textInput(); ?>

                <?= $form->field($model,'email')->textInput(); ?>
            </div>

            <div class="box-footer">
                <a class="btn btn-primary" href="<?= Url::to(['/admin/users/index']); ?>"><?= Yii::t('app','Back to list'); ?></a>
                <button type="submit" class="btn btn-primary"><?= Yii::t('app','Save'); ?></button>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>

    <div class="col-md-12">
        <div class="box box-primary">
            <div class="box-header with-border"><h3 class="box-title"><?= Yii::t('app','Group admin settings'); ?></h3></div>
            <div class="box-body">
                <?php if($model->isApprovedGroupAdmin()): ?>
                    <p><strong><?= Yii::t('app','Balance'); ?>:</strong> <?= Help::toPrice($model->getMoneyAccount(Constants::GROUP_ADMIN_ACCOUNT)->amount).' ₽'; ?></p>
                    <p><strong><?= Yii::t('app','Undone payouts sum'); ?>:</strong> <?= $model->getUndonePayoutsSum(true).' ₽'; ?></p>
                    <p><strong><?= Yii::t('app','Total income'); ?>:</strong> <?= $model->getGroupAdminIncome(true).' ₽'; ?></p>
                    <p><strong><?= Yii::t('app','Average day income'); ?>:</strong> <?= $model->getGroupAdminDayIncome(true).' ₽'; ?></p>
                    <p><strong><?= Yii::t('app','Paid advertisers'); ?>:</strong> <?= $model->getPaidAdvertisersCount(); ?></p>
                    <hr>
                    <?php $form = ActiveForm::begin([
                        'id' => 'edit-users-ga-form',
                        'options' => ['role' => 'form', 'method' => 'post', 'enctype' => 'multipart/form-data'],
                        'enableClientValidation'=>false,
                        'fieldConfig' => [
                            'template' => "{label}\n{input}\n{error}\n",
                            //'labelOptions' => ['class' => 'col-lg-1 control-label'],
                        ],
                    ]); ?>
                    <?= $form->field($model, 'ag_income_percentage')->textInput(); ?>
                    <button type="submit" class="btn btn-primary"><?= Yii::t('app','Save'); ?></button>
                    <?php ActiveForm::end(); ?>
                <?php else: ?>
                    <div class="alert alert-warning alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                        <h4><i class="icon fa fa-info"></i><?= Yii::t('app','Information'); ?></h4>
                        <?= Yii::t('app','User has hot approved his group admin status. Information unavailable'); ?>
                    </div>
                <?php endif;?>
            </div>
        </div>
    </div>

    <div class="col-md-12">
        <div class="box box-primary">
            <div class="box-header with-border"><h3 class="box-title"><?= Yii::t('app','Advertiser settings'); ?></h3></div>
            <div class="box-body">
                <?php if($model->isApprovedMember()): ?>
                    <p><strong><?= Yii::t('app','Total spent'); ?>:</strong> <?= Help::toPrice(abs($model->getMoneyAccount(Constants::MEMBER_ACCOUNT)->amount)).' ₽'; ?></p>
                    <p><strong><?= Yii::t('app','Total advertisements'); ?>:</strong> <?= $model->getPosters()->count(); ?></p>
                    <p><strong><?= Yii::t('app','Paid advertisements'); ?>:</strong> <?= $model->getPosters()->andWhere('paid_at IS NOT NULL')->count(); ?></p>
                <?php else: ?>
                    <div class="alert alert-warning alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                        <h4><i class="icon fa fa-info"></i><?= Yii::t('app','Information'); ?></h4>
                        <?= Yii::t('app','User has hot approved his advertiser status. Information unavailable'); ?>
                    </div>
                <?php endif;?>
            </div>
        </div>
    </div>
</div>