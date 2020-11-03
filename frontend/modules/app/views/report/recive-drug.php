<?php
use yii\bootstrap\Tabs;
use kartik\icons\Icon;
use yii\helpers\Html;
use kartik\widgets\ActiveForm;
use kartik\widgets\DatePicker;
use kartik\grid\GridView;
use yii\helpers\Url;
use unclead\multipleinput\MultipleInput;
use kartik\widgets\TimePicker;

$this->title = 'รายงาน';
$this->params['breadcrumbs'][] = $this->title;
?>
<?php
echo Tabs::widget([
    'items' => [
        [
            'label' => Icon::show('chart',[]).' รายงานระยะเวลารอคอยคิวการเงิน ',
            'options' => ['id' => 'tab-1'],
            'url' => Url::to(['/app/report/index']),
        ],
        [
            'label' => Icon::show('chart',[]).' รายงานระยะเวลารอคอยคิวรับยา ',
            'options' => ['id' => 'tab-2'],
            'url' => Url::to(['/app/report/recive-drug']),
            'active' => true,
        ],
    ],
    'encodeLabels' => false,
    'renderTabContent' => false,
]);
?>

<div class="row">
    <div class="col-xs-12 col-sm-12 col-md-12">
        <div class="tabs-container">
            <div class="tab-content">
                <div id="tab-2" class="tab-pane active">
                    <div class="panel-body">
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
                                <?= Html::label('ช่วงเวลา','', ['class'=>'col-sm-2 control-label']) ?>
                                <div class="col-sm-4">
                                    <?= $form->field($model, 'times',['showLabels'=>false])->widget(MultipleInput::className(), [
                                        'max' => 20,
                                        'columns' => [
                                            [
                                                'name'  => 'time_start',
                                                'type'  => TimePicker::className(),
                                                'title' => 'เริ่ม',
                                                'value' => function($data) {
                                                    return $data['time_start'];
                                                },
                                                'options' => [
                                                    'pluginOptions' => [
                                                        'showSeconds' => false,
                                                        'showMeridian' => false,
                                                        'minuteStep' => 1,
                                                    ],
                                                    'options' => [
                                                        'readonly' => true,
                                                    ],
                                                ]
                                            ],
                                            [
                                                'name'  => 'time_end',
                                                'type'  => TimePicker::className(),
                                                'title' => 'สิ้นสุด',
                                                'value' => function($data) {
                                                    return $data['time_end'];
                                                },
                                                'options' => [
                                                    'pluginOptions' => [
                                                        'showSeconds' => false,
                                                        'showMeridian' => false,
                                                        'minuteStep' => 1,
                                                    ],
                                                    'options' => [
                                                        'readonly' => true,
                                                    ],
                                                ]
                                            ],
                                        ]
                                    ]);
                                    ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-offset-2 col-sm-10">
                                    <hr>
                                    <?= Html::submitButton('แสดงข้อมูล', ['class' => 'btn btn-primary']) ?>  
                                    <?= Html::a('รีเซ็ต',['/app/report/recive-drug'], ['class' => 'btn btn-danger']) ?>
                                </div>
                            </div>
                        <?php ActiveForm::end(); ?>
                        <hr>
                        <?php
                        $title = isset($posted['from_date']) ? 'รายงานระยะเวลารอคอยคิวรับยา'.' ('.Yii::$app->formatter->asDate($posted['from_date'], 'php:d F Y').' - '.Yii::$app->formatter->asDate($posted['to_date'], 'php:d F Y').')' : 'รายงานระยะเวลารอคอยคิวรับยา';
                        echo GridView::widget([
                            'dataProvider'=> $dataProvider,
                            'caption' => $title,
                            'panel' => [
                                'heading' => '<h3 class="panel-title"><i class="glyphicon glyphicon-list"></i> '.$title.'</h3>',
                                'type' => 'success',
                                'before' => '',
                                'after' => '',
                                'footer' => false
                            ],
                            'toolbar' => [
                                '{export}',
                            ],
                            'export' => [
                                'fontAwesome' => true
                            ],
                            'captionOptions' => ['style' => 'text-align: center;font-size:18px;border-bottom: 1px solid #ddd;'],
                            'exportConfig' => [
                                GridView::PDF => [
                                    'label' => 'PDF',
                                    'icon' => 'file-pdf-o',
                                    'iconOptions' => ['class' => 'text-danger'],
                                    'showHeader' => true,
                                    'showPageSummary' => true,
                                    'showFooter' => true,
                                    'showCaption' => true,
                                    'filename' => 'รายงานระยะเวลารอคอยคิวรับยา',
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
                                                [
                                                    'odd' => [
                                                        'L' => [
                                                            'content' => '',
                                                            'font-size' => 8,
                                                            'color' => '#333333',
                                                        ],
                                                        'C' => [
                                                            'content' => '',
                                                            'font-size' => 16,
                                                            'color' => '#333333',
                                                        ],
                                                        'R' => [
                                                            'content' => 'พิมพ์วันที่'. ': ' . Yii::$app->formatter->asDate('now', 'php:d/m/Y'),
                                                            'font-size' => 10,
                                                            'color' => '#333333',
                                                        ],
                                                    ], 
                                                    'even' => [
                                                        'L' => [
                                                            'content' => '',
                                                            'font-size' => 8,
                                                            'color' => '#333333',
                                                        ],
                                                        'C' => [
                                                            'content' => '',
                                                            'font-size' => 16,
                                                            'color' => '#333333',
                                                        ],
                                                        'R' => [
                                                            'content' => 'พิมพ์วันที่'. ': ' . Yii::$app->formatter->asDate('now', 'php:d/m/Y'),
                                                            'font-size' => 10,
                                                            'color' => '#333333',
                                                        ],
                                                    ]
                                                ],
                                            ],
                                            'SetFooter' => [
                                                [
                                                    'odd' => [
                                                        'L' => [
                                                            'content' => 'โรงพยาบาลพิมาย',
                                                            'font-size' => 10,
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
                                                        'line' => false,
                                                    ], 
                                                    'even' => [
                                                        'L' => [
                                                            'content' => 'โรงพยาบาลพิมาย',
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
                                                        'line' => false,
                                                    ]
                                                ],
                                            ],
                                        ],
                                        'options' => [
                                            'title' => '',
                                            'subject' => 'PDF export generated by kartik-v/yii2-grid extension',
                                            'keywords' => 'grid, export, yii2-grid, pdf',
                                        ],
                                        'contentBefore' => '',
                                        'contentAfter' => '',
                                    ]
                                ],
                                GridView::EXCEL => [
                                    'label' => 'Excel',
                                    'icon' => 'file-excel-o',
                                    'iconOptions' => ['class' => 'text-success'],
                                    'showHeader' => true,
                                    'showPageSummary' => true,
                                    'showFooter' => true,
                                    'showCaption' => true,
                                    'filename' => 'รายงานระยะเวลารอคอย',
                                    'alertMsg' => 'The EXCEL export file will be generated for download.',
                                    'options' => ['title' => 'Microsoft Excel 95+'],
                                    'mime' => 'application/vnd.ms-excel',
                                    'config' => [
                                        'worksheet' => 'ExportWorksheet',
                                        'cssFile' => '',
                                    ],
                                ],
                            ],
                            'columns' => [
                                [
                                    'class' => '\kartik\grid\SerialColumn'
                                ],
                                [
                                    'header' => 'วันที่',
                                    'attribute' => 'day',
                                    'hAlign' => 'center',
                                    'format' => ['date','php:d F Y'],
                                    'group' => true,
                                    'groupedRow'=>true,
                                    'contentOptions' => ['style' => 'text-align: left;'],
                                    'groupOddCssClass'=>'kv-grouped-row',
                                    'groupEvenCssClass'=>'kv-grouped-row',
                                    'groupFooter'=>function ($model, $key, $index, $widget) { // Closure method
                                        return [
                                            'content'=>[             // content to show in each summary cell
                                                2=>'Summary',
                                                3=>GridView::F_COUNT,
                                                7=>GridView::F_AVG,
                                                10=>GridView::F_AVG,
                                                11=>GridView::F_AVG,
                                            ],
                                            'contentFormats'=>[      // content reformatting for each summary cell
                                               // 4=>['format'=>'number', 'decimals'=>2],
                                                3=>['format'=>'number', 'decimals'=>0],
                                                7=>['format'=>'number', 'decimals'=>0],
                                                10=>['format'=>'number', 'decimals'=>0],
                                                11=>['format'=>'number', 'decimals'=>0],
                                                //6=>['format'=>'number', 'decimals'=>2],
                                            ],
                                            'contentOptions'=>[      // content html attributes for each summary cell
                                                2=>['style'=>'font-variant:small-caps;text-align:center'],
                                                3=>['style'=>'text-align:center'],
                                                7=>['style'=>'text-align:center'],
                                                10=>['style'=>'text-align:center'],
                                                11=>['style'=>'text-align:center'],
                                                /* 4=>['style'=>'text-align:right'],
                                                5=>['style'=>'text-align:right'],
                                                6=>['style'=>'text-align:right'], */
                                            ],
                                            // html attributes for group summary row
                                            'options'=>['class'=>'success','style'=>'font-weight:bold;']
                                        ];
                                    }
                                ],
                                [
                                    'header' => 'ช่วงเวลา',
                                    'attribute' => 'time_range',
                                    'hAlign' => 'center',
                                    'group' => true,
                                    'pageSummary' => 'Total',
                                ],
                                [
                                    'header' => 'หมายเลขคิว',
                                    'attribute' => 'que_num',
                                    'hAlign' => 'center',
                                    'pageSummary' => true,
                                    'pageSummaryFunc' => GridView::F_COUNT
                                ],
                                [
                                    'header' => 'ประเภทบริการ',
                                    'attribute' => 'service_name',
                                ],
                                [
                                    'header' => 'เวลาออกบัตรคิว',
                                    'attribute' => 'created_at',
                                    'hAlign' => 'center',
                                    'format' => ['date','php:H:i:s'],
                                ],
                                [
                                    'header' => 'เวลาโอนคิว',
                                    'attribute' => 'tb_qtrans_time',
                                    'hAlign' => 'center',
                                    'format' => ['date','php:H:i:s'],
                                ],
                                [
                                    'header' => '<p>เวลาออกบัตรคิว - เวลาโอนคิว</p><small>เวลารอคอย(นาที)</small>',
                                    'attribute' => 't_wait1',
                                    'hAlign' => 'center',
                                    'pageSummary' => true,
                                    'pageSummaryFunc' => GridView::F_AVG,
                                    'format' => ['decimal', 0]
                                ],
                                [
                                    'header' => 'เวลาโอนคิว',
                                    'attribute' => 'tb_qtrans_time',
                                    'hAlign' => 'center',
                                    'format' => ['date','php:H:i:s'],
                                ],
                                [
                                    'header' => 'เวลาเรียก',
                                    'attribute' => 'call_timestp',
                                    'hAlign' => 'center',
                                    'format' => ['date','php:H:i:s'],
                                ],
                                [
                                    'header' => '<p>เวลาโอนคิว - เวลาเรียก</p><small>เวลารอคอย(นาที)</small>',
                                    'attribute' => 't_wait2',
                                    'hAlign' => 'center',
                                    'pageSummary' => true,
                                    'pageSummaryFunc' => GridView::F_AVG,
                                    'format' => ['decimal', 0]
                                ],
                                [
                                    'class' => '\kartik\grid\FormulaColumn',
                                    'header' => 'รวมเวลารอคอย(นาที)',
                                    'hAlign' => 'center',
                                    'pageSummary' => true,
                                    'value' => function ($model, $key, $index, $widget) {
                                        $p = compact('model', 'key', 'index');
                                        // Write your formula below
                                        if(!empty($widget->col(7, $p)) || !empty($widget->col(10, $p))){
                                            return $widget->col(7, $p) + $widget->col(10, $p);
                                        }
                                        return '';
                                    },
                                    'pageSummaryFunc' => GridView::F_AVG,
                                    'format' => ['decimal', 0]
                                ],
                            ],
                            'responsive'=>true,
                            'hover'=>true,
                            'showPageSummary' => true
                        ]);
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
