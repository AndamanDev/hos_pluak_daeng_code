<?php
use common\widgets\Alert;
use yii\widgets\Breadcrumbs;
?>
<div id="page-wrapper" class="gray-bg">
    <?=$this->render('header', ['directoryAsset' => $directoryAsset])?>
    <div class="row wrapper border-bottom white-bg page-heading" style="padding-bottom: 10px;">
        <div class="col-xs-4 col-sm-4 col-md-4">
            <h2 class="font-light m-b-xs" style="margin: 0;padding-top: 10px;"><?= $this->title; ?></h2>
        </div>
        <div class="col-xs-8 col-sm-8 col-md-8">
        <?=Breadcrumbs::widget([
                'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
                'tag' => 'ol',
                'options' => ['class' => 'breadcrumb','style' => 'float: right;margin-top:15px;'],
            ])?>
        </div>
    </div>

    <div class="wrapper wrapper-content">
        <?=Alert::widget()?>
        <?=$content?>
    </div>
    <?=$this->render('config', ['directoryAsset' => $directoryAsset])?>
    <?=$this->render('footer', ['directoryAsset' => $directoryAsset])?>
</div>