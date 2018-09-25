<?php
use app\helpers\FileLoad;
use app\helpers\CropHelper;
use yii\helpers\Url;

/* @var $link \app\models\ShortLink */

$title = $link->title ? htmlspecialchars($link->title) : Yii::t('app','Hype.Today');
$description = $link->description ? htmlspecialchars($link->description) : Yii::t('app','Short link');
$siteName = $link->site_name ? htmlspecialchars($link->site_name) : Yii::t('app','Hype.Today');
$logo = FileLoad::hasFile($link,'image_file') ? CropHelper::GetCroppedUrl($link,'image_file',null,[256,256],true,false) : Url::to('@web/frontend/img/logo.png',true);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= $title; ?></title>
    <meta name="description" content="<?= $description; ?>">

    <meta property="og:title" content="<?= $title; ?>"/>
    <meta property="og:image" content="<?= $logo; ?>"/>
    <meta property="og:image:width" content="256">
    <meta property="og:image:height" content="256">
    <meta property="og:site_name" content="<?= $siteName; ?>"/>
    <meta property="og:description" content="<?= $description; ?>"/>

    <meta http-equiv="refresh" content="0; url=<?= $link->original_link; ?>">
</head>
</html>
