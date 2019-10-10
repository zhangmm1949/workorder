<?php

$local_config = require  __DIR__ . '/local_config.php'; // yii 命令行入口文件（./../yii.php）不加载local_config.php 所以在这里要引入
$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';

$config = [
    'id' => 'basic-console',
    'timeZone' => 'Asia/Shanghai', # 设置时区
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'app\commands',
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'redis' => [
            'class' => 'yii\redis\Connection',
            'hostname' => $local_config['redis_hostname'],
            'port' => $local_config['redis_port'],
            'database' => $local_config['redis_database'],
            'password' => $local_config['redis_password'],
        ],
        'log' => [
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'authManager' => [
            'class' => 'yii\rbac\DbManager', //权限控制类
            'defaultRoles' => ['guest'],
        ],
        'db' => $db,

        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            // send all mails to a file by default. You have to set
            // 'useFileTransport' to false and configure a transport
            // for the mailer to send real emails.
            'useFileTransport' => false,
            'transport' => [
                'class' => 'Swift_SmtpTransport',
                // 如果是QQ邮箱，host改为smtp.qq.com
//                'host' => 'smtp.qq.com',
                'host' => 'smtp.163.com',
                // 邮箱登录帐号
                'username' => $local_config['adminEmail'],
                // 如果是qq邮箱，这里要填写第三方授权码，而不是你的qq登录密码，参考qq邮箱的帮助文档
                //http://service.mail.qq.com/cgi-bin/help?subtype=1&&id=28&&no=1001256
                'password' => $local_config['adminEmail_password'],// 网易邮箱授权码，并非真实邮箱密码
                'port' => '465',
                'encryption' => 'ssl',
            ],
            'messageConfig'=>[
                'charset'=>'UTF-8',
                'from'=>['zhangmm1949@163.com'=>'zhangmm']
            ],
        ],

    ],
    'params' => $params,
    /*
    'controllerMap' => [
        'fixture' => [ // Fixture generation command line.
            'class' => 'yii\faker\FixtureController',
        ],
    ],
    */
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
    ];
}

return $config;
