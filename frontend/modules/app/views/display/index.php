<?php
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'รายการจอแสดงผล';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
    <?php foreach($query as $item): ?>
    <div class="col-md-3">
        <a href="<?= Url::to(['/app/display/view','id' => $item['display_ids']])?>" target="_blank" data-pjax="0">
            <div class="widget yellow-bg p-lg text-center">
                <div class="m-b-md">
                    <i class="fa fa-desktop fa-4x"></i>
                    <h4 class="m-xs">
                        <?= $item['display_name']; ?>
                    </h4>
                </div>
            </div>
        </a>
    </div>
    <?php endforeach;?>
</div>
