<?php

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';

$config = [
    'id' => 'basic',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'components' => [
        'request' => [
            'cookieValidationKey' => 'FKJrs5SCytcjk7wYktC1UBERIHjVk3Dn',
            'baseUrl' => '/hos-dashboard',
        ],
        'user' => [
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => true,
            'identityCookie' => [ // เพิ่มตรงนี้เพื่อให้คุกกี้จำตัวตนทำงานถูกต้อง
            'name' => '_identity-hos-dashboard', 
            'path' => '/hos-dashboard', 
            'httpOnly' => true
            ],
        ],
        'session' => [
            'name' => 'HOSDASH-SESSID',
            'cookieParams' => [
            'path' => '/hos-dashboard',
            'httpOnly' => true,
            ],
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
            // ตั้งค่าการเข้าถึงหน้าแรกให้ชัดเจน
            '' => 'site/index',
            '<action:\w+>' => 'site/<action>',
            ],
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer' => [
            'class' => \yii\symfonymailer\Mailer::class,
            'viewPath' => '@app/mail',
            // send all mails to a file by default.
            'useFileTransport' => true,
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => $db,
        /*
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
            ],
        ],
        */
        'formatter' => [
            'dateFormat' => 'php:d/m/Y', // ตั้งค่าเริ่มต้นที่นี่
            'datetimeFormat' => 'php:d/m/Y H:i:s',
            'timeZone' => 'Asia/Bangkok',
        ],
    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];
}

return $config;
