<?php
namespace inspinia\assets;

use yii\web\AssetBundle;

class ToastrAsset extends AssetBundle 
{
    public $sourcePath = '@bower/toastr';

    public $css = [
        "toastr.min.css"
    ];

    public $js = [
        'toastr.min.js', 
    ];

    public $depends = [
        'yii\web\YiiAsset',
        'yii\web\JqueryAsset',
        'yii\bootstrap\BootstrapAsset',
        'yii\bootstrap\BootstrapPluginAsset',
    ];
}