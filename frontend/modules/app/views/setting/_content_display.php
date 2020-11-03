<?php
use inspinia\widgets\Table;
use kartik\icons\Icon;
use yii\helpers\Html;
use yii\web\JsExpression;

$this->params['breadcrumbs'][] = ['label' => 'ตั้งค่า', 'url' => ['/app/setting/counter-service']];
$this->params['breadcrumbs'][] = ['label' => 'ระบบคิว', 'url' => ['/app/setting/counter-service']];
$this->params['breadcrumbs'][] = 'จอแสดงผล';
?>
<div class="tabs-container">
    <?= $this->render('_tabs'); ?>
    <div class="tab-content">
        <div id="tab-display" class="tab-pane active">
            <div class="panel-body">
                <?php
                echo Table::widget([
                    'tableOptions' => ['class' => 'table table-hover table-striped','id' => 'tb-display'],
                    'panel' => [
                        'type' => Table::TYPE_DEFAULT,
                        'heading' => Html::tag('h3', Icon::show('list').' จอแสดงผล', ['class' => 'panel-title']),
                        'before' => '',
                        'after' => false,
                        'footer-left' => false,
                        'footer-right' => false,
                    ],
                    'toolbar' => [
                        [
                            'content'=> Html::a(Icon::show('plus') . ' เพิ่มรายการ', ['/app/setting/create-display'], ['class' => 'btn btn-success btn-sm']),
                        ],
                    ],
                    'beforeHeader' => [
                        [
                            'columns' => [
                                ['content' => '#', 'options' => ['style' => 'text-align: center;width: 35px;']],
                                ['content' => 'ชื่อจอแสดงผล', 'options' => ['style' => 'text-align: center;']],
                                ['content' => 'สถานะ', 'options' => ['style' => 'text-align: center;']],
                                ['content' => 'ดำเนินการ', 'options' => ['style' => 'text-align: center;']],
                            ],
                        ],
                    ],
                    'datatableOptions' => [
                        "clientOptions" => [
                            "ajax" => [
                                "url" => "/app/setting/data-display",
                                "type" => "GET",
                            ],
                            "responsive" => true,
                            "language" => [
                            ],
                            "autoWidth" => false,
                            "deferRender" => true,
                            "columns" => [
                                ["data" => "index", "className" => "text-center"],
                                ["data" => "display_name"],
                                ["data" => "display_status","className" => "text-center"],
                                ["data" => "actions", "className" => "text-center", "orderable" => false],
                            ],
                            "drawCallback" => new JsExpression('function ( settings ) {
                                var api = this.api();
                                var rows = api.rows( {page:"current"} ).nodes();
                                var columns = api.columns().nodes();
                                var last=null;
                                dtFunc.initConfirm("#tb-display");
                                $("a.activity-copy").on("click",function(event){
                                    event.preventDefault();
                                    QueSetting.dupplicate(this);
                                });
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
<?php
$this->registerJs(<<<JS
    QueSetting = {
        dupplicate: function(elm){
            swal({
                title: 'Are you sure?',
                text: "",
                type: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Confirm'
            }).then((result) => {
                if (result.value) {
                    $.ajax({
                        method: "GET",
                        url: $(elm).attr("href"),
                        dataType: "json",
                        success: function(response){
                            dt_tbdisplay.ajax.reload();
                        },
                        error: function( jqXHR, textStatus, errorThrown){
                            swal({title: 'Error...!',text: errorThrown ,type: 'error'});
                        }
                    });
                }
            });
        }
    };
JS
);
?>