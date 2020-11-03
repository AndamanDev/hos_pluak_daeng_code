<?php
$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

return [
    'id' => 'app-backend',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'backend\controllers',
    'bootstrap' => ['log'],
    'homeUrl' => '/api',
    'name' => 'ระบบคิวโรงพยาบาลปลวกแดง',
    'defaultRoute' => 'auth/login',
    'modules' => [
        'user' => [
            'class' => 'dektrium\user\Module',
            'enableUnconfirmedLogin' => true,
            'confirmWithin' => 21600,
            'cost' => 10,
            'admins' => ['Admin'],
            'urlPrefix' => 'auth',
            'modelMap' => [
                'RegistrationForm' => 'que\user\models\RegistrationForm',
                'User' => 'backend\modules\v1\models\User',
                'Profile' => 'que\user\models\Profile'
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
        'v1' => [
            'class' => 'backend\modules\v1\Module',
        ],
    ],
    'components' => [
        'request' => [
            'csrfParam' => '_csrf-backend',
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ],
            'baseUrl' => '/api',
        ],
        'user' => [
            'identityCookie' => [
                'name'     => '_backendIdentity',
                //'path'     => '/admin',
                'httpOnly' => true,
            ],
        ],
        'session' => [
            'name' => 'BACKENDSESSID',
            'cookieParams' => [
                'httpOnly' => true,
                //'path'     => '/admin',
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
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => 'v1/user',
                    'pluralize' => false,
                    'tokens' => [
                        '{id}' => '<id:\d+>',
                    ],
                    'extraPatterns' => [
                        'OPTIONS {id}' => 'options',
                        
                        'POST login' => 'login',
                        'OPTIONS login' => 'options',

                        'GET info' => 'info',
                        'OPTIONS info' => 'options',

                        'POST update-profile' => 'update-profile',
                        'OPTIONS update-profile' => 'options',
                    ],
                ],
                [
                    'class'         => 'yii\rest\UrlRule',
                    'controller'    => 'v1/setting',
                    'pluralize'     => false,
                    'tokens'        => [
                        '{id}'             => '<id:\d+>',
                    ],
                    'extraPatterns' => [
                        'GET data-service-group' => 'data-service-group',
                        'OPTIONS data-service-group' => 'options',
                        'GET data-ticket' => 'data-ticket',
                        'OPTIONS data-ticket' => 'options',
                        'GET data-counter-service' => 'data-counter-service',
                        'OPTIONS data-counter-service' => 'options',
                        'GET data-sound' => 'data-sound',
                        'OPTIONS data-sound' => 'options',
                        'GET data-sound-station' => 'data-sound-station',
                        'OPTIONS data-sound-station' => 'options',
                        'GET data-service-profile' => 'data-service-profile',
                        'OPTIONS data-service-profile' => 'options',
                        'GET data-display' => 'data-display',
                        'OPTIONS data-display' => 'options',
                    ]
                ],

                [
                    'class'         => 'yii\rest\UrlRule',
                    'controller'    => 'v1/que',
                    'pluralize'     => false,
                    'tokens'        => [
                        '{id}'             => '<id:\d+>',
                    ],
                    'extraPatterns' => [
                        'GET data-que-list' => 'data-que-list',
                        'OPTIONS data-que-list' => 'options',

                        'POST data-wait-checkdrug' => 'data-wait-checkdrug',
                        'OPTIONS data-wait-checkdrug' => 'options',

                        'POST data-wait-drug-checkdrug' => 'data-wait-drug-checkdrug',
                        'OPTIONS data-wait-drug-checkdrug' => 'options',

                        'POST data-wait-drug-checkdrug' => 'data-wait-drug-checkdrug',
                        'OPTIONS data-wait-drug-checkdrug' => 'options',

                        'POST data-waiting-payment' => 'data-waiting-payment',
                        'OPTIONS  data-waiting-payment' => 'options',

                        'POST data-calling-payment' => 'data-calling-payment',
                        'OPTIONS  data-calling-payment' => 'options',

                        'POST data-hold-payment' => 'data-hold-payment',
                        'OPTIONS  data-hold-payment' => 'options',

                        'POST data-waiting-recive' => 'data-waiting-recive',
                        'OPTIONS  data-waiting-recive' => 'options',

                        'POST data-calling-recive' => 'data-calling-recive',
                        'OPTIONS  data-calling-recive' => 'options',

                        'GET data-form-service-profile' => 'data-form-service-profile',
                        'OPTIONS data-form-service-profile' => 'options',

                        'POST data-wait-payment-checkdrug' => 'data-wait-payment-checkdrug',
                        'OPTIONS data-wait-payment-checkdrug' => 'options',

                        'POST update-status-checkdrug' => 'update-status-checkdrug',
                        'OPTIONS update-status-checkdrug' => 'options',

                        'POST delete-que' => 'delete-que',
                        'OPTIONS delete-que' => 'options',

                        'POST call-payment' => 'call-payment',
                        'OPTIONS call-payment' => 'options',

                        'POST recall-payment' => 'recall-payment',
                        'OPTIONS recall-payment' => 'options',

                        'POST hold-payment' => 'hold-payment',
                        'OPTIONS hold-payment' => 'options',

                        'POST end-payment' => 'end-payment',
                        'OPTIONS end-payment' => 'options',

                        'POST call-recive-selected' => 'call-recive-selected',
                        'OPTIONS call-recive-selected' => 'options',

                        'POST call-recive' => 'call-recive',
                        'OPTIONS call-recive' => 'options',

                        'POST recall-recive-drug' => 'recall-recive-drug',
                        'OPTIONS recall-recive-drug' => 'options',

                        'POST hold-recive-drug' => 'hold-recive-drug',
                        'OPTIONS hold-recive-drug' => 'options',

                        'POST end-recive-drug' => 'end-recive-drug',
                        'OPTIONS end-recive-drug' => 'options',

                        'GET dashboard-data' => 'dashboard-data',
                        'OPTIONS dashboard-data' => 'options',

                        'GET que-scanner' => 'que-scanner',
                        'OPTIONS que-scanner' => 'options',

                        'GET data-que' => 'data-que',
                        'OPTIONS data-que' => 'options',

                        'GET loadmore-que-data' => 'loadmore-que-data',
                        'OPTIONS loadmore-que-data' => 'options',
                    ]
                ],
            ],
        ],
        'view' => [
            'theme' => [
                'pathMap' => [
                    '@dektrium/user/views' => '@que/user/views',
                ],
            ],
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
        'response' => [
            'class' => 'yii\web\Response',
            'on beforeSend' => function ($event) {
                $response = $event->sender;
                if ($response->format == 'html') {
                    return $response;
                }
                $responseData = $response->data;
                if (is_string($responseData) && json_encode($responseData)) {
                    $responseData = json_encode($responseData, true);
                }
                if ($response->statusCode >= 200 && $response->statusCode <= 299) {
                    $response->data = [
                        'success' => true,
                        'status' => $response->statusCode,
                        'data' => $responseData,
                    ];
                } else {
                    $response->data = [
                        'success' => false,
                        'status' => $response->statusCode,
                        'data' => $responseData,
                    ];
                }
                return $response;
            },
        ],
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
