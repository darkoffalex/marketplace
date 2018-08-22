<?php

//return [
//    'class' => 'yii\db\Connection',
//    'dsn' => 'sqlite:'.realpath(__DIR__."/../data")."/database.db",
//];

return [
    'class' => 'yii\db\Connection',
    'dsn' => 'mysql:host=localhost;dbname=marketplace',
    'username' => 'root',
    'password' => '',
    'charset' => 'utf8',
];