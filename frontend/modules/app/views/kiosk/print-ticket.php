<?php
use yii\bootstrap\BootstrapAsset;
use yii\web\JqueryAsset;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;

BootstrapAsset::register($this);
JqueryAsset::register($this);

$this->registerCssFile("@web/css/80mm.css", [
    'depends' => [BootstrapAsset::className()],
]);
/* $this->registerCssFile("https://fonts.googleapis.com/css?family=Prompt",[
    'depends' => [BootstrapAsset::className()],
]); */

$baseUrl = 'http://pluakdaeng-queue.dyndns.info:8081';

$this->registerCss("
div#bcTarget {
    overflow: hidden !important;
    padding-top: 10px !important;
}
div#qrcode img {
    display: none;
}
.qwaiting > h4 {
    margin-top: 0px !important;
    margin-bottom: 0px !important;
    text-align: center !important;
}
");

$y = date('Y') + 543;

$this->title = 'บัตรคิว';
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>
<!-- 80mm -->
<?php 
for ($x = 0; $x < $modelService['print_copy_qty']; $x++) {
    echo "<center>";
    echo $template;
    if($modelService['print_copy_qty'] > 1){
        echo '<div class="row" style="margin-bottom:0px; margin-left:0px; margin-right:0px; margin-top:0px; width:80mm">
        <div class="col-md-12 col-sm-12 col-xs-12" style="padding:5px 20px 0px 20px">
            <div class="col-xs-12" style="padding:0; text-align:left">
                <div class="col-xs-12" style="border-top:dashed 1px #ddd; padding:4px 0px 3px 0px">
                </div>
            </div>
        </div>
        </div>';
    }
    echo "</center>";
}
?>
<?php
$this->registerJsFile(
    '@web/vendor/barcode/jquery-barcode.min.js',
    ['depends' => [\yii\web\JqueryAsset::className()]]
);
$this->registerJsFile(
    '@web/js/jquery-qrcode-0.14.0.min.js',
    ['depends' => [\yii\web\JqueryAsset::className()]]
);

$this->registerJs(<<<JS
});
$(window).on('load', function() {
    //Barcode
    $(".bcTarget").barcode("{$modelQue->que_num}", "$modelTicket->barcode_type",{
        fontSize: 14,
        showHRI: true,
        barWidth: 2,
        barHeight: 50,
        moduleSize: 10,
        output: 'css'
    });
    //QRCode
    $('.qrcode').qrcode({
        render: 'canvas',
        text: "{$baseUrl}/site/mobile-view?view_id={$modelQue->que_ids}",
        image: null,
        mode: 0,
        background: null,
        size: 100,
        fill: '#000',
        mSize: 0.1,
        mPosX: 0.5,
        mPosY: 0.5,
        fontname: 'sans',
        fontcolor: '#000',
    });
    //jQuery('.qrcode').qrcode({width: 100,height: 100,text: "{$baseUrl}/site/mobile-view?view_id={$modelQue->que_ids}" });
    //Print
    window.print();
    window.onafterprint = function(){
        window.close();
    }

JS
);
?>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>