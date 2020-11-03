<?php
use inspinia\widgets\Table;
use kartik\icons\Icon;
use yii\helpers\Html;
use yii\web\JsExpression;

$this->params['breadcrumbs'][] = ['label' => 'ตั้งค่า', 'url' => ['/app/setting/service-group']];
$this->params['breadcrumbs'][] = ['label' => 'ระบบคิว', 'url' => ['/app/setting/service-group']];
$this->params['breadcrumbs'][] = 'กลุ่มบริการ';

?>
<div class="tabs-container">
    <?= $this->render('_tabs'); ?>
    <div class="tab-content">
        <div id="tab-service-group" class="tab-pane active">
            <div class="panel-body">
                <?php
                echo Table::widget([
                    'tableOptions' => ['class' => 'table table-hover table-striped','id' => 'tb-service-group'],
                    'panel' => [
                        'type' => Table::TYPE_DEFAULT,
                        'heading' => Html::tag('h3', Icon::show('list').' กลุ่มบริการ', ['class' => 'panel-title']),
                        'before' => '',
                        'after' => false,
                        'footer-left' => false,
                        'footer-right' => false,
                    ],
                    'toolbar' => [
                        [
                            'content'=> Html::a(Icon::show('plus') . ' เพิ่มรายการ', ['/app/setting/create-service-group'], ['class' => 'btn btn-success btn-sm','role' => 'modal-remote']),
                        ],
                    ],
                    'beforeHeader' => [
                        [
                            'columns' => [
                                ['content' => '#', 'options' => ['style' => 'text-align: center;width: 35px;']],
                                ['content' => 'ชื่อกลุ่มบริการ', 'options' => ['style' => 'text-align: center;']],
                                ['content' => 'ชื่อบริการ','options' => ['style' => 'text-align: center;']],
                                ['content' => 'แบบการพิมพ์บัตรคิว', 'options' => ['style' => 'text-align: center;']],
                                ['content' => 'จำนวนพิมพ์/ครั้ง', 'options' => ['style' => 'text-align: center;']],
                                ['content' => 'ตัวอักษร/ตัวเลข นำหน้าคิว', 'options' => ['style' => 'text-align: center;']],
                                ['content' => 'จำนวนหลักหมายเลขคิว', 'options' => ['style' => 'text-align: center;']],
                                ['content' => 'สถานะคิว', 'options' => ['style' => 'text-align: center;']],
                                ['content' => 'ดำเนินการ', 'options' => ['style' => 'text-align: center;']],
                            ],
                        ],
                    ],
                    'datatableOptions' => [
                        "clientOptions" => [
                            "ajax" => [
                                "url" => "/app/setting/data-service-group",
                                "type" => "GET",
                            ],
                            "responsive" => true,
                            "language" => [
                            ],
                            "autoWidth" => false,
                            "deferRender" => true,
                            "columns" => [
                                ["data" => "index", "className" => "text-center"],
                                ["data" => "service_group_name"],
                                ["data" => "service_name"],
                                ["data" => "hos_name_th"],
                                ["data" => "print_copy_qty", "className" => "text-center"],
                                ["data" => "service_prefix", "className" => "text-center"],
                                ["data" => "service_numdigit", "className" => "text-center"],
                                ["data" => "service_status", "className" => "text-center"],
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
                                            \'<tr class="warning"><td colspan="\'+columns.length+\'"><b>กลุ่มบริการ:</b> \'+group+\' <a href="/app/setting/update-service-group?id=\'+data[0].service_group_id+\'" class="btn btn-xs btn-success" role="modal-remote"><i class="fa fa-plus"></i> เพิ่มรายการ</a> </td></tr>\'
                                        );
                                        last = group;
                                    }
                                } );
                                dtFunc.initConfirm("#tb-service-group");
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
