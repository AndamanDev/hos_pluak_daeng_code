<?php
use inspinia\widgets\Table;
use yii\helpers\Html;
use yii\web\JsExpression;
use yii\helpers\Url;
use yii\web\View;
use yii\helpers\Json;
use inspinia\assets\FontAwesomeAsset;
use frontend\assets\SocketIOAsset;
use inspinia\assets\ToastrAsset;
use inspinia\sweetalert2\assets\SweetAlert2Asset;
use frontend\assets\ModernBlinkAsset;

SweetAlert2Asset::register($this);
SocketIOAsset::register($this);
ToastrAsset::register($this);
FontAwesomeAsset::register($this);
ModernBlinkAsset::register($this);

$this->title = 'จอแสดงผล'.$config['display_name'];
$this->params['breadcrumbs'][] = $this->title;
$this->registerCssFile("@web/css/display.css", [
    'depends' => [\yii\bootstrap\BootstrapAsset::className()],
]);
$this->registerCssFile("@web/vendor/pace/themes/red/pace-theme-center-atom.css", [
    'depends' => [\yii\bootstrap\BootstrapAsset::className()],
]);
$this->registerCss($config['display_css']);
$this->registerJs('var baseUrl = '.Json::encode(Url::base(true)).'; ',View::POS_HEAD);
$this->registerJs('var config = '. Json::encode($config).';',View::POS_HEAD);
$this->registerJs('var services = '. Json::encode($service_ids).';',View::POS_HEAD);
$this->registerJs('var counters = '. Json::encode($counters).';',View::POS_HEAD);
?>
<div class="row">
    <div class="col-xs-12 col-sm-12 col-md-12">
        <div class="container">
            <?php
                echo Table::widget([
                    'tableOptions' => ['class' => 'table','id' => 'tb-display'],
                    'beforeHeader' => [
                        [
                            'columns' => [
                                ['content' => $config['text_th_left'], 'options' => ['style' => 'text-align: center;width: 60%;','class' => 'th-left']],
                                ['content' => $config['text_th_right'], 'options' => ['style' => 'text-align: center;width: 20%;','class' => 'th-right']],
                                ['content' => '', 'options' => []],
                            ],
                        ],
                    ],
                    'datatableOptions' => [
                        "clientOptions" => [
                            "ajax" => [
                                "url" => Url::base(true)."/app/display/data-display",
                                "type" => "POST",
                                "data" => ['config' => $config],
                            ],
                            "dom" => "t",
                            "responsive" => true,
                            "autoWidth" => false,
                            "deferRender" => true,
                            "ordering" => false,
                            "pageLength" => empty($config['page_length']) ? -1 : $config['page_length'],
                            "columns" => [
                                ["data" => "que_number","defaultContent" => "", "className" => "text-center td-left","orderable" => false],
                                ["data" => "counter_number","defaultContent" => "", "className" => "text-center td-right","orderable" => false],
                                ["data" => "data","orderable" => false, "visible" => false],
                            ],
                            "language" => [
                                "loadingRecords" => "กำลังโหลดข้อมูล...",
                                "emptyTable" => "ไม่มีข้อมูลคิว"
                            ]
                        ],
                        'clientEvents' => [
                            'error.dt' => 'function ( e, settings, techNote, message ){
                                e.preventDefault();
                                console.warn("error message",message);
                            }'
                        ],
                    ],
                ]);
            ?>
        </div>
    </div>
