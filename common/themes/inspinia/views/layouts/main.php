<?php
$directoryAsset = Yii::$app->assetManager->getPublishedUrl('@inspinia/assets/vendor');
?>
<?php $this->beginContent('@inspinia/views/layouts/base.php',['class' => 'skin-0']); ?>
<div id="wrapper">
    <?= $this->render('navigation',['directoryAsset' => $directoryAsset]) ?>
    <?= $this->render('content',['content' => $content,'directoryAsset' => $directoryAsset]) ?>
</div>
<?php $this->endContent(); ?>