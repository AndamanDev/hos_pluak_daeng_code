<?php
use frontend\assets\SocketIOAsset;
use inspinia\assets\ToastrAsset;
use inspinia\sweetalert2\assets\SweetAlert2Asset;
use inspinia\assets\jPlayerAsset;
use kartik\widgets\ActiveForm;
use kartik\widgets\Select2;
use yii\helpers\Html;
use frontend\modules\app\models\TbSoundStation;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\View;

jPlayerAsset::register($this);
SweetAlert2Asset::register($this);
SocketIOAsset::register($this);
ToastrAsset::register($this);

$this->title = 'โปรแกรมเสียงเรียกคิว';
$this->params['breadcrumbs'][] = $this->title;

$this->registerCssFile("@web/css/jPlayer.css", [
    'depends' => [\yii\bootstrap\BootstrapAsset::className()],
]);
$this->registerJs('var baseUrl = '.Json::encode(Url::base(true)).'; ',View::POS_HEAD);
$this->registerJs('var model = '. Json::encode($model).';',View::POS_HEAD);
?>
<div class="row">
    <div class="col-lg-12">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5><?= $this->title ?></h5>
                <div class="ibox-tools">
                    <a class="collapse-link">
                        <i class="fa fa-chevron-up"></i>
                    </a>
                </div>
            </div>
            <div class="ibox-content" style="border-style: outset;">
                <?php  $form = ActiveForm::begin(['type'=>ActiveForm::TYPE_HORIZONTAL,'id' => 'form-sound-station']); ?>
                <div class="form-group">
                    <div class="col-sm-4">
                        <?= $form->field($model, 'sound_station_id', ['showLabels'=>false])->widget(Select2::classname(), [
                            'data'=> ArrayHelper::map(TbSoundStation::find()->where(['sound_station_status' => 1])->asArray()->all(),'sound_station_id','sound_station_name') ,
                            'pluginOptions'=>['allowClear'=>true],
                            'options' => ['placeholder'=>'Select state...'],
                            'theme' => Select2::THEME_BOOTSTRAP,
                            'pluginEvents' => [
                                "change" => "function(e) {
                                    $('#form-sound-station').yiiActiveForm('validate', true);
                                }",
                            ],
                        ]); ?>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-12">
                        <?php foreach($services as $item): ?>
                            <?= \kartik\helpers\Html::badge($item['counter_service_type_name'].': '.$item['counter_service_name'],['class' => 'badge badge-primary']) ?>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php ActiveForm::end(); ?>
                <section>
                    <div id="jplayer" class="jp-jplayer"></div>

                    <div id="jp_container">
                        <div class="jp-gui ui-widget ui-widget-content ui-corner-all">
                            <ul>
                                <li class="jp-play ui-state-default ui-corner-all"><a href="javascript:;" class="jp-play ui-icon ui-icon-play" tabindex="1" title="play">play</a></li>
                                <li class="jp-pause ui-state-default ui-corner-all"><a href="javascript:;" class="jp-pause ui-icon ui-icon-pause" tabindex="1" title="pause">pause</a></li>
                                <li class="jp-stop ui-state-default ui-corner-all"><a href="javascript:;" class="jp-stop ui-icon ui-icon-stop" tabindex="1" title="stop">stop</a></li>
                                <li class="jp-repeat ui-state-default ui-corner-all"><a href="javascript:;" class="jp-repeat ui-icon ui-icon-refresh" tabindex="1" title="repeat">repeat</a></li>
                                <li class="jp-repeat-off ui-state-default ui-state-active ui-corner-all"><a href="javascript:;" class="jp-repeat-off ui-icon ui-icon-refresh" tabindex="1" title="repeat off">repeat off</a></li>
                                <li class="jp-mute ui-state-default ui-corner-all"><a href="javascript:;" class="jp-mute ui-icon ui-icon-volume-off" tabindex="1" title="mute">mute</a></li>
                                <li class="jp-unmute ui-state-default ui-state-active ui-corner-all"><a href="javascript:;" class="jp-unmute ui-icon ui-icon-volume-off" tabindex="1" title="unmute">unmute</a></li>
                                <li class="jp-volume-max ui-state-default ui-corner-all"><a href="javascript:;" class="jp-volume-max ui-icon ui-icon-volume-on" tabindex="1" title="max volume">max volume</a></li>
                            </ul>
                            <div class="jp-progress-slider"></div>
                            <div class="jp-volume-slider"></div>
                            <div class="jp-current-time"></div>
                            <div class="jp-title"></div>
                            <div class="jp-duration"></div>
                            <div class="jp-clearboth"></div>
                        </div>
                    </div>
                    <div id="jplayer_inspector"></div>
                </section>
            </div>
        </div>
    </div>
</div>
<?php
$this->registerJsFile(
    '@web/js/jPlayer.js',
    ['depends' => [\yii\web\JqueryAsset::className()]]
);
$this->registerJs(<<<JS
$.ajax({
    method: "POST",
    url: "/app/calling/autoplay-media",
    data: model,
    dataType: "json",
    success: function(res){
        if(res.length){
            $.each(res, function( index, data ) {
                if(jQuery.inArray(data.modelCaller.counter_service_id.toString(), model.counter_service_id) != -1) {
                    $.each(data.media_files, function( i, sound ) {
                        myPlaylist.add({
                            title: data.modelQue.que_num,
                            artist: data,
                            wav: sound
                        });
                    });
                }
            });
            $(jPlayerid).jPlayer("play");
        }
    },
    error:function(jqXHR, textStatus, errorThrown){
        swal({
            type: 'error',
            title: errorThrown,
            showConfirmButton: false,
            timer: 1500
        });
    }
});
JS
);
?>