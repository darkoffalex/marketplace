<?php

$params = require(__DIR__ . '/params.php');
$session = require(__DIR__ . '/session.php');
$user = require(__DIR__ . '/user.php');

$config = [
    'id' => 'MarketplaceGuide',
    'name' => 'Marketplace Guide',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log','thumbnail'],

    'language' => 'en',

    'modules' => [
        'gridview' =>  'kartik\grid\Module',
        'admin' => ['class' => 'app\modules\admin\AdminModule'],
    ],

    'components' => [

        'i18n' => [
            'translations' => [
                '*' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'basePath' => '@app/messages',
                    'sourceLanguage' => 'en-US',
                ],
                'app' => [
                    'class' => \yii\i18n\DbMessageSource::class,
                    'sourceLanguage' => 'src',
                    'sourceMessageTable' => 'source_message',
                    'messageTable' => 'message',
                    'on missingTranslation' =>  [\app\components\TranslationEventHandler::class,'handleMissingTranslation']
                ],
            ],
        ],

        'thumbnail' => [
            'class' => 'himiklab\thumbnail\EasyThumbnail',
            'cacheAlias' => 'assets/thumbnails',
        ],

        'assetManager' => [
            'bundles' => [
                'dmstr\web\AdminLteAsset' => [
                    'skin' => 'skin-blue',
                ],
            ],
        ],

        'request' => [
            'cookieValidationKey' => 'Inv98aJIqVcdG-5g34NaHHMvOdbD3Z9q',
            'baseUrl' => '',
        ],

        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],

        'user' => $user,

        'session' => $session,

        'errorHandler' => [
            'errorAction' => 'main/error',
        ],

        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'useFileTransport' => false,
            'transport' => [
                'class' => 'Swift_SmtpTransport',

                'host' => 'smtp.gmail.com',
                'username' => 'webapplications.testing@gmail.com',
                'password' => '354fsdkiose4!',
                'port' => '465',
                'encryption' => 'ssl',
            ]
        ],

        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => \yii\log\FileTarget::class,
                    'levels' => ['error', 'warning', 'info'],
                ],
                [
                    'class' => \yii\log\FileTarget::class,
                    'levels' => ['info'],
                    'categories' => ['info'],
                    'logVars' => [],
                    'logFile' => '@app/runtime/logs/info.log'
                ]
            ],
        ],

        'db' => require(__DIR__ . '/db.php'),

        'urlManager' => [
            'class' => \app\components\UrlManager::class,
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                //Админ панель
                '<protocol:http|https>://<subDomain>.<domain>/admin' => 'admin/main/index',
                '<protocol:http|https>://<subDomain>.<domain>/admin/<controller>' => 'admin/main/index',
                '<protocol:http|https>://<subDomain>.<domain>/admin/<controller>/<action>/<id:\d+>' => 'admin/<controller>/<action>',
                '<protocol:http|https>://<subDomain>.<domain>/admin/<controller>/<action>' => 'admin/<controller>/<action>',

                //Страна (суб-домен)
                '<protocol:http|https>://<subSubDomain>.<subDomain>.<domain>/<language:\w{2}>' => 'country/index',
                '<protocol:http|https>://<subSubDomain>.<subDomain>.<domain>/' => 'country/index',

                //Категория
                '<protocol:http|https>://<subSubDomain>.<subDomain>.<domain>/<language:\w{2}>/<id:\d+>/<title:\w+(-\w+)*>' => 'country/category',
                '<protocol:http|https>://<subSubDomain>.<subDomain>.<domain>/<id:\d+>/<title:\w+(-\w+)*>' => 'country/category',

                //Базовые правила
                '<protocol:http|https>://<subDomain>.<domain>/<language:\w{2}>/' => 'main/index',
                '<protocol:http|https>://<subDomain>.<domain>/' => 'main/index',
                '<protocol:http|https>://<subDomain>.<domain>/<language:\w{2}>/<controller>' => '<controller>/index',
                '<protocol:http|https>://<subDomain>.<domain>/<controller>' => '<controller>/index',
                '<protocol:http|https>://<subDomain>.<domain>/<language:\w{2}>/<controller>/<action>/<id:\d+>/<title:\w+(-\w+)*>' => '<controller>/<action>',
                '<protocol:http|https>://<subDomain>.<domain>/<controller>/<action>/<id:\d+>/<title:\w+(-\w+)*>' => '<controller>/<action>',
                '<protocol:http|https>://<subDomain>.<domain>/<language:\w{2}>/<controller>/<action>/<id:\d+>' => '<controller>/<action>',
                '<protocol:http|https>://<subDomain>.<domain>/<controller>/<action>/<id:\d+>' => '<controller>/<action>',
                '<protocol:http|https>://<subDomain>.<domain>/<language:\w{2}>/<controller>/<action>' => '<controller>/<action>',
                '<protocol:http|https>://<subDomain>.<domain>/<controller>/<action>' => '<controller>/<action>',

                //'admin' => 'admin/main/index',
                //'admin/<controller>' => 'admin/<controller>/index',
                //'admin/<controller>/<action>/<id:\d+>' => 'admin/<controller>/<action>',
                //'admin/<controller>/<action>' => 'admin/<controller>/<action>',

                //'/' => 'main/index',
                //'<controller>' => '<controller>/index',
                //'<controller>/<action>/<id:\d+>/<title:\w+(-\w+)*>' => '<controller>/<action>',
                //'<controller>/<action>/<id:\d+>/<status:\d+>' => '<controller>/<action>',
                //'<controller>/<action>/<id:\d+>' => '<controller>/<action>',
                //'<controller>/<action>' => '<controller>/<action>',

            ],
        ],
    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        'allowedIPs' => ['127.0.0.1', '::1', '78.56.14.109', '78.31.184.83']
    ];
}

return $config;
