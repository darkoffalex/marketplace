<?php
use app\helpers\Help;
use yii\helpers\Url;

/* @var $comments \app\models\MonitoredGroupPostComment[] */
/* @var $post \app\models\MonitoredGroupPost */
/* @var $this \yii\web\View */
/* @var $controller \app\modules\groupAdmin\controllers\MonitoredGroupsController */
/* @var $user \app\models\User */

$user = Yii::$app->user->identity;
$controller = $this->context;
?>

<div class="modal-header">
    <h4 class="modal-title"><?= Yii::t('app','Comments'); ?></h4>
</div>

<div class="modal-body box-comments" style="max-height: 500px; overflow-y: scroll;">
    <?php if(!empty($comments)): ?>
        <?php foreach($comments as $comment): ?>

            <div class="box-comment">
                <img class="img-circle img-sm" src="<?= Url::to('@web/frontend/img/profile_128.png') ?>" alt="user image">
                <div class="comment-text">
                    <span class="username"><?= Yii::t('app','User'); ?><span class="text-muted pull-right"><?= Help::dateReformat($comment->created_at,'d.m.Y H:i','Y-m-d H:i:s'); ?></span></span>
                    <p><?= $comment->text; ?></p>
                    <a target="_blank" href="https://www.facebook.com/<?= $comment->facebook_id; ?>/"><i class="fa fa-facebook margin-r-5"></i><?= Yii::t('app','View on facebook'); ?></a>
                </div><!-- /.comment-text -->
            </div>

            <?php if(!empty($comment->childrenSorted)): ?>
                <?php foreach ($comment->childrenSorted as $child): ?>
                    <div class="box-comment" style="padding-left: 20px; font-size: 12px;">
                        <img class="img-circle img-sm" src="<?= Url::to('@web/frontend/img/profile_128.png') ?>" alt="user image">
                        <div class="comment-text">
                            <span class="username"><?= Yii::t('app','User'); ?><span class="text-muted pull-right"><?= Help::dateReformat($comment->created_at,'d.m.Y H:i','Y-m-d H:i:s'); ?></span></span>
                            <p><?= $child->text; ?></p>
                            <a target="_blank" href="https://www.facebook.com/<?= $comment->facebook_id; ?>/"><i class="fa fa-facebook margin-r-5"></i><?= Yii::t('app','View on facebook'); ?></a>
                        </div><!-- /.comment-text -->
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>

        <?php endforeach; ?>
    <?php else: ?>
        <div class="box-comment">
            <p><?= Yii::t('app','No data'); ?></p>
        </div>
    <?php endif; ?>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-default pull-left" data-dismiss="modal"><?= Yii::t('app','Close'); ?></button>
</div>

