<?php
return [
    'identityClass' => \app\models\User::class,
    'enableAutoLogin' => true,
    'identityCookie' => [
        'name' => 'marketplace',
        'domain' => '.marketplace.loc',
        'expire' => 0,
        'path' => '/'
    ],
];