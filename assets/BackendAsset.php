<?php

namespace app\assets;

use yii\web\AssetBundle;
use yii\web\View;

/**
 * Конфигурация asset'ов для админ-панели
 *
 * @copyright 	2017 Alex Nem
 * @link 		https://github.com/darkoffalex
 * @author 		Alex Nem
 *
 * @package app\assets
 */
class BackendAsset extends AssetBundle
{
    public $basePath = '@webroot/backend';
    public $baseUrl = '@web/backend';
    public $jsOptions = ['position' => View::POS_HEAD];

    public $css = [
        'css/common.css',
    ];

    public $js = [
    ];

    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
        'yii\bootstrap\BootstrapPluginAsset',
    ];
}
