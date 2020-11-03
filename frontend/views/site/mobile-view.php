<?php
use yii\bootstrap\BootstrapAsset;
use yii\helpers\Html;
use frontend\assets\SocketIOAsset;
use inspinia\sweetalert2\assets\SweetAlert2Asset;
use yii\helpers\Json;
use yii\web\View;
use yii\widgets\Pjax;

$directoryAsset = Yii::$app->assetManager->getPublishedUrl('@inspinia/assets/vendor');
$this->registerCssFile($directoryAsset."/css/style.css", [
    'depends' => [BootstrapAsset::className()],
]);

SocketIOAsset::register($this);
SweetAlert2Asset::register($this);
$this->registerJs('var modelQue = '. Json::encode($modelQue).';',View::POS_HEAD);

$this->title = 'Mobile View';
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?php echo Html::encode(!empty($this->title) ? strtoupper($this->title) . ' | '. Yii::$app->name : ''); ?></title>
    <?php $this->head() ?>
</head>
<body class="bg-gray">
<?php $this->beginBody() ?>
<?php Pjax::begin(['id' => 'pjax-mobile-view']); ?>
<div class="container" style="width: auto;">
    <div class="row">
        <div class="col-md-6 col-md-offset-3">
            <div class="widget-head-color-box navy-bg p-lg text-center" style="border-radius: 5px 5px 5px 5px;">
                <div class="mobile-content">
                    <img src="<?= Yii::getAlias('@web/images/logo/Untitled2.jpg') ?>" class="rounded-circle img-responsive center-block" alt="profile" width="100px" height="100px">
                    <div class="m-b-md">
                        <h2 class="font-bold no-margins">
                            โรงพยาบาลปลวกแดง
                        </h2>
                        <small></small>
                    </div>
                    <img src="<?= Yii::getAlias('@web/images/admin.png') ?>" class="rounded-circle circle-border m-b-md img-responsive center-block" alt="profile" width="100px" height="100px">
                    <div class="m-b-md">
                        <h1 class="font-bold no-margins" style="font-size: 40px;">
                            <?= $modelQue['que_num'] ?>
                        </h1>
                    </div>
                    <p>
                        <button type="button" class="btn btn-lg btn-primary" style="border-color: #fff;">สถานะ / จุดบริการ</button>
                    </p>
                    <div class="m-b-md">
                        <h2 class="font-bold no-margins">
                            <?= $service_name.' '.$countername; ?>
                        </h2>
                    </div>
                    <p>
                        <button type="button" class="btn btn-lg btn-primary" style="border-color: #fff;">คิวรอ / Wait</button>
                    </p>
                    <div class="m-b-md">
                        <h2 class="font-bold no-margins">
                            <?= $count ?>
                        </h2>
                    </div>
                    <div>
                        <p>
                            <span class="label label-warning" style="white-space: normal;font-size: 14px;">**นำยาเดิมมาด้วยทุกครั้ง แพ้ยา ตั้งครรภ์ หรือให้นมบุตร</span>
                        </p>
                        <p>
                            <span class="label label-warning" style="white-space: normal;font-size: 14px;">โปรด! แจ้งเภสัชกร</span>
                        </p> 
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php Pjax::end(); ?>
<?php
$this->registerJsFile(
    '@web/js/mobile-view.js',
    ['depends' => [\yii\web\JqueryAsset::className()]]
);
?>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
