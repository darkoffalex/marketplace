<?php
return [
    /*
    'savePath' => __DIR__.'/../sessions',
    'cookieParams' => [
        'path' => '/',
        'domain' => '.marketplace.loc',
    ]
    */

    'cookieParams' => [
        'domain' => '.marketplace.loc',
        'path' => '/',
        'httpOnly' => true,
        'secure' => false,
    ],
];