</div>
<?php if($config['show_que_waitdrug'] == 1): ?>
<div class="row">
    <div class="col-xs-12 col-sm-12 col-md-12">
        <div class="container">
            <?php
                echo Table::widget([
                    'tableOptions' => ['class' => 'table','id' => 'tb-que-wait'],
                    'columns' => [
                        [
                            ['content' => '<div class="ribbon ribbon-right ribbon-shadow ribbon-border-dash ribbon-round ribbon-wait uppercase" style="width:100%;padding: 0.2em 1em;">
                            คิวกำลังจัดยา
                            </div>', 'options' => ['style' => 'width: 40%;']],
                            ['content' => '-', 'options' => ['class' => 'td-right','style' => 'width: 60%;']],
                        ],
                    ],
                    'datatableOptions' => [
                        "clientOptions" => [
                            "ajax" => [
                                "url" => Url::base(true)."/app/display/data-que-wait",
                                "type" => "POST",
                                "data" => ['config' => $config],
                            ],
                            "dom" => "t",
                            "responsive" => true,
                            "autoWidth" => false,
                            "deferRender" => true,
                            "ordering" => false,
                            "pageLength" => 1,
                            "columns" => [
                                ["data" => "text","defaultContent" => "", "className" => "text-center","orderable" => false],
                                ["data" => "que_number","defaultContent" => "", "className" => "text-center td-right","orderable" => false],
                            ],
                            "language" => [
                                "loadingRecords" => "กำลังโหลดข้อมูล...",
                                "emptyTable" => "ไม่มีข้อมูลคิว"
                            ],
                            'initComplete' => new JsExpression ('
                                function () {
                                    var api = this.api();
                                    $("#tb-que-wait thead").hide();
                                }
                            '),
                        ],
                        'clientEvents' => [
                            'error.dt' => 'function ( e, settings, techNote, message ){
                                e.preventDefault();
                                console.warn("error message",message);
                            }'
                        ],
                    ],
                ]);
            ?>
        </div>
    </div>
</div>
<?php endif; ?>
<div class="row">
    <div class="col-xs-12 col-sm-12 col-md-12">
    <div class="container">
            <?php
                echo Table::widget([
                    'tableOptions' => ['class' => 'table','id' => 'tb-hold'],
                    'columns' => [
                        [
                            ['content' => '<div class="ribbon ribbon-right ribbon-shadow ribbon-border-dash ribbon-round ribbon-hold uppercase" style="width:100%;padding: 0.2em 1em;">
                            คิวที่เรียกไปแล้ว
                            </div>', 'options' => ['class' => 'td-left','style' => 'width: 40%;']],
                            ['content' => '-', 'options' => ['class' => 'td-right','style' => 'width: 60%;']],
                        ],
                    ],
                    'datatableOptions' => [
                        "clientOptions" => [
                            "ajax" => [
                                "url" => Url::base(true)."/app/display/data-hold",
                                "type" => "POST",
                                "data" => ['config' => $config],
                            ],
                            "dom" => "t",
                            "responsive" => true,
                            "autoWidth" => false,
                            "deferRender" => true,
                            "ordering" => false,
                            "pageLength" => 1,
                            "columns" => [
                                ["data" => "text","defaultContent" => '<div class="ribbon ribbon-right ribbon-shadow ribbon-border-dash ribbon-round ribbon-hold uppercase" style="width:100%;padding: 0.2em 1em;">
                                '.$config['text_hold'].'
                                </div>', "className" => "text-center","orderable" => false],
                                ["data" => "que_number","defaultContent" => "", "className" => "text-center td-right","orderable" => false],
                            ],
                            "language" => [
                                "loadingRecords" => "กำลังโหลดข้อมูล...",
                                "emptyTable" => "ไม่มีข้อมูลคิว"
                            ],
                            'initComplete' => new JsExpression ('
                                function () {
                                    var api = this.api();
                                    $("#tb-hold thead").hide();
                                }
                            '),
                        ],
                        'clientEvents' => [
                            'error.dt' => 'function ( e, settings, techNote, message ){
                                e.preventDefault();
                                console.warn("error message",message);
                            }'
                        ],
                    ],
                ]);
            ?>
        </div>
    </div>
</div>
<!-- <div class="row">
    <div class="col-xs-12 col-sm-12 col-md-12">
        <div class="container">
            <marquee id="marquee" style="color: #ffffff;" direction="left"><i class="fa fa-hospital-o"></i> <?= Yii::$app->params['hospital_name'] ?>ยินดีให้บริการ</marquee>
        </div>
    </div>
</div> -->

<?php
$this->registerJsFile(
    '@web/vendor/pace/pace.min.js',
    ['depends' => [\yii\web\JqueryAsset::className()]]
);
$this->registerJsFile(
    '@web/js/display/display.js',
    ['depends' => [\yii\web\JqueryAsset::className()]]
);
?>