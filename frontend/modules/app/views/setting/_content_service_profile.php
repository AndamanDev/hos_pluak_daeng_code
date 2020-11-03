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
        <div id="tab-service-profile" class="tab-pane active">
            <div class="panel-body">
                <?php
                echo Table::widget([
                    'tableOptions' => ['class' => 'table table-hover table-striped','id' => 'tb-service-profile'],
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
                            'content'=> Html::a(Icon::show('plus') . ' เพิ่มรายการ', ['/app/setting/create-service-profile'], ['class' => 'btn btn-success btn-sm','role' => 'modal-remote']),
                        ],
                    ],
                    'beforeHeader' => [
                        [
                            'columns' => [
                                ['content' => '#', 'options' => ['style' => 'text-align: center;width: 35px;']],
                                ['content' => 'ชื่อโปรไฟล์', 'options' => ['style' => 'text-align: center;']],
                                ['content' => 'เคาท์เตอร์','options' => ['style' => 'text-align: center;']],
                                ['content' => 'เซอร์วิสบริการ', 'options' => ['style' => 'text-align: center;']],
                                ['content' => 'ดำเนินการ', 'options' => ['style' => 'text-align: center;']],
                            ],
                        ],
                    ],
                    'datatableOptions' => [
                        "clientOptions" => [
                            "ajax" => [
                                "url" => "/app/setting/data-service-profile",
                                "type" => "GET",
                            ],
                            "responsive" => true,
                            "language" => [
                            ],
                            "autoWidth" => false,
                            "deferRender" => true,
                            "columns" => [
                                ["data" => "index", "className" => "text-center"],
                                ["data" => "service_profile_name"],
                                ["data" => "counter_service_type_name"],
                                ["data" => "service_names"],
                                ["data" => "service_profile_status", "className" => "text-center"],
                                ["data" => "actions", "className" => "text-center", "orderable" => false],
                            ],
                            "drawCallback" => new JsExpression('function ( settings ) {
                                var api = this.api();
                                dtFunc.initConfirm("#tb-service-profile");
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