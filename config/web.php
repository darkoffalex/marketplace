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
        'gridview' =>  kartik\grid\Module::class,
        'admin' => ['class' => \app\modules\admin\AdminModule::class],
        'group-admin' => ['class' => \app\modules\groupAdmin\GroupAdminModule::class],
        'user' => ['class' => \app\modules\user\UserModule::class],
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

                //Web-hook
                '<protocol:http|https>://<hostName>/web-hook/fb-page-hook' => 'web-hook/fb-page-hook',

                //Админ панель
                '<protocol:http|https>://<subDomain>.<domain>/admin' => 'admin/main/index',
                '<protocol:http|https>://<subDomain>.<domain>/admin/<controller>' => 'admin/<controller>/index',
                '<protocol:http|https>://<subDomain>.<domain>/admin/<controller>/<action>/<id:\d+>' => 'admin/<controller>/<action>',
                '<protocol:http|https>://<subDomain>.<domain>/admin/<controller>/<action>' => 'admin/<controller>/<action>',

                //Личный кабинет админа группы (черз поддомен .admin)
                '<protocol:http|https>://admin.<subDomain>.<domain>/<language:\w{2}>' => 'group-admin/main/index',
                '<protocol:http|https>://admin.<subDomain>.<domain>/' => 'group-admin/main/index',
                '<protocol:http|https>://admin.<subDomain>.<domain>/<language:\w{2}>/<controller>' => 'group-admin/<controller>/index',
                '<protocol:http|https>://admin.<subDomain>.<domain>/<controller>' => 'group-admin/<controller>/index',
                '<protocol:http|https>://admin.<subDomain>.<domain>/<language:\w{2}>/<controller>/<action>/<id:\d+>' => 'group-admin/<controller>/<action>',
                '<protocol:http|https>://admin.<subDomain>.<domain>/<controller>/<action>/<id:\d+>' => 'group-admin/<controller>/<action>',
                '<protocol:http|https>://admin.<subDomain>.<domain>/<language:\w{2}>/<controller>/<action>' => 'group-admin/<controller>/<action>',
                '<protocol:http|https>://admin.<subDomain>.<domain>/<controller>/<action>' => 'group-admin/<controller>/<action>',

                //Личный кабинет пользователя (черз поддомен .user)
                '<protocol:http|https>://user.<subDomain>.<domain>/<language:\w{2}>' => 'user/main/index',
                '<protocol:http|https>://user.<subDomain>.<domain>/' => 'user/main/index',
                '<protocol:http|https>://user.<subDomain>.<domain>/<language:\w{2}>/<controller>' => 'user/<controller>/index',
                '<protocol:http|https>://user.<subDomain>.<domain>/<controller>' => 'user/<controller>/index',
                '<protocol:http|https>://user.<subDomain>.<domain>/<language:\w{2}>/<controller>/<action>/<id:\d+>' => 'user/<controller>/<action>',
                '<protocol:http|https>://user.<subDomain>.<domain>/<controller>/<action>/<id:\d+>' => 'user/<controller>/<action>',
                '<protocol:http|https>://user.<subDomain>.<domain>/<language:\w{2}>/<controller>/<action>' => 'user/<controller>/<action>',
                '<protocol:http|https>://user.<subDomain>.<domain>/<controller>/<action>' => 'user/<controller>/<action>',

                //Маркетплейс (суб-суб-домен)
                '<protocol:http|https>://<subSubSubDomain>.<subSubDomain>.<subDomain>.<domain>/<language:\w{2}>' => 'marketplace/index',
                '<protocol:http|https>://<subSubSubDomain>.<subSubDomain>.<subDomain>.<domain>/' => 'marketplace/index',

                //Категория в маркетплейсе
                '<protocol:http|https>://<subSubSubDomain>.<subSubDomain>.<subDomain>.<domain>/<language:\w{2}>/<id:\d+>/<title:\w+(-\w+)*>' => 'marketplace/category',
                '<protocol:http|https>://<subSubSubDomain>.<subSubDomain>.<subDomain>.<domain>/<id:\d+>/<title:\w+(-\w+)*>' => 'marketplace/category',

                //Страна (суб-домен)
                '<protocol:http|https>://<subSubDomain>.<subDomain>.<domain>/<language:\w{2}>' => 'country/index',
                '<protocol:http|https>://<subSubDomain>.<subDomain>.<domain>/' => 'country/index',

                //Категория (обычная, в стране)
                '<protocol:http|https>://<subSubDomain>.<subDomain>.<domain>/<language:\w{2}>/<id:\d+>/<title:\w+(-\w+)*>' => 'country/category',
                '<protocol:http|https>://<subSubDomain>.<subDomain>.<domain>/<id:\d+>/<title:\w+(-\w+)*>' => 'country/category',

                //Редирект для коротких ссылок
                '<protocol:http|https>://<subDomain>.<domain>/sl/<key:\w+>' => 'main/short-link-redirect',

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
