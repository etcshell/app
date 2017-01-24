<?php
return [
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=127.0.0.1;dbname=test',
            'username' => 'root',
            'password' => '',
            'charset' => 'utf8',
        ],
        'mongodb' => [
            'class' => '\yii\mongodb\Connection',
        //    'dsn' => 'mongodb://piper:111111@localhost:27016/piper,localhost:27017/piper,localhost:27018/piper?replicaSet=etcshell',
            'dsn' => 'mongodb://piper:111111@127.0.0.1:27018/piper',
        ],
        'jwt' => [
            'class' => 'sizeg\jwt\Jwt',
            'key' => 'IfyouwantolawitJWT',
        ],
    ],
];
