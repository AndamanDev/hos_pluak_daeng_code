<?php
use yii\helpers\Html;
use inspinia\highcharts\Highcharts;
use yii\web\JsExpression;
use frontend\assets\SocketIOAsset;
use inspinia\assets\ToastrAsset;
use yii\widgets\Pjax;
use kartik\widgets\ActiveForm;
use kartik\widgets\DatePicker;

SocketIOAsset::register($this);
ToastrAsset::register($this);
/* @var $this yii\web\View */

$this->title = strtoupper('Dashboard');
$title = isset($posted['from_date']) && isset($posted['to_date']) ? 'คิววันที่ ('.Yii::$app->formatter->asDate($posted['from_date'], 'php:d F Y').' - '.Yii::$app->formatter->asDate($posted['to_date'], 'php:d F Y').')' : 'คิววันนี้';
?>
<div class="row">
    <div class="col-xs-12 col-sm-12 col-md-12">
        <div class="ibox collapsed">
            <div class="ibox-title">
                <h5>ค้นหา</h5>
                <div class="ibox-tools">
                    <a class="collapse-link">
                        <i class="fa fa-chevron-up"></i>
                    </a>
                </div>
            </div>
            <div class="ibox-content" style="border-style: outset;">
                <?php $form = ActiveForm::begin(['type'=>ActiveForm::TYPE_HORIZONTAL]); ?>
                    <div class="form-group">
                        <?= Html::label('วันที่','', ['class'=>'col-sm-2 control-label']) ?>
                        <div class="col-sm-4">
                            <?php
                            echo DatePicker::widget([
                                'model' => $model,
                                'attribute' => 'from_date',
                                'type' => DatePicker::TYPE_RANGE,
                                'attribute2' => 'to_date',
                                'pluginOptions' => [
                                    'autoclose'=>true,
                                    'format' => 'yyyy-mm-dd'
                                ],
                                'options' => ['readonly' => true,'value' => isset($posted['from_date']) ? $posted['from_date'] : Yii::$app->formatter->asDate('now', 'php:Y-m-d')],
                                'options2' => ['readonly' => true,'value' => isset($posted['to_date']) ? $posted['to_date'] : Yii::$app->formatter->asDate('now', 'php:Y-m-d'),],
                                'separator' => 'ถึงวันที่'
                            ]);
                            ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-offset-2 col-sm-10">
                            <?= Html::submitButton('แสดงข้อมูล', ['class' => 'btn btn-primary']) ?>  
                            <?= Html::a('รีเซ็ต',['/dashboard'], ['class' => 'btn btn-danger']) ?>
                        </div>
                    </div>
                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>
<?php Pjax::begin(['id' => 'pjax-dashboard']); ?>
<div class="row">
    <div class="col-xs-12 col-sm-12 col-md-12">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5><?= $title ?></h5>
                <div class="ibox-tools">
                    <a class="collapse-link">
                        <i class="fa fa-chevron-up"></i>
                    </a>
                </div>
            </div>
            <div class="ibox-content" style="border-style: outset;">
                <div class="row">
                    <?php foreach($items as $item): ?>
                    <div class="col-lg-3">
                        <div class="widget style1 yellow-bg">
                            <div class="row">
                                <div class="col-xs-4">
                                    <i class="fa fa-tags fa-5x"></i>
                                </div>
                                <div class="col-xs-8 text-right">
                                    <h3> <?= $item['service_name'] ?> </h3>
                                    <h2 class="font-bold"><?= $item['count'] ?></h2>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-xs-12 col-sm-12 col-md-6">
        <?php
        echo Highcharts::widget([
            'options' => [
                'chart'=> [
                    'plotBackgroundColor'=> null,
                    'plotBorderWidth'=> null,
                    'plotShadow'=> false,
                    'type'=> 'pie'
                ],
                'title'=> [
                    'text'=> $title
                ],
                'tooltip'=> [
                    'pointFormat'=> '{series.name}: <b>{point.percentage:.1f}%</b>'
                ],
                'plotOptions'=> [
                    'pie'=> [
                        'allowPointSelect'=> true,
                        'cursor'=> 'pointer',
                        'dataLabels'=> [
                            'enabled'=> true,
                            'format'=> '<b>{point.name}</b>: {point.percentage:.1f} %',
                            'style'=> [
                                'color'=> new JsExpression('(Highcharts.theme && Highcharts.theme.contrastTextColor) || \'black\' ')
                            ]
                        ]
                    ]
                ],
                'series'=> [[
                    'name'=> 'คิดเป็น',
                    'colorByPoint'=> true,
                    'data'=> $pieData
                ]],
                'credits' => ['enabled' => false],
            ],
            'scripts' => [
                'highcharts-more',   // enables supplementary chart types (gauge, arearange, columnrange, etc.)
                'modules/exporting', // adds Exporting button/menu to chart
                'modules/export-data',
            ],
        ]);
        ?>
    </div>
    <div class="col-xs-12 col-sm-12 col-md-6">
        <?php
        echo Highcharts::widget([
            'options' => [
                'chart'=> [
                    'type'=> 'bar'
                ],
                'title'=> [
                    'text'=> $title
                ],
                'xAxis' => [
                    'categories' => $categories,
                    'title' => [
                        'text' => null
                    ]
                ],
                'yAxis' => [
                    'min' => 0,
                    'title' => [
                        'text' => 'จำนวน',
                        'align' => 'high'
                    ],
                    'labels' => [
                        'overflow' => 'justify'
                    ]
                ],
                'tooltip' => [
                    'valueSuffix' => ' คิว'
                ],
                'plotOptions' => [
                    'bar' => [
                        'dataLabels' => [
                            'enabled' => true
                        ]
                    ]
                ],
                'series'=> $series,
                'credits' => ['enabled' => false],
            ],
            'scripts' => [
                'highcharts-more',   // enables supplementary chart types (gauge, arearange, columnrange, etc.)
                'modules/exporting', // adds Exporting button/menu to chart
                'modules/export-data',
            ],
        ]);
        ?>
    </div>
</div>
<br>
<br>
<?php Pjax::end(); ?>

<?php
$this->registerJs(<<<JS
//Socket Events
$(function () {
    socket.on('register', (res) => {
        toastr.warning('#' + res.modelQue.que_num, 'คิวใหม่!', {
            "timeOut": 5000,
            "positionClass": "toast-top-right",
            "progressBar": true,
            "closeButton": true,
        });
        $.pjax.reload({container:"#pjax-dashboard"});  //Reload
    });
});
JS
);
?>