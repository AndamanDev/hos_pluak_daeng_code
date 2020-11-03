<?php
use yii\helpers\Html;
use yii\helpers\Url;

$directoryAsset = Yii::$app->assetManager->getPublishedUrl('@inspinia/assets/vendor');
?>
<?php $this->beginContent('@inspinia/views/layouts/base.php',['class' => 'gray-bg']); ?>

<div class="middle-box loginscreen animated fadeInDown">
    <?= $content; ?>
</div>
<?php $this->endContent(); ?>