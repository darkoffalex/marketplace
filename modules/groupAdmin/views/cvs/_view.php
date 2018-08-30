<?php
use app\helpers\Constants;
use yii\helpers\Url;

/* @var $model \app\models\Cv*/
/* @var $this \yii\web\View */
/* @var $controller \app\modules\admin\controllers\CvsController */
/* @var $flags array[] */

$this->registerJsFile(Url::to('@web/common/js/show-hide.js'));
$controller = $this->context;
?>

<div class="modal-header">
    <h4 class="modal-title"><?= Yii::t('app','View CV'); ?></h4>
</div>


<div class="modal-body">
    <p><strong><?= Yii::t('app','Group\'s name'); ?></strong> : <span><?= $model->group_name; ?></span></p>
    <p><strong><?= Yii::t('app','Group\'s URL'); ?></strong> : <span><?= $model->group_url; ?></span></p>
    <p><strong><?= Yii::t('app','Group admin\'s profile URL'); ?></strong> : <span><?= $model->group_admin_profile; ?></span></p>
    <p><strong><?= Yii::t('app','Group\'s thematics'); ?></strong> : <span><?= $model->group_thematics; ?></span></p>
    <p><strong><?= Yii::t('app','Group\'s popularity'); ?></strong> : <span><?= $model->group_popularity; ?></span></p>
    <p><strong><?= Yii::t('app','Group\'s description'); ?></strong> : <span><?= $model->group_description; ?></span></p>
    <p><strong><?= Yii::t('app','Group\'s geo-orientation'); ?></strong> : <span><?= $model->group_geo; ?></span></p>

    <hr>

    <p><strong><?= Yii::t('app','Name and surname'); ?></strong> : <span><?= $model->name; ?></span></p>
    <p><strong><?= Yii::t('app','Member of "Admin.School"'); ?></strong> : <span><?= Yii::t('app',$model->is_member ? 'Yes' : 'No'); ?></span></p>
    <p><strong><?= Yii::t('app','Country'); ?></strong> : <span><?= $model->country->name; ?></span></p>
    <p><strong><?= Yii::t('app','Email'); ?></strong> : <span><?= $model->email; ?></span></p>
    <p><strong><?= Yii::t('app','Phone'); ?></strong> : <span><?= $model->phone; ?></span></p>
    <p><strong><?= Yii::t('app','Has Viber'); ?></strong> : <span><?= Yii::t('app',$model->has_viber ? 'Yes' : 'No'); ?></span></p>
    <p><strong><?= Yii::t('app','Has WhatsApp'); ?></strong> : <span><?= Yii::t('app',$model->has_whatsapp ? 'Yes' : 'No'); ?></span></p>
    <p><strong><?= Yii::t('app','Timezone'); ?></strong> : <span><?= $model->timezone; ?></span></p>
    <p><strong><?= Yii::t('app','Comfortable call time'); ?></strong> : <span><?= $model->comfortable_call_time; ?></span></p>

    <?php if($model->status_id == Constants::CV_STATUS_REJECTED && !empty($model->discard_reason)): ?>
        <hr>
        <h4><?= Yii::t('app','Discard reason'); ?></h4>
        <p><?= $model->discard_reason; ?></p>
    <?php endif; ?>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-default pull-left" data-dismiss="modal"><?= Yii::t('app','OK'); ?></button>
</div>

