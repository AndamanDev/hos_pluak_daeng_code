<?php
namespace inspinia\assets;

use yii\web\AssetBundle as BaseAssetBundle;

class InspiniaAsset extends BaseAssetBundle
{
    public $sourcePath = '@inspinia/assets/vendor';

    public $css = [
        'css/animate.css',
        'css/style.css',
        'pace/themes/silver/pace-theme-center-circle.css',
    ];

    public $js = [
        'js/plugins/metisMenu/jquery.metisMenu.js',
        'js/plugins/slimscroll/jquery.slimscroll.min.js',
        'js/inspinia.js',
        'js/app-config.js',
        'pace/pace.min.js',
    ];

    public $depends = [
        'frontend\assets\AppAsset',
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
        'yii\bootstrap\BootstrapPluginAsset',
        'inspinia\assets\FontAwesomeAsset',
    ];
}
