<?php
use inspinia\widgets\Table;
use kartik\icons\Icon;
use yii\helpers\Html;
use yii\web\JsExpression;

$this->params['breadcrumbs'][] = ['label' => 'ตั้งค่า', 'url' => ['/app/setting/counter-service']];
$this->params['breadcrumbs'][] = ['label' => 'ระบบคิว', 'url' => ['/app/setting/counter-service']];
$this->params['breadcrumbs'][] = 'จุดบริการ';
?>
<div class="tabs-container">
    <?= $this->render('_tabs'); ?>
    <div class="tab-content">
        <div id="tab-counter-service" class="tab-pane active">
            <div class="panel-body">
                <?php
                echo Table::widget([
                    'tableOptions' => ['class' => 'table table-hover table-striped','id' => 'tb-counter-service'],
                    'panel' => [
                        'type' => Table::TYPE_DEFAULT,
                        'heading' => Html::tag('h3', Icon::show('list').' จุดบริการ', ['class' => 'panel-title']),
                        'before' => '',
                        'after' => false,
                        'footer-left' => false,
                        'footer-right' => false,
                    ],
                    'toolbar' => [
                        [
                            'content'=> Html::a(Icon::show('plus') . ' เพิ่มรายการ', ['/app/setting/create-counter-service'], ['class' => 'btn btn-success btn-sm','role' => 'modal-remote']),
                        ],
                    ],
                    'beforeHeader' => [
                        [
                            'columns' => [
                                ['content' => '#', 'options' => ['style' => 'text-align: center;width: 35px;']],
                                ['content' => 'ประเภทบริการ', 'options' => ['style' => 'text-align: center;']],
                                ['content' => 'ชื่อจุดบริการ','options' => ['style' => 'text-align: center;']],
                                ['content' => 'หมายเลข', 'options' => ['style' => 'text-align: center;']],
                                ['content' => 'กลุ่มบริการ', 'options' => ['style' => 'text-align: center;']],
                                ['content' => 'เสียงบริการ', 'options' => ['style' => 'text-align: center;']],
                                ['content' => 'เสียงเรียกหมายเลข', 'options' => ['style' => 'text-align: center;']],
                                ['content' => 'สถานะ', 'options' => ['style' => 'text-align: center;']],
                                ['content' => 'ดำเนินการ', 'options' => ['style' => 'text-align: center;']],
                            ],
                        ],
                    ],
                    'datatableOptions' => [
                        "clientOptions" => [
                            "ajax" => [
                                "url" => "/app/setting/data-counter-service",
                                "type" => "GET",
                            ],
                            "responsive" => true,
                            "language" => [
                            ],
                            "autoWidth" => false,
                            "deferRender" => true,
                            "columns" => [
                                ["data" => "index", "className" => "text-center"],
                                ["data" => "counter_service_type_name"],
                                ["data" => "counter_service_name"],
                                ["data" => "counter_service_call_number","className" => "text-center"],
                                ["data" => "service_group_name"],
                                ["data" => "sound_name1"],
                                ["data" => "sound_name2"],
                                ["data" => "counter_service_status","className" => "text-center"],
                                ["data" => "actions", "className" => "text-center no-wrap", "orderable" => false],
                            ],
                            "drawCallback" => new JsExpression('function ( settings ) {
                                var api = this.api();
                                var rows = api.rows( {page:"current"} ).nodes();
                                var columns = api.columns().nodes();
                                var last=null;
                                api.column(1, {page:"current"} ).data().each( function ( group, i ) {
                                    var data = api.rows(i).data();
                                    if ( last !== group ) {
                                        $(rows).eq( i ).before(
                                            \'<tr class="warning"><td colspan="\'+columns.length+\'"><b>จุดบริการ:</b> \'+group+\' <a href="/app/setting/update-counter-service?id=\'+data[0].counter_service_type_id+\'" class="btn btn-xs btn-success" role="modal-remote"><i class="fa fa-plus"></i> เพิ่มรายการ</a> </td></tr>\'
                                        );
                                        last = group;
                                    }
                                } );
                                dtFunc.initConfirm("#tb-counter-service");
                            }'),
                            "columnDefs" => [
                                ["visible" => false, "targets" => 1],
                            ],
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