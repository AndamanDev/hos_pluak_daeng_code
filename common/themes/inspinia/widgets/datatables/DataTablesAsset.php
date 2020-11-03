<?php
namespace inspinia\widgets\datatables;

use yii\web\AssetBundle;

class DataTablesAsset extends AssetBundle
{
    public $sourcePath = '@inspinia/widgets/datatables/assets';

    public $css = [
        'media/css/dataTables.bootstrap.min.css',
        'extensions/Responsive/css/responsive.bootstrap.min.css',
        'extensions/Buttons/css/buttons.dataTables.min.css',
    ];

    public $js = [
        'media/js/jquery.dataTables.min.js',
        'media/js/dataTables.bootstrap.min.js',
        'media/js/table-export-pdf.js',
        'extensions/Responsive/js/dataTables.responsive.min.js',
        'extensions/Buttons/js/dataTables.buttons.min.js',
        'extensions/Buttons/js/buttons.flash.min.js',
        'extensions/Buttons/js/buttons.colVis.min.js',
        'extensions/Buttons/js/buttons.html5.min.js',
        'extensions/Buttons/js/vfs_fonts.js',
        'extensions/Buttons/js/jszip.min.js',
        'extensions/Buttons/js/pdfmake.min.js',
    ];

    public $depends = [
        'yii\web\YiiAsset',
        'yii\web\JqueryAsset',
        'yii\bootstrap\BootstrapAsset',
        'yii\bootstrap\BootstrapPluginAsset',
    ];
}
