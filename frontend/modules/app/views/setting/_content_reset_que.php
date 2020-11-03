<?php
use yii\helpers\Html;
use kartik\icons\Icon;
use inspinia\widgets\Table;
use yii\web\JsExpression;
use yii\helpers\Url;

$this->params['breadcrumbs'][] = ['label' => 'ตั้งค่า', 'url' => ['/app/setting/counter-service']];
$this->params['breadcrumbs'][] = ['label' => 'ระบบคิว', 'url' => ['/app/setting/counter-service']];
$this->params['breadcrumbs'][] = 'รีเซ็ตคิว';
?>
<div class="tabs-container">
    <?= $this->render('_tabs'); ?>
    <div class="tab-content">
        <div id="tab-reset" class="tab-pane active">
            <div class="panel-body">
                <?php
                echo Table::widget([
                    'tableOptions' => ['class' => 'table table-hover table-striped','id' => 'tb-que-list'],
                    'panel' => [
                        'type' => Table::TYPE_DEFAULT,
                        'heading' => Html::tag('h3', Icon::show('list').' รายการคิว', ['class' => 'panel-title']),
                        'before' => Html::button(Icon::show('refresh').'Reset',['class' => 'btn btn-danger activity-reset']),
                        'after' => false,
                        'footer-left' => false,
                        'footer-right' => false,
                    ],
                    'beforeHeader' => [
                        [
                            'columns' => [
                                ['content' => '#', 'options' => ['style' => 'text-align: center;width: 35px;']],
                                ['content' => 'หมายเลขคิว', 'options' => ['style' => 'text-align: center;']],
                                ['content' => 'กลุ่มบริการ','options' => ['style' => 'text-align: center;']],
                                ['content' => 'ชื่อบริการ', 'options' => ['style' => 'text-align: center;']],
                                ['content' => 'เวลา', 'options' => ['style' => 'text-align: center;']],
                                ['content' => 'สถานะ', 'options' => ['style' => 'text-align: center;']],
                                ['content' => 'ดำเนินการ', 'options' => ['style' => 'text-align: center;']],
                            ],
                        ],
                    ],
                    'datatableOptions' => [
                        "clientOptions" => [
                            "ajax" => [
                                "url" => Url::base(true)."/app/kiosk/data-que",
                                "type" => "GET",
                            ],
                            "responsive" => true,
                            "autoWidth" => false,
                            "deferRender" => true,
                            "pageLength" => 50,
                            "columns" => [
                                ["data" => "index", "className" => "text-center"],
                                ["data" => "que_num_badge","className" => "text-center"],
                                ["data" => "service_group_name"],
                                ["data" => "service_name"],
                                ["data" => "created_at","className" => "text-center"],
                                ["data" => "que_status_name","className" => "text-center"],
                                ["data" => "actions", "className" => "text-center", "orderable" => false],
                            ],
                            "drawCallback" => new JsExpression('function ( settings ) {
                                var api = this.api();
                                var count  = api.data().count();
                                $("#count-qdata").html(count);
                                dtFunc.initConfirm("#tb-que-list");
                            }'),
                            "columnDefs" => [
                                [ "visible" => false, "targets" => [6] ]
                            ]
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
<?php
$this->registerJs(<<<JS
    $('button.activity-reset').on('click',function(e){
        swal({
            title: 'ยืนยัน?',
            text: "",
            type: 'warning',
            showCancelButton: true,
            confirmButtonText: 'รีเซ็ต',
            cancelButtonText: "ยกเลิก",
            allowEscapeKey: false,
            allowOutsideClick: false,
            showLoaderOnConfirm: true,
            preConfirm: function () {
                return new Promise(function (resolve, reject) {
                    $.ajax({
                        type: 'POST',
                        url: "/app/setting/reset-data-que",
                        success: function (response, textStatus, jqXHR) {
                            dt_tbquelist.ajax.reload();
                            resolve();
                        },
                        error: function (jqXHR, textStatus, errorThrown) {
                            swal({
                                type: "error",
                                title: textStatus,
                                text: errorThrown,
                                showConfirmButton: false,
                                timer: 1500
                            });
                        },
                        dataType: "json"
                    });
                });
            },
        }).then((result) => {
            if (result.value) {
                swal.close();
            }
        });
    });
JS
);
?>