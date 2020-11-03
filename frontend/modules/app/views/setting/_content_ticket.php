<?php
use inspinia\widgets\Table;
use kartik\icons\Icon;
use yii\helpers\Html;
use yii\web\JsExpression;

$this->params['breadcrumbs'][] = ['label' => 'ตั้งค่า', 'url' => ['/app/setting/ticket']];
$this->params['breadcrumbs'][] = ['label' => 'ระบบคิว', 'url' => ['/app/setting/ticket']];
$this->params['breadcrumbs'][] = 'บัตรคิว';
?>

<div class="tabs-container">
    <?= $this->render('_tabs'); ?>
    <div class="tab-content">
        <div id="tab-ticket" class="tab-pane active">
            <div class="panel-body">
                <?php
                echo Table::widget([
                    'tableOptions' => ['class' => 'table table-hover table-striped','id' => 'tb-ticket'],
                    'panel' => [
                        'type' => Table::TYPE_DEFAULT,
                        'heading' => Html::tag('h3', Icon::show('list').' บัตรคิว', ['class' => 'panel-title']),
                        'before' => '',
                        'after' => false,
                        'footer-left' => false,
                        'footer-right' => false,
                    ],
                    'toolbar' => [
                        [
                            'content'=> Html::a(Icon::show('plus') . ' เพิ่มรายการ', ['/app/setting/create-ticket'], ['class' => 'btn btn-success btn-sm']),
                        ],
                    ],
                    'beforeHeader' => [
                        [
                            'columns' => [
                                ['content' => '#', 'options' => ['style' => 'text-align: center;width: 35px;']],
                                ['content' => 'ชื่อ รพ. ไทย', 'options' => ['style' => 'text-align: center;']],
                                ['content' => 'ชื่อ รพ. อังกฤษ','options' => ['style' => 'text-align: center;']],
                                ['content' => 'รหัสโค้ด', 'options' => ['style' => 'text-align: center;']],
                                ['content' => 'สถานะการใช้งาน', 'options' => ['style' => 'text-align: center;']],
                                ['content' => 'ดำเนินการ', 'options' => ['style' => 'text-align: center;']],
                            ],
                        ],
                    ],
                    'datatableOptions' => [
                        "clientOptions" => [
                            "ajax" => [
                                "url" => "/app/setting/data-ticket",
                                "type" => "GET",
                            ],
                            "responsive" => true,
                            "language" => [
                            ],
                            "autoWidth" => false,
                            "deferRender" => true,
                            "columns" => [
                                ["data" => "index", "className" => "text-center"],
                                ["data" => "hos_name_th"],
                                ["data" => "hos_name_en"],
                                ["data" => "barcode_type"],
                                ["data" => "status", "className" => "text-center"],
                                ["data" => "actions", "className" => "text-center no-wrap", "orderable" => false],
                            ],
                            "drawCallback" => new JsExpression('function ( settings ) {
                                dtFunc.initConfirm("#tb-ticket");
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