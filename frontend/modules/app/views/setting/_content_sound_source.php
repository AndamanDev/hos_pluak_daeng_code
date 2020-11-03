<?php

$this->params['breadcrumbs'][] = ['label' => 'ตั้งค่า', 'url' => ['/app/setting/sound-source']];
$this->params['breadcrumbs'][] = ['label' => 'ระบบคิว', 'url' => ['/app/setting/sound-source']];
$this->params['breadcrumbs'][] = 'อัพโหลดไฟล์เสียง';
?>

<div class="tabs-container">
    <?= $this->render('_tabs'); ?>
    <div class="tab-content">
        <div id="tab-sound-source" class="tab-pane active">
            <div class="panel-body">
                <?php echo \mihaildev\elfinder\ElFinder::widget([
                    'controller'       => 'file-manager-elfinder',
                    'frameOptions' => ['style'=>'min-height: 500px; width: 100%; border: 0'],
                    'language'     => 'en',
                ]);
                ?>
            </div>
        </div>
    </div>
</div>