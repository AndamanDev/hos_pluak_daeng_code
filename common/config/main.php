<?php
return [
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
        '@que/user'   => '@common/modules/yii2-user',
        '@mdm/admin'   => '@common/modules/yii2-admin',
        '@inspinia' => '@common/themes/inspinia',
        '@inspinia/sweetalert2' => '@inspinia/widgets/yii2-sweetalert2',
        '@mihaildev/elfinder' => '@inspinia/widgets/yii2-elfinder',
        '@inspinia/highcharts' => '@inspinia/widgets/yii2-highcharts',
        '@inspinia/ckeditor' => '@inspinia/widgets/yii2-ckeditor',
        '@kartik/field' => '@inspinia/widgets/yii2-field-range',
        '@Mpdf' => '@common/lib/mpdf/src',
        '@unclead/multipleinput' => '@inspinia/widgets/yii2-multiple-input',
    ],
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    # ตั้งค่าการใช้งานภาษาไทย (Language)
    'language' => 'th', // ตั้งค่าภาษาไทย
    # ตั้งค่า TimeZone ประเทศไทย
    'timeZone' => 'Asia/Bangkok', // ตั้งค่า TimeZone
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'authManager' => [
            'class' => 'yii\rbac\DbManager', // or use 'yii\rbac\DbManager'
        ],
        'formatter' => [
            'class' => 'yii\i18n\Formatter',
            'nullDisplay' => '',
            'dateFormat' => 'php:Y-m-d',
            'datetimeFormat' => 'php:Y-m-d H:i:s',
            'timeFormat' => 'php:H:i:s',
            'defaultTimeZone' => 'Asia/Bangkok',
            'timeZone' => 'Asia/Bangkok'
        ],
    ],
    'modules' => [
        'user' => [
            'class' => 'dektrium\user\Module',
            'enableRegistration' => false
        ],
    ],
];
