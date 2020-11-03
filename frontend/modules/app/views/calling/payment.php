<?php
use yii\helpers\Url;
use yii\helpers\Json;
use yii\web\View;
use yii\helpers\Html;
use kartik\icons\Icon;
use yii\bootstrap\Tabs;
use inspinia\widgets\Table;
use yii\web\JsExpression;

use frontend\assets\SocketIOAsset;
use inspinia\assets\ToastrAsset;
use inspinia\sweetalert2\assets\SweetAlert2Asset;
SweetAlert2Asset::register($this);
SocketIOAsset::register($this);
ToastrAsset::register($this);

$this->title = 'เรียกคิวการเงิน';
$this->params['breadcrumbs'][] = ['label' => 'เรียกคิว', 'url' => ['/app/calling/payment']];
$this->params['breadcrumbs'][] = $this->title;

$this->registerJs('var baseUrl = '.Json::encode(Url::base(true)).'; ',View::POS_HEAD);
$this->registerJs('var modelProfile = '. Json::encode($modelProfile).';',View::POS_HEAD);
$this->registerJs('var formData = '. Json::encode($formData).';',View::POS_HEAD);

$this->registerCss(<<<CSS
.dropdown-action ul li a:hover {
    font-weight: 600;
    font-size: 14px;
}
/* .dropdown-action .dropdown-menu {
    left: -75px;
} */
.footer-theme {
    display: none;
}
.tabs-container .tabs-buttom > li.active a,
.tabs-container .tabs-buttom > li.active a:hover {
    background-color: #1a7bb9 !important;
    /* border-bottom: 0px solid #fff; */
    color: #FFFFFF !important;
}
@media (max-width: 767px){
    .tabs-container .tabs-buttom > li {
        float: left !important;
    }
}
.form-group {
    margin-bottom: 0px;
}
input[type="search"] {
    height: 46px;
}
CSS
);
?>
<div class="tabs-container">
    <?php
    echo Tabs::widget([
        'items' => [
            [
                'label' => 'เรียกคิว',
                'active' => true,
                'options' => ['id' => 'my-tab1'],
            ],
            [
                'label' => 'รายการคิว '. Html::tag('span',0,['id' => 'count-qdata','class' => 'label label-warning']),
                'options' => ['id' => 'my-tab2'],
            ],
        ],
        'encodeLabels' => false,
        'renderTabContent' => false,
    ]);
    ?>
    <div class="tab-content">
        <div id="my-tab1" class="tab-pane active">
           <div class="panel-body">
                <div class="row">
                    <div class="col-xs-12 col-sm-12 col-md-12">
                        <div class="ibox float-e-margins">
                            <div class="ibox-tools">
                                <a class="collapse-link">
                                    <i class="fa fa-chevron-up"></i>
                                </a>
                            </div>
                            <div class="ibox-content" style="border-color: #fff;">
                                <?= $this->render('_form_payment',['modelProfile' => $modelProfile,'services' => $services,'formData' => $formData]); ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-12 col-sm-12 col-md-12">
                        <div class="tabs-container">
                            <?php
                            /*
                            echo Tabs::widget([
                                'items' => [
                                    [
                                        'label' => Icon::show('list').' รายการคิว '.Html::tag('badge',0,['id' => 'count-waiting','class' => 'badge badge-success']),
                                        'active' => true,
                                        'options' => ['id' => 'tab-1'],
                                        'headerOptions' => ['style' => 'width: 33.33%;'],
                                        'linkOptions' => ['style' => 'text-align: center;'],
                                    ],
                                    [
                                        'label' => Icon::show('list').' คิวกำลังเรียก '.Html::tag('badge',0,['id' => 'count-calling','class' => 'badge badge-primary']),
                                        'options' => ['id' => 'tab-2'],
                                        'headerOptions' => ['style' => 'width: 33.33%;'],
                                        'linkOptions' => ['style' => 'text-align: center;'],
                                    ],
                                    [
                                        'label' => Icon::show('list').' พักคิว '.Html::tag('badge',0,['id' => 'count-hold','class' => 'badge badge-warning']),
                                        'options' => ['id' => 'tab-3'],
                                        'headerOptions' => ['style' => 'width: 33.33%;'],
                                        'linkOptions' => ['style' => 'text-align: center;'],
                                    ],
                                ],
                                'encodeLabels' => false,
                                'renderTabContent' => false,
                            ]);*/
                            ?>
                            <div class="tab-content">
                                <div id="tab-1" class="tab-pane active">
                                    <!-- <div class="panel-body" style="border: 1px solid #e7eaec;"> -->
                                        <?php
                                        echo Table::widget([
                                            'tableOptions' => ['class' => 'table table-hover table-bordered','id' => 'tb-que-waiting'],
                                            'beforeHeader' => [
                                                [
                                                    'columns' => [
                                                        ['content' => '#', 'options' => ['style' => 'text-align: center;width: 35px;']],
                                                        ['content' => 'คิว', 'options' => ['style' => 'text-align: center;']],
                                                        ['content' => 'กลุ่มบริการ', 'options' => ['style' => 'text-align: center;']],
                                                        ['content' => 'ประเภทบริการ', 'options' => ['style' => 'text-align: center;']],
                                                        ['content' => 'เวลา', 'options' => ['style' => 'text-align: center;']],
                                                        ['content' => 'prefix', 'options' => ['style' => 'text-align: center;']],
                                                        ['content' => 'ดำเนินการ', 'options' => ['style' => 'text-align: center;']],
                                                    ],
                                                ],
                                            ],
                                            'datatableOptions' => [
                                                "clientOptions" => [
                                                    "ajax" => [
                                                        "url" => Url::base(true)."/app/calling/data-waiting-payment",
                                                        "type" => "POST",
                                                        "data" => ['data' => $modelProfile],
                                                    ],
                                                    "dom" => "<'row'<'col-sm-6'l B><'col-sm-6'f>><'row'<'col-xs-12 col-sm-12 col-md-12'tr>><'row'<'col-sm-6'i><'col-sm-6'p>>",
                                                    "responsive" => true,
                                                    "language" => [
                                                        "sSearch" => "_INPUT_",
                                                        "searchPlaceholder" => "ค้นหา...",
                                                        "sLengthMenu" => "_MENU_",
                                                    ],
                                                    "autoWidth" => false,
                                                    "deferRender" => true,
                                                    "pageLength" => -1,
                                                    "lengthMenu" => [ [10, 25, 50, -1], [10, 25, 50, "All"] ],
                                                    "columns" => [
                                                        ["data" => "index", "className" => "text-center"],
                                                        ["data" => "que_num_badge","className" => "text-center"],
                                                        ["data" => "service_group_name"],
                                                        ["data" => "service_name"],
                                                        ["data" => "created_at","className" => "text-center"],
                                                        ["data" => "service_prefix","className" => "text-center"],
                                                        ["data" => "actions", "className" => "text-center", "orderable" => false],
                                                    ],
                                                    "drawCallback" => new JsExpression('function ( settings ) {
                                                        var api = this.api();
                                                        var count  = api.data().count();
                                                        $(".count-waiting").html(count);
                                                        dtFunc.initConfirm("#" + api.table().node().id);

                                                        var rows = api.rows( {page:"current"} ).nodes();
                                                        var columns = api.columns().nodes();
                                                        var last=null;
                                            
                                                        api.column(5, {page:"current"} ).data().each( function ( group, i ) {
                                                            if ( last !== group ) {
                                                                $(rows).eq( i ).before(
                                                                    \'<tr class="group" style="background-color: #fff;"><td colspan="\'+columns.length+\'">Prefix : <b>\'+group+\'</b></td></tr>\'
                                                                );
                                            
                                                                last = group;
                                                            }
                                                        } );
                                                    }'),
                                                    "columnDefs" => [
                                                        [ "visible" => false, "targets" => [2,5] ]
                                                    ],
                                                    "order" => [[ 1, 'asc' ]],
                                                    "buttons" => [
                                                        [
                                                            "extend" => "colvis",
                                                            "text" => Icon::show('list', [], Icon::BSG),
                                                        ],
                                                        [
                                                            "text" => Icon::show('refresh', [], Icon::BSG),
                                                            "action" => new JsExpression ('function ( e, dt, node, config ) {
                                                                dt.ajax.reload();
                                                            }'),
                                                        ]
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
                                    <!-- </div> -->
                                </div>
                                <div id="tab-2" class="tab-pane">
                                    <!-- <div class="panel-body" style="border: 1px solid #e7eaec;"> -->
                                    <?php
                                        echo Table::widget([
                                            'tableOptions' => ['class' => 'table table-hover table-bordered','id' => 'tb-que-calling'],
                                            'beforeHeader' => [
                                                [
                                                    'columns' => [
                                                        ['content' => '#', 'options' => ['style' => 'text-align: center;width: 35px;']],
                                                        ['content' => 'คิว', 'options' => ['style' => 'text-align: center;']],
                                                        ['content' => 'กลุ่มบริการ', 'options' => ['style' => 'text-align: center;']],
                                                        ['content' => 'ประเภทบริการ', 'options' => ['style' => 'text-align: center;']],
                                                        ['content' => 'จุดบริการ', 'options' => ['style' => 'text-align: center;']],
                                                        ['content' => 'เวลา', 'options' => ['style' => 'text-align: center;']],
                                                        ['content' => 'prefix', 'options' => ['style' => 'text-align: center;']],
                                                        ['content' => 'ดำเนินการ', 'options' => ['style' => 'text-align: center;']],
                                                    ],
                                                ],
                                            ],
                                            'datatableOptions' => [
                                                "clientOptions" => [
                                                    "ajax" => [
                                                        "url" => Url::base(true)."/app/calling/data-calling-payment",
                                                        "type" => "POST",
                                                        "data" => ['data' => $modelProfile,'formData' => $formData],
                                                    ],
                                                    "dom" => "<'row'<'col-sm-6'l B><'col-sm-6'f>><'row'<'col-xs-12 col-sm-12 col-md-12'tr>><'row'<'col-sm-6'i><'col-sm-6'p>>",
                                                    "responsive" => true,
                                                    "language" => [
                                                        "sSearch" => "_INPUT_",
                                                        "searchPlaceholder" => "ค้นหา...",
                                                        "sLengthMenu" => "_MENU_",
                                                    ],
                                                    "autoWidth" => false,
                                                    "deferRender" => true,
                                                    "pageLength" => -1,
                                                    "lengthMenu" => [ [10, 25, 50, -1], [10, 25, 50, "All"] ],
                                                    "columns" => [
                                                        ["data" => "index", "className" => "text-center"],
                                                        ["data" => "que_num_badge","className" => "text-center"],
                                                        ["data" => "service_group_name"],
                                                        ["data" => "service_name"],
                                                        ["data" => "counter_service_name"],
                                                        ["data" => "created_at","className" => "text-center"],
                                                        ["data" => "service_prefix","className" => "text-center"],
                                                        ["data" => "actions", "className" => "text-center", "orderable" => false],
                                                    ],
                                                    "drawCallback" => new JsExpression('function ( settings ) {
                                                        var api = this.api();
                                                        var count  = api.data().count();
                                                        $(".count-calling").html(count);
                                                        dtFunc.initConfirm("#" + api.table().node().id);

                                                        var rows = api.rows( {page:"current"} ).nodes();
                                                        var columns = api.columns().nodes();
                                                        var last=null;
                                            
                                                        api.column(6, {page:"current"} ).data().each( function ( group, i ) {
                                                            if ( last !== group ) {
                                                                $(rows).eq( i ).before(
                                                                    \'<tr class="group" style="background-color: #fff;"><td colspan="\'+columns.length+\'">Prefix : <b>\'+group+\'</b></td></tr>\'
                                                                );
                                            
                                                                last = group;
                                                            }
                                                        } );
                                                    }'),
                                                    "columnDefs" => [
                                                        [ "visible" => false, "targets" => [2,4,6] ]
                                                    ],
                                                    "order" => [[ 1, 'asc' ]],
                                                    "buttons" => [
                                                        [
                                                            "extend" => "colvis",
                                                            "text" => Icon::show('list', [], Icon::BSG),
                                                        ],
                                                        [
                                                            "text" => Icon::show('refresh', [], Icon::BSG),
                                                            "action" => new JsExpression ('function ( e, dt, node, config ) {
                                                                dt.ajax.reload();
                                                            }'),
                                                        ]
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
                                    <!-- </div> -->
                                </div>
                                <div id="tab-3" class="tab-pane">
                                    <!-- <div class="panel-body" style="border: 1px solid #e7eaec;"> -->
                                    <?php
                                        echo Table::widget([
                                            'tableOptions' => ['class' => 'table table-hover table-bordered','id' => 'tb-que-hold'],
                                            'beforeHeader' => [
                                                [
                                                    'columns' => [
                                                        ['content' => '#', 'options' => ['style' => 'text-align: center;width: 35px;']],
                                                        ['content' => 'คิว', 'options' => ['style' => 'text-align: center;']],
                                                        ['content' => 'กลุ่มบริการ', 'options' => ['style' => 'text-align: center;']],
                                                        ['content' => 'ประเภทบริการ', 'options' => ['style' => 'text-align: center;']],
                                                        ['content' => 'จุดบริการ', 'options' => ['style' => 'text-align: center;']],
                                                        ['content' => 'เวลา', 'options' => ['style' => 'text-align: center;']],
                                                        ['content' => 'prefix', 'options' => ['style' => 'text-align: center;']],
                                                        ['content' => 'ดำเนินการ', 'options' => ['style' => 'text-align: center;']],
                                                    ],
                                                ],
                                            ],
                                            'datatableOptions' => [
                                                "clientOptions" => [
                                                    "ajax" => [
                                                        "url" => Url::base(true)."/app/calling/data-hold-payment",
                                                        "type" => "POST",
                                                        "data" => ['data' => $modelProfile,'formData' => $formData],
                                                    ],
                                                    "dom" => "<'row'<'col-sm-6'l B><'col-sm-6'f>><'row'<'col-xs-12 col-sm-12 col-md-12'tr>><'row'<'col-sm-6'i><'col-sm-6'p>>",
                                                    "responsive" => true,
                                                    "language" => [
                                                        "sSearch" => "_INPUT_",
                                                        "searchPlaceholder" => "ค้นหา...",
                                                        "sLengthMenu" => "_MENU_",
                                                    ],
                                                    "autoWidth" => false,
                                                    "deferRender" => true,
                                                    "pageLength" => -1,
                                                    "lengthMenu" => [ [10, 25, 50, -1], [10, 25, 50, "All"] ],
                                                    "columns" => [
                                                        ["data" => "index", "className" => "text-center"],
                                                        ["data" => "que_num_badge","className" => "text-center"],
                                                        ["data" => "service_group_name"],
                                                        ["data" => "service_name"],
                                                        ["data" => "counter_service_name"],
                                                        ["data" => "created_at","className" => "text-center"],
                                                        ["data" => "service_prefix","className" => "text-center"],
                                                        ["data" => "actions", "className" => "text-center", "orderable" => false],
                                                    ],
                                                    "drawCallback" => new JsExpression('function ( settings ) {
                                                        var api = this.api();
                                                        var count  = api.data().count();
                                                        $(".count-hold").html(count);
                                                        dtFunc.initConfirm("#" + api.table().node().id);

                                                        var rows = api.rows( {page:"current"} ).nodes();
                                                        var columns = api.columns().nodes();
                                                        var last=null;
                                            
                                                        api.column(6, {page:"current"} ).data().each( function ( group, i ) {
                                                            if ( last !== group ) {
                                                                $(rows).eq( i ).before(
                                                                    \'<tr class="group" style="background-color: #fff;"><td colspan="\'+columns.length+\'">Prefix : <b>\'+group+\'</b></td></tr>\'
                                                                );
                                            
                                                                last = group;
                                                            }
                                                        } );
                                                    }'),
                                                    "columnDefs" => [
                                                        [ "visible" => false, "targets" => [2,4,6] ]
                                                    ],
                                                    "order" => [[ 1, 'asc' ]],
                                                    "buttons" => [
                                                        [
                                                            "extend" => "colvis",
                                                            "text" => Icon::show('list', [], Icon::BSG),
                                                        ],
                                                        [
                                                            "text" => Icon::show('refresh', [], Icon::BSG),
                                                            "action" => new JsExpression ('function ( e, dt, node, config ) {
                                                                dt.ajax.reload();
                                                            }'),
                                                        ]
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
                                    <!-- </div> -->
                                </div>
                            </div>
                            <div class="footer footer-tabs" style="position: fixed;">
                                <?php
                                echo Tabs::widget([
                                    'items' => [
                                        [
                                            'label' => '<p style="margin: 0">'.Icon::show('list',['style' => 'font-size: 1.5em;']).'</p> รายการคิว '.Html::tag('badge',0,['id' => 'count-waiting','class' => 'badge badge-success count-waiting']),
                                            'active' => true,
                                            'options' => ['id' => 'tab-1'],
                                            'headerOptions' => ['style' => 'width: 33.33%;','id' => 'tabs-1'],
                                            'linkOptions' => ['style' => 'text-align: center;bottom: 10px;'],
                                        ],
                                        [
                                            'label' => '<p style="margin: 0">'.Icon::show('list',['style' => 'font-size: 1.5em;']).'</p> คิวกำลังเรียก '.Html::tag('badge',0,['id' => 'count-calling','class' => 'badge badge-primary count-calling']),
                                            'options' => ['id' => 'tab-2'],
                                            'headerOptions' => ['style' => 'width: 33.33%;','id' => 'tabs-2'],
                                            'linkOptions' => ['style' => 'text-align: center;bottom: 10px;'],
                                        ],
                                        [
                                            'label' => '<p style="margin: 0">'.Icon::show('list',['style' => 'font-size: 1.5em;']).'</p> พักคิว '.Html::tag('badge',0,['id' => 'count-hold','class' => 'badge badge-warning count-hold']),
                                            'options' => ['id' => 'tab-3'],
                                            'headerOptions' => ['style' => 'width: 33.33%;','id' => 'tabs-3'],
                                            'linkOptions' => ['style' => 'text-align: center;bottom: 10px;'],
                                        ],
                                    ],
                                    'encodeLabels' => false,
                                    'renderTabContent' => false,
                                    'options' => ['class' => 'tabs-buttom']
                                ]);
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
           </div>
        </div>
        <div id="my-tab2" class="tab-pane">
            <div class="panel-body">
                <?php
                    echo Table::widget([
                        'tableOptions' => ['class' => 'table table-hover table-striped table-bordered','id' => 'tb-que-list'],
                        /* 'panel' => [
                            'type' => Table::TYPE_DEFAULT,
                            'heading' => Html::tag('h3', Icon::show('list').' รายการคิว', ['class' => 'panel-title']),
                            'before' => '',
                            'after' => false,
                            'footer-left' => false,
                            'footer-right' => false,
                        ], */
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
                                ],
                                "buttons" => [
                                    [
                                        "extend" => "colvis",
                                        "text" => Icon::show('list', [], Icon::BSG),
                                    ],
                                    [
                                        "text" => Icon::show('refresh', [], Icon::BSG),
                                        "action" => new JsExpression ('function ( e, dt, node, config ) {
                                            dt.ajax.reload();
                                        }'),
                                    ]
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
<?php
$this->registerJsFile(
    '@web/js/call/payment.js',
    [
        'depends' => [\yii\web\JqueryAsset::className(),'inspinia\assets\InspiniaAsset'],
        'position' => View::POS_END,
    ]
);
$this->registerJs(<<<JS
$('#form-TbServiceProfile').on('beforeSubmit', function (e) {
    if(!$('#tbserviceprofile-counter_service_id').val() && $('#tbserviceprofile-counter_service_id').val() == ''){
        return false;
    }else{
        return true;
    }
});

dt_tbquewaiting.on( "order.dt search.dt", function () {
    dt_tbquewaiting.column(0, {search:"applied", order:"applied"}).nodes().each( function (cell, i) {
        cell.innerHTML = i+1;
    } );
} ).draw();

dt_tbquecalling.on( "order.dt search.dt", function () {
    dt_tbquecalling.column(0, {search:"applied", order:"applied"}).nodes().each( function (cell, i) {
        cell.innerHTML = i+1;
    } );
} ).draw();

dt_tbquehold.on( "order.dt search.dt", function () {
    dt_tbquehold.column(0, {search:"applied", order:"applied"}).nodes().each( function (cell, i) {
        cell.innerHTML = i+1;
    } );
} ).draw();

function initStyle(){
    $('.navbar-minimalize').on('click', function () {
        setTimeout(() => {
            $(".footer-tabs").css("left",$('#side-menu').width());
        }, 300);
    });

    $(window).bind("resize", function () {
        $(".footer-tabs").css("left",$('#side-menu').width());
    });
    SmoothlyMenu();
    setTimeout(() => {
        $(".footer-tabs").css("left",$('#side-menu').width());
    }, 300);

    if (localStorageSupport) {
        if(localStorage.getItem("collapse_menu") == null){
            localStorage.setItem("collapse_menu", 'on');
            $('#collapsemenu').prop('checked', true);
            $("body").addClass('mini-navbar');
        }
        if(localStorage.getItem("fixedsidebar") == null){
            localStorage.setItem("fixedsidebar", 'on');
            $("body").addClass('fixed-sidebar');
            $('.sidebar-collapse').slimScroll({
                height: '100%',
                railOpacity: 0.9
            });
            $('#fixedsidebar').prop('checked', true);
        }
    }
}
initStyle();
JS
);
?>