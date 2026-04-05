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
            // ห้ามเปิด baseUrl ตรงนี้ (เพราะคุณบอกว่าใส่แล้วคลิกไม่ได้)
            'trustedHosts' => ['10.55.106.30'],
            'secureProtocolHeaders' => [
                'X-Forwarded-Proto' => ['https'],
            ],
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'baseUrl' => '/hos-dashboard', // ตัวนี้ห้ามเอาออก เพราะคุณบอกว่าตัวนี้ทำให้คลิกได้
            'rules' => [],
        ],
        'user' => [
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => true,
            // ลบ path config ออก ให้มันใช้ค่า default (Root) เพื่อไม่ให้ขัดกับ Proxy
        ],
        'session' => [
            'name' => 'HOSDASH-SESSID',
            'cookieParams' => [
                'httpOnly' => true,
                // ปิด secure ไว้ก่อน เผื่อ Proxy ส่งมาเป็น http
                'secure' => false, 
            ],
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'db' => $db,
        'formatter' => [
            'dateFormat' => 'php:d/m/Y',
            'datetimeFormat' => 'php:d/m/Y H:i:s',
            'timeZone' => 'Asia/Bangkok',
        ],
    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = ['class' => 'yii\debug\Module'];
    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = ['class' => 'yii\gii\Module'];
}

return $config;