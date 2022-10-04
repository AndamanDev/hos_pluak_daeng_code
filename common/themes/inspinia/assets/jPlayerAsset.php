<?php
namespace inspinia\assets;

use yii\web\AssetBundle;

class jPlayerAsset extends AssetBundle 
{
    public $sourcePath = '@npm/jplayer/dist';

    public $js = [
        'jplayer/jquery.jplayer.min.js',
        'add-on/jplayer.playlist.min.js',
        'add-on/jquery.jplayer.inspector.js'
    ];

    public $depends = [
        'yii\web\YiiAsset',
        'yii\web\JqueryAsset',
        'yii\jui\JuiAsset'
    ];
}