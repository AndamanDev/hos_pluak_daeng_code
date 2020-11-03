<?php

namespace frontend\assets;

use yii\web\AssetBundle;

class SocketIOAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [];
    public $js = [
        'vendor/socket.io-client/dist/socket.io.js',
        'vendor/socket.io-client/dist/socket-client.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\web\JqueryAsset',
        'inspinia\assets\ToastrAsset'
    ];
}