<?php
use yii\widgets\LinkPager;
use app\helpers\CropHelper;

/* @var $this yii\web\View */
/* @var $controller \app\controllers\MarketplaceController */
/* @var $user \yii\web\User */
/* @var $country \app\models\Country */
/* @var $categories \app\models\Category[] */
/* @var $posters \app\models\Poster[] */
/* @var $marketplace \app\models\Marketplace */
/* @var $pagination \yii\data\Pagination */

$controller = $this->context;
$user = Yii::$app->user->identity;

$controller->title = $marketplace->name;
$this->title = $marketplace->name;
//$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default">
            <?php if(\app\helpers\FileLoad::hasFile($marketplace,'header_image_filename')): ?>
                <img class="img-responsive" src="<?= CropHelper::GetCroppedUrl($marketplace,'header_image_filename','header_image_crop_settings',Yii::$app->params['visual']['marketplaceHeaderSizes']); ?>" alt="...">
            <?php endif; ?>
            <div class="panel-body">
                <p><?= $marketplace->group_description; ?></p>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <?php if(!empty($categories)): ?>
            <p>
                <?php foreach ($categories as $index => $categoryItem): ?>
                    <?php if($index != 0): ?>|<?php endif; ?>
                    <a href="<?= $marketplace->getCategoryLink($categoryItem); ?>"><?= $categoryItem->name; ?></a>
                <?php endforeach; ?>
            </p>
        <?php endif; ?>

        <div class="row classify-wrapper">
            <?php if(!empty($posters)): ?>
                <?php foreach ($posters as $poster): ?>
                    <div class="col-md-6">
                        <div class="panel panel-default">
                            <?php if(!empty($poster->mainImageActive) && $poster->mainImageActive->hasFile()): ?>
                                <img class="img-responsive" src="<?= $poster->mainImageActive->getThumbnailUrl(); ?>" alt="...">
                            <?php endif; ?>
                            <div class="panel-body"><h3 class="panel-title"><?= $poster->title_approved; ?></h3>
                                <p><?= $poster->description_approved; ?></p>
                            </div>
                            <div class="panel-footer">
                                <div class="row">
                                    <div class="col-sm-6"><p><a  href="tel:<?= $poster->phone_approved; ?>"  class="btn btn-default classyfy-btn"><i class="fa fa-phone"></i> <?= $poster->phone_approved; ?> </a></p></div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <?php echo LinkPager::widget([
            'pagination' => $pagination,
        ]); ?>
    </div>
</div>
