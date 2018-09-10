<?php
namespace app\assets;

use yii\web\AssetBundle;
use yii\web\View;

/**
 * Конфигурация asset'ов для frontend-a
 *
 * @copyright 	2017 Alex Nem
 * @link 		https://github.com/darkoffalex
 * @author 		Alex Nem
 *
 * @package app\assets
 */
class FrontendAsset extends AssetBundle
{
    public $basePath = '@webroot/frontend';
    public $baseUrl = '@web/frontend';
    public $jsOptions = ['position' => View::POS_HEAD];

    public $css = [
        'css/site.css',
    ];

    public $js = [
        'js/common.js'
    ];

    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
        'yii\bootstrap\BootstrapPluginAsset',
        'rmrevin\yii\fontawesome\AssetBundle',
    ];
}
