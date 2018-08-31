<?php
return [
    /*
    'identityClass' => \app\models\User::class,
    'enableAutoLogin' => true,
    'identityCookie' => [
        'name' => 'marketplace',
        'domain' => '.marketplace.loc',
        'expire' => 0,
        'httpOnly' => true,
        'path' => '/'
    ],
    */

    'identityClass' => \app\models\User::class,
    'enableAutoLogin' => true,
    'identityCookie' => [
        'name' => '_identity',
        'httpOnly' => true,
        'domain' => '.marketplace.loc',
    ],
];