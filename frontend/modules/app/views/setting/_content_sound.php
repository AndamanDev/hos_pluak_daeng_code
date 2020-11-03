<?php
use inspinia\widgets\Table;
use kartik\icons\Icon;
use yii\helpers\Html;
use yii\web\JsExpression;

$this->params['breadcrumbs'][] = ['label' => 'ตั้งค่า', 'url' => ['/app/setting/sound']];
$this->params['breadcrumbs'][] = ['label' => 'ระบบคิว', 'url' => ['/app/setting/sound']];
$this->params['breadcrumbs'][] = 'ข้อมูลไฟล์เสียง';
?>

<div class="tabs-container">
    <?= $this->render('_tabs'); ?>
    <div class="tab-content">
        <div id="tab-sound" class="tab-pane active">
            <div class="panel-body">
                <?php
                echo Table::widget([
                    'tableOptions' => ['class' => 'table table-hover table-striped','id' => 'tb-sound'],
                    'panel' => [
                        'type' => Table::TYPE_DEFAULT,
                        'heading' => Html::tag('h3', Icon::show('list').' ข้อมูลไฟล์เสียง', ['class' => 'panel-title']),
                        'before' => '',
                        'after' => false,
                        'footer-left' => false,
                        'footer-right' => false,
                    ],
                    'toolbar' => [
                        [
                            'content'=> Html::a(Icon::show('plus') . ' เพิ่มรายการ', ['/app/setting/create-sound'], ['class' => 'btn btn-success btn-sm','role' => 'modal-remote']),
                        ],
                    ],
                    'beforeHeader' => [
                        [
                            'columns' => [
                                ['content' => '#', 'options' => ['style' => 'text-align: center;width: 35px;']],
                                ['content' => 'ชื่อไฟล์', 'options' => ['style' => 'text-align: center;']],
                                ['content' => 'โฟรเดอร์ไฟล์','options' => ['style' => 'text-align: center;']],
                                ['content' => 'เสียงเรียก', 'options' => ['style' => 'text-align: center;']],
                                ['content' => 'ประเภทเสียง', 'options' => ['style' => 'text-align: center;']],
                                ['content' => 'ดำเนินการ', 'options' => ['style' => 'text-align: center;']],
                            ],
                        ],
                    ],
                    'datatableOptions' => [
                        "clientOptions" => [
                            "ajax" => [
                                "url" => "/app/setting/data-sound",
                                "type" => "GET",
                            ],
                            "responsive" => true,
                            "language" => [
                            ],
                            "autoWidth" => false,
                            "deferRender" => true,
                            "columns" => [
                                ["data" => "index", "className" => "text-center"],
                                ["data" => "sound_name"],
                                ["data" => "sound_path_name"],
                                ["data" => "sound_th"],
                                ["data" => "sound_type", "className" => "text-center"],
                                ["data" => "actions", "className" => "text-center", "orderable" => false],
                            ],
                            "drawCallback" => new JsExpression('function ( settings ) {
                                dtFunc.initConfirm("#tb-sound");
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