<?php
use yii\helpers\Html;
use yii\widgets\Breadcrumbs;
use yii\bootstrap\Nav;

use app\widgets\LoginWidget;
use app\widgets\LanguageSwitchWidget;
use yii\helpers\Url;

/* @var $this \yii\web\View */
/* @var $controller \app\modules\admin\controllers\MainController */
/* @var $content string */
/* @var $user \app\models\User */

app\assets\FrontendAsset::register($this);
dmstr\web\AdminLteAsset::register($this);

$directoryAsset = Yii::$app->assetManager->getPublishedUrl('@vendor/almasaeed2010/adminlte/dist');
$controller = $this->context;
$user = Yii::$app->user->identity;
?>

<?php $this->beginPage() ?>
    <!DOCTYPE html>
    <html lang="<?= Yii::$app->language ?>">
    <head>
        <meta charset="<?= Yii::$app->charset ?>"/>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <?= Html::csrfMetaTags() ?>
        <title><?= Html::encode($this->title); ?></title>
        <?php $this->head() ?>
    </head>

    <body class="skin-blue layout-top-nav" style="height: auto; min-height: 100%;">
    <?php $this->beginBody() ?>
    <div class="wrapper" style="height: auto; min-height: 100%;">

        <header class="main-header">
            <nav class="navbar navbar-static-top">
                <div class="container">
                    <div class="navbar-header">
                        <a href="<?= Url::to(['/user/main/index']); ?>" class="navbar-brand" style="padding: 8px 15px;"><b><?= Yii::$app->name; ?></b> <br> <h6 class="no-margin"><?= Yii::t('app','Member') ?></h6></a>
                        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-collapse">
                            <i class="fa fa-bars"></i>
                        </button>
                    </div>

                    <div class="collapse navbar-collapse pull-left" id="navbar-collapse">
                        <?php $c = Yii::$app->controller->id; ?>
                        <?php $a = Yii::$app->controller->action->id; ?>

                        <?= Nav::widget([
                            'items' =>
                                [
                                    [
                                        'label' => Yii::t('app','My profile'),
                                        'active' => $c == 'profile',
                                        'url' => ['/user/profile/index'],
                                        'visible' => true,
                                    ],
                                    [
                                        'label' => Yii::t('app','Marketplaces'),
                                        'active' => $c == 'marketplaces',
                                        'url' => ['/user/marketplaces/index'],
                                        'visible' => true,
                                    ],
                                    [
                                        'label' => Yii::t('app','Advertisements'),
                                        'active' => $c == 'posters',
                                        'url' => ['/user/posters/index'],
                                        'visible' => true,
                                    ],
                                    [
                                        'label' => Yii::t('app','Payment history'),
                                        'active' => $c == 'operations',
                                        'url' => ['/user/operations/index'],
                                        'visible' => true,
                                    ],
                                    [
                                        'label' => Yii::t('app','Short links'),
                                        'url' => ['/user/short-links/index'],
                                        'active' => $c == 'short-links',
                                        'visible' => true,
                                    ],
                                ],
                            'options' => ['class' => 'navbar-nav'],
                        ]); ?>
                    </div>

                    <div class="navbar-custom-menu">
                        <ul class="nav navbar-nav">
                            <?= LanguageSwitchWidget::widget(); ?>
                            <?= LoginWidget::widget(); ?>
                        </ul>
                    </div>
                </div>
            </nav>
        </header>

        <div class="content-wrapper" style="min-height: 324px;">
            <div class="container">
                <section class="content-header">
                    <h1><?= $this->title; ?></h1>
                    <?= Breadcrumbs::widget(['links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : []]); ?>
                </section>

                <section class="content">
                    <?= $content; ?>
                </section>
            </div>
        </div>

        <footer class="main-footer">
            <div class="container">
                <div class="pull-right hidden-xs"><b>Version</b> 1.0</div>
                <strong><?= Yii::t('app','Copyright ©'); ?></strong> <?= Yii::t('app','All rights reserved'); ?>.
            </div>
        </footer>
    </div>

    <?php $this->endBody() ?>

    <div class="modal modal-main fade">
        <div class="modal-dialog">
            <div class="modal-content">
            </div>
        </div>
    </div>

    <script type="text/javascript">
        $('.modal').on('hide.bs.modal', function() {
            $(this).removeData();
        });
    </script>

    </body>
    </html>
<?php $this->endPage() ?>