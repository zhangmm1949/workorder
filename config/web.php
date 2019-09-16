<?php
//require __DIR__ . '/local_config.php'; 入口文件中已经引入了local_config文件，这里就不需要了，直接使用即可

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';

$config = [
    'id' => 'WorkOrder',
    'language'=>'zh-CN', //Ubuntu虚拟机系统语言为英语 为了使应用中显示为中文，设置应用的语言为中文
    'timeZone' => 'Asia/Shanghai', # 设置时区
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
        '@mdm/admin' => '@vendor/mdmsoft/yii2-admin',
    ],
    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'asd$%^$DFDSFasdffASDh2353@×&',
        ],
        'cache' => [
//            'class' => 'yii\caching\FileCache',
            'class' => 'yii\redis\Cache',
            'redis' => [
                'hostname' => $local_config['redis_hostname'],
                'port' => $local_config['redis_port'],
                'database' => $local_config['redis_database'],
                'password' => $local_config['redis_password'],
             ],
        ],
        'redis' => [
            'class' => 'yii\redis\Connection',
            'redis' =>[
                'hostname' => $local_config['redis_hostname'],
                'port' => $local_config['redis_port'],
                'database' => $local_config['redis_database'],
                'password' => $local_config['redis_password'],
            ]
        ],

        /*'session' => [
            'class' => 'yii\redis\Session',
        ],*/

        'user' => [
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => true,
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
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
                'username' => $params['adminEmail'],
                // 如果是qq邮箱，这里要填写第三方授权码，而不是你的qq登录密码，参考qq邮箱的帮助文档
                //http://service.mail.qq.com/cgi-bin/help?subtype=1&&id=28&&no=1001256
                'password' => $params['adminEmail_password'],// 网易邮箱授权码，并非真实邮箱密码
                'port' => '465',
                'encryption' => 'ssl',
            ],
            'messageConfig'=>[
                'charset'=>'UTF-8',
                'from'=>['zhangmm1949@163.com'=>'Admin']
            ],
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
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
            ],
        ],

        // admin-lte的配置
        'assetManager' => [
            'bundles' => [
                'dmstr\web\AdminLteAsset' => [
                    'skin' => 'skin-blue',
                ],
            ],
        ],

        'authManager' => [
            'class' => 'yii\rbac\DbManager', //权限控制类
            'defaultRoles' => ['guest'],
        ],

        'redis' => [
            'class' => 'yii\redis\Connection',
            'hostname' => $local_config['redis_hostname'],
            'port' => $local_config['redis_port'],
            'database' => $local_config['redis_database'],
            'password' => $local_config['redis_password'],
        ],

        'test' => [
            'class' => 'app\models\UserSystem',
        ],
    ],

    'modules' => [
        'admin' => [
            'class' => 'mdm\admin\Module',
//            'layout' => 'left-menu' //页面布局
        ],
    ],


    'as access' => [
        'class' => 'mdm\admin\components\AccessControl',
        'allowActions' => [
            '*',
            //此处的action列表，允许任何人（包括游客）访问
            //所以如果是正式环境（线上环境），不应该在这里配置任何东西，为空即可
            //但是为了在开发环境更简单的使用，可以在此处配置你所需要的任何权限
            //在开发完成之后，需要清空这里的配置，转而在系统里面通过RBAC配置权限
        ]
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
        'allowedIPs' => $params['gii.allowedIPs'],
    ];
}

return $config;
