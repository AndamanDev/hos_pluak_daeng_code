<?php
use inspinia\widgets\Table;
use kartik\icons\Icon;
use yii\helpers\Html;
use yii\web\JsExpression;

$this->params['breadcrumbs'][] = ['label' => 'ตั้งค่า', 'url' => ['/app/setting/sound-station']];
$this->params['breadcrumbs'][] = ['label' => 'ระบบคิว', 'url' => ['/app/setting/sound-station']];
$this->params['breadcrumbs'][] = 'โปรแกรมเสียงเรียก';
?>

<div class="tabs-container">
    <?= $this->render('_tabs'); ?>
    <div class="tab-content">
        <div id="tab-sound-station" class="tab-pane active">
            <div class="panel-body">
            <?php
                echo Table::widget([
                    'tableOptions' => ['class' => 'table table-hover table-striped','id' => 'tb-sound-station'],
                    'panel' => [
                        'type' => Table::TYPE_DEFAULT,
                        'heading' => Html::tag('h3', Icon::show('list').' โปรแกรมเสียงเรียก', ['class' => 'panel-title']),
                        'before' => '',
                        'after' => false,
                        'footer-left' => false,
                        'footer-right' => false,
                    ],
                    'toolbar' => [
                        [
                            'content'=> Html::a(Icon::show('plus') . ' เพิ่มรายการ', ['/app/setting/create-sound-station'], ['class' => 'btn btn-success btn-sm','role' => 'modal-remote']),
                        ],
                    ],
                    'beforeHeader' => [
                        [
                            'columns' => [
                                ['content' => '#', 'options' => ['style' => 'text-align: center;width: 35px;']],
                                ['content' => 'ชื่อ', 'options' => ['style' => 'text-align: center;']],
                                ['content' => 'จุดบริการ','options' => ['style' => 'text-align: center;']],
                                ['content' => 'สถานะ', 'options' => ['style' => 'text-align: center;']],
                                ['content' => 'ดำเนินการ', 'options' => ['style' => 'text-align: center;']],
                            ],
                        ],
                    ],
                    'datatableOptions' => [
                        "clientOptions" => [
                            "ajax" => [
                                "url" => "/app/setting/data-sound-station",
                                "type" => "GET",
                            ],
                            "responsive" => true,
                            "language" => [
                            ],
                            "autoWidth" => false,
                            "deferRender" => true,
                            "columns" => [
                                ["data" => "index", "className" => "text-center"],
                                ["data" => "sound_station_name"],
                                ["data" => "counter_service_id"],
                                ["data" => "sound_station_status", "className" => "text-center"],
                                ["data" => "actions", "className" => "text-center no-wrap", "orderable" => false],
                            ],
                            "drawCallback" => new JsExpression('function ( settings ) {
                                dtFunc.initConfirm("#tb-sound-station");
                            }'),
                        ],
                        'clientEvents' => [
                            'error.dt' => 'function ( e, settings, techNote, message ){
                                e.preventDefault();
                                swal({title: \'Error...!\',html: \'<small>\'+message+\'</small>\',type: \'error\',});
                            }'
                        ]
                    ],
                ]);
                ?>
            </div>
        </div>
    </div>
</div>