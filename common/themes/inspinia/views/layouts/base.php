<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use frontend\assets\AppAsset;
use inspinia\assets\InspiniaAsset;

AppAsset::register($this);
InspiniaAsset::register($this);
$this->registerMetaTag([
    'name' => 'description',
    'content' => 'ระบบบริหารจัดการคิวผู้ป่วย',
]);
$this->registerMetaTag([
    'name' => 'keywords',
    'content' => Yii::$app->name,
]);
$this->registerMetaTag([
    'name' => 'description',
    'content' => $this->title,
]);
$this->registerMetaTag([
    'name' => 'keywords',
    'content' => $this->title,
]);
$this->registerMetaTag([
    'name' => 'keywords',
    'content' => 'MComScience',
]);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <link rel="shortcut icon" type="image/x-icon" href="<?= Yii::getAlias('@web') ?>/images/favicon.ico" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?php echo Html::encode(!empty($this->title) ? strtoupper($this->title).' | '.Yii::$app->name : Yii::$app->name); ?></title>
    <?php $this->head() ?>
</head>
<body class="<?= $class ?>">
<?php $this->beginBody() ?>
<?= $content ?>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>