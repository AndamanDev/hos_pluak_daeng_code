<?php
use yii\helpers\Html;
use kartik\icons\Icon;
use inspinia\widgets\Table;
use yii\web\JsExpression;
use yii\helpers\Url;

use frontend\assets\SocketIOAsset;
use inspinia\assets\ToastrAsset;
use inspinia\sweetalert2\assets\SweetAlert2Asset;
SweetAlert2Asset::register($this);
SocketIOAsset::register($this);
ToastrAsset::register($this);

$this->title = 'ออกบัตรคิว';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
    <div class="col-xs-12 col-sm-12 col-md-12">
        <div class="tabs-container">
            <ul class="nav nav-tabs">
                <li class="active"><a data-toggle="tab" href="#tab-1"> <?= Html::encode($this->title); ?></a></li>
                <li class=""><a data-toggle="tab" href="#tab-2"><?= Html::encode('รายการคิว') ?> <span id="count-qdata" class="badge badge-success">0</span></a></li>
            </ul>
            <div class="tab-content">
                <div id="tab-1" class="tab-pane active">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-6 col-md-offset-3">
                                <?php foreach($rows as $group): ?>
                                    <h2><?= Icon::show('address-card-o').$group['service_group_name'] ?></h2>
                                    <?php foreach($group['services'] as $data): ?>
                                        <p>
                                            <?= Html::a($data['service_prefix'].': '.$data['service_name'],
                                                ['/app/kiosk/create-ticket','service_id' => $data['service_id'],'service_group_id' => $group['service_group_id']],
                                                [
                                                    'class' => 'btn btn-success btn-lg btn-block btn-outline dim btn-large-dim activity-print',
                                                    'style' => 'text-align: left;width: 100%;height: 100%;',
                                                    'title' => $data['service_prefix'].': '.$data['service_name']
                                                ]
                                            ); ?>
                                        </p>
                                    <?php endforeach; ?>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="tab-2" class="tab-pane">
                    <div class="panel-body">
                        <?php
                        $pdfHeader = [
                            'L' => [
                                'content' => 'Yii2 Datatable Export (PDF)',
                                'font-size' => 8,
                                'color' => '#333333',
                            ],
                            'C' => [
                                'content' => 'Datatable Export',
                                'font-size' => 16,
                                'color' => '#333333',
                            ],
                            'R' => [
                                'content' => 'Generated' . ': ' . date('D, d-M-Y g:i a T'),
                                'font-size' => 8,
                                'color' => '#333333',
                            ],
                        ];
                        $pdfFooter = [
                            'L' => [
                                'content' => '© Yii2 Extensions',
                                'font-size' => 8,
                                'font-style' => 'B',
                                'color' => '#999999',
                            ],
                            'R' => [
                                'content' => '[ {PAGENO} ]',
                                'font-size' => 10,
                                'font-style' => 'B',
                                'font-family' => 'serif',
                                'color' => '#333333',
                            ],
                            'line' => true,
                        ];
                        $config = [
                            'label' => 'PDF',
                            'icon' => 'file-pdf-o',
                            'iconOptions' => ['class' => 'text-danger'],
                            'showHeader' => true,
                            'showPageSummary' => true,
                            'showFooter' => true,
                            'showCaption' => true,
                            'filename' => 'grid-export',
                            'alertMsg' => 'The PDF export file will be generated for download.',
                            'options' => ['title' => 'Portable Document Format'],
                            'mime' => 'application/pdf',
                            'config' => [
                                'mode' => 'UTF-8',
                                'format' => 'A4-L',
                                'destination' => 'D',
                                'marginTop' => 20,
                                'marginBottom' => 20,
                                'cssInline' => '.kv-wrap{padding:20px;}' .
                                    '.kv-align-center{text-align:center;}' .
                                    '.kv-align-left{text-align:left;}' .
                                    '.kv-align-right{text-align:right;}' .
                                    '.kv-align-top{vertical-align:top!important;}' .
                                    '.kv-align-bottom{vertical-align:bottom!important;}' .
                                    '.kv-align-middle{vertical-align:middle!important;}' .
                                    '.kv-page-summary{border-top:4px double #ddd;font-weight: bold;}' .
                                    '.kv-table-footer{border-top:4px double #ddd;font-weight: bold;}' .
                                    '.kv-table-caption{font-size:1.5em;padding:8px;border:1px solid #ddd;border-bottom:none;}',
                                'methods' => [
                                    'SetHeader' => [
                                        ['odd' => $pdfHeader, 'even' => $pdfHeader],
                                    ],
                                    'SetFooter' => [
                                        ['odd' => $pdfFooter, 'even' => $pdfFooter],
                                    ],
                                ],
                                'options' => [
                                    'title' => 'Datatable Export',
                                    'subject' => 'PDF export generated by kartik-v/yii2-grid extension',
                                    'keywords' => 'krajee, grid, export, yii2-grid, pdf',
                                ],
                                'contentBefore' => '',
                                'contentAfter' => '',
                            ],
                        ];
                        echo Table::widget([
                            'tableOptions' => ['class' => 'table table-hover table-striped','id' => 'tb-que-list'],
                            'panel' => [
                                'type' => Table::TYPE_DEFAULT,
                                'heading' => Html::tag('h3', Icon::show('list').' รายการคิว', ['class' => 'panel-title']),
                                'before' => '',
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
                                        ['content' => 'ดำเนินการ', 'options' => ['style' => 'text-align: center;','class' => 'skip-export']],
                                    ],
                                ],
                            ],
                            'datatableOptions' => [
                                "clientOptions" => [
                                    "dom" => "<'row'<'col-sm-6'l B><'col-sm-6'f>><'row'<'col-xs-12 col-sm-12 col-md-12'tr>><'row'<'col-sm-6'i><'col-sm-6'p>>",
                                    "ajax" => [
                                        "url" => Url::base(true)."/app/kiosk/data-que",
                                        "type" => "GET",
                                    ],
                                    "responsive" => true,
                                    "lengthMenu" => [ [10, 25, 50, -1], [10, 25, 50, "All"] ],
                                    "language" => [
                                        "sSearch" => "_INPUT_",
                                        "searchPlaceholder" => "ค้นหา...",
                                        "sLengthMenu" => "_MENU_",
                                    ],
                                    "autoWidth" => false,
                                    "deferRender" => true,
                                    "columns" => [
                                        ["data" => "index", "className" => "text-center"],
                                        ["data" => "que_num_badge","className" => "text-center"],
                                        ["data" => "service_group_name"],
                                        ["data" => "service_name"],
                                        ["data" => "created_at","className" => "text-center"],
                                        ["data" => "que_status_name","className" => "text-center"],
                                        ["data" => "actions", "className" => "text-center skip-export", "orderable" => false],
                                    ],
                                    "drawCallback" => new JsExpression('function ( settings ) {
                                        var api = this.api();
                                        var count  = api.data().count();
                                        $("#count-qdata").html(count);
                                        dtFunc.initConfirm("#tb-que-list");
                                    }'),
                                    "buttons" => [
                                        [
                                            "extend" => "colvis",
                                            "text" => Icon::show('list', [], Icon::BSG).' Column visibility',
                                        ],
                                        [
                                            "text" => Icon::show('refresh', [], Icon::BSG).' Reload',
                                            "action" => new JsExpression ('function ( e, dt, node, config ) {
                                                dt.ajax.reload();
                                            }'),
                                        ],
                                        [
                                            "text" => Icon::show('file-pdf-o').' PDF',
                                            "action" => new JsExpression ('function ( e, dt, node, config ) {
                                                TableExportPdf(dt,"/site/download",config);
                                            }'),
                                            'exportOptions' => $config
                                        ],
                                        [
                                            "extend" => "excel",
                                            "text" => Icon::show('file-excel-o').' Excel',
                                        ],
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
    </div>
</div>
<?php
$this->registerJsFile(
    '@web/js/kiosk/kiosk.js',
    ['depends' => [\yii\web\JqueryAsset::className(),'inspinia\assets\InspiniaAsset']]
);
echo $this->render('modal');
?>