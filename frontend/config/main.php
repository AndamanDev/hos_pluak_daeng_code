<?php
$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

return [
    'id' => 'app-frontend',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'frontend\controllers',
    'name' => 'ระบบคิวโรงพยาบาลปลวกแดง',
    'defaultRoute' => 'site/index',
    'controllerMap' => [
        'file-manager-elfinder' => [
            'class' => \mihaildev\elfinder\Controller::class,
            'access' => ['@'],
            'disabledCommands' => ['netmount'],
            'roots' => [
                [
                    'baseUrl'=>'@web',
                    'basePath'=>'@webroot',
                    'path' => '/media',
                    'access' => ['read' => 'Admin', 'write' => 'Admin']
                ]
            ]
        ]
    ],
    'modules' => [
        'user' => [
            'class' => 'dektrium\user\Module',
            'enableUnconfirmedLogin' => false,
            'confirmWithin' => 21600,
            'cost' => 10,
            'admins' => ['Admin'],
            'urlPrefix' => 'auth',
            'modelMap' => [
                'RegistrationForm' => 'que\user\models\RegistrationForm',
                'User' => 'que\user\models\User',
                'Profile' => 'que\user\models\Profile',
                'LoginForm' => 'que\user\models\LoginForm'
            ],
            'controllerMap' => [
                'security' => [
                    'class' => 'que\user\controllers\SecurityController',
                    'layout' => '@inspinia/views/layouts/main-login',
                ],
                'recovery' => [
                    'class' => 'que\user\controllers\RecoveryController',
                    'layout' => '@inspinia/views/layouts/main-login',
                ],
                'registration' => [
                    'class' => 'que\user\controllers\RegistrationController',
                    'layout' => '@inspinia/views/layouts/main-login',
                ],
                'settings' => [
                    'class' => 'que\user\controllers\SettingsController',
                ],
                'admin' => [
                    'class' => 'que\user\controllers\AdminController',
                ],
            ],
        ],
        'rbac' => [
            'class' => 'mdm\admin\Module',
            'layout' => 'top-menu',
            'menus' => [
                'user' => null, // disable menu
            ],
        ],
        'gridview' =>  [
            'class' => '\kartik\grid\Module'
        ],
        'app' => [
            'class' => 'frontend\modules\app\Module',
        ],
    ],
    'components' => [
        'request' => [
            'csrfParam' => '_csrf-frontend',
            'baseUrl' => '',
        ],
        'user' => [
            'identityCookie' => [
                'name'     => '_frontendIdentity',
                'path'     => '/',
                'httpOnly' => true,
            ],
            'authTimeout' => 3600*8
        ],
        'session' => [
            'name' => 'FRONTENDSESSID',
            'cookieParams' => [
                'httpOnly' => true,
                'path'     => '/',
            ],
            'timeout' => 3600*8
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
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                'dashboard' => 'site/index',
                'player' => 'app/calling/player'
            ],
        ],
        'view' => [
            'theme' => [
                'pathMap' => [
                    '@app/views' => '@inspinia/views',
                    '@dektrium/user/views' => '@que/user/views'
                ],
            ],
        ],
        'assetManager' => [
            'appendTimestamp' => true,
        ],
        'glide' => [
            'class' => 'trntv\glide\components\Glide',
            'sourcePath' => '@app/web/uploads',
            'cachePath' => '@runtime/glide',
            'signKey' => false
        ],
        'fileStorage' => [
            'class' => 'trntv\filekit\Storage',
            'baseUrl' => '@web/uploads',
            'filesystem' => [
                'class' => 'common\components\filesystem\LocalFlysystemBuilder',
                'path' => '@webroot/uploads'
            ],
            'as log' => [
                'class' => 'common\behaviors\FileStorageLogBehavior',
                'component' => 'fileStorage'
            ]
        ],
        'keyStorage' => [
            'class' => 'common\components\keyStorage\KeyStorage'
        ],
        'api' => [
            'class' => 'common\components\ApiQueComponent',
        ],
        /* 'authClientCollection' => [
            'class' => 'yii\authclient\Collection',
            'clients' => [
                'facebook' => [
                    'class' => 'yii\authclient\clients\Facebook',
                    'clientId' => '1469390719775625',
                    'clientSecret' => '0adc266a99569ee4a71e1ebe831404da',
                ],
                'line' => [
                    'class' => 'common\clients\Line',
                    'clientId' => '1583157145',
                    'clientSecret' => 'b9afa7386fbe9b31d749f52116f07500',
                    'returnUrl' => 'http://13.229.153.53'
                ],
                'github' => [
                    'class'        => 'dektrium\user\clients\GitHub',
                    'clientId'     => '94ef0eef2cedc37bbbcf',
                    'clientSecret' => 'f4b27af16b3334e99c1bf3e386b0550499910d89',
                    'returnUrl' => 'http://km4-v1.local/user/auth?authclient=github'
                ],
            ],
        ] */
    ],
    /* 'as access' => [
        'class' => 'mdm\admin\components\AccessControl',
        'allowActions' => [
            'user/registration/*',
            'user/recovery/*',
            'rbac/*',
        ]
    ], */
    'params' => $params,
];
