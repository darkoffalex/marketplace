<?php
use yii\widgets\LinkPager;

/* @var $this yii\web\View */
/* @var $controller \app\controllers\MainController */
/* @var $user \yii\web\User */
/* @var $country \app\models\Country */
/* @var $categories \app\models\Category[] */
/* @var $posters \app\models\Poster[] */
/* @var $pagination \yii\data\Pagination */

$controller = $this->context;
$user = Yii::$app->user->identity;

$controller->title = Yii::$app->name.' | '.Yii::t('app',$country->name);
$this->title = Yii::t('app',$country->name);
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row">
    <div class="col-md-12">
        <?php if(!empty($country->description)): ?>
            <p><?= $country->description; ?></p>
        <?php endif; ?>

        <?php if(!empty($categories)): ?>
            <p>
                <?php foreach ($categories as $index => $category): ?>
                    <?php if($index != 0): ?>|<?php endif; ?>
                    <a href="<?= $category->getUrl($country->domain_alias); ?>"><?= $category->name; ?></a>
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
