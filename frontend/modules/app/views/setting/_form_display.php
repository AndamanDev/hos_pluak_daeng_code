<?php
use yii\helpers\Html;
use kartik\form\ActiveForm;
use kartik\widgets\ColorInput;
use kartik\icons\Icon;
use inspinia\ckeditor\CKEditor;
use inspinia\widgets\Table;
use yii\web\JsExpression;
use frontend\assets\SocketIOAsset;
SocketIOAsset::register($this);

$this->title = 'บันทึกจอแสดงผล';
$this->params['breadcrumbs'][] = ['label' => 'ตั้งค่า', 'url' => ['/app/setting/display']];
$this->params['breadcrumbs'][] = ['label' => 'ระบบคิว', 'url' => ['/app/setting/display']];
$this->params['breadcrumbs'][] = 'จอแสดงผล';

$this->registerCss(<<<CSS
/* .form-horizontal .radio,
.form-horizontal .checkbox,
.form-horizontal .radio-inline,
.form-horizontal .checkbox-inline {
    display: inline-block;
} */
/* Small devices (tablets, 768px and up) */

@media (min-width: 768px) {
    table#tb-display thead tr th {
        text-align: center;
        font-size: 30px;
    }
    table#tb-display tbody tr td {
        text-align: center;
        font-size: 40px;
    }
    table#tb-hold tbody tr td {
        text-align: center;
        font-size: 30px;
    }
    table#tb-que-wait tbody tr td {
        text-align: center;
        font-size: 30px;
    }
}

/* Medium devices (desktops, 992px and up) */

@media (min-width: 992px) {
    table#tb-display thead tr th {
        text-align: center;
        font-size: 30px;
    }
    table#tb-display tbody tr td {
        text-align: center;
        font-size: 40px;
    }
    table#tb-hold tbody tr td {
        text-align: center;
        font-size: 30px;
    }
    table#tb-que-wait tbody tr td {
        text-align: center;
        font-size: 30px;
    }
}

/* Large devices (large desktops, 1200px and up) */

@media (min-width: 1920px) {
    table#tb-display thead tr th {
        text-align: center;
        font-size: 50px;
    }
    table#tb-display tbody tr td {
        text-align: center;
        font-size: 60px;
    }
    table#tb-hold tbody tr td {
        text-align: center;
        font-size: 50px;
    }
    table#tb-que-wait tbody tr td {
        text-align: center;
        font-size: 50px;
    }
}

table {
    border-spacing: 5px;
    border-collapse: unset;
    border-spacing: 0 10px;
}

.table>thead>tr>th {
    vertical-align: bottom;
    border-bottom: 0px;
}

table#tb-hold tbody tr td {
    border-top: 0px;
}

table#tb-display {
    margin-top: 0px !important;
    margin-bottom: 0px !important;
}

table#tb-que-wait {
    border-spacing: 5px;
    border-collapse: unset;
    border-spacing: 0 0px;
    margin-top: 0px !important;
    margin-bottom: 0px !important;
}

table#tb-que-wait tbody tr td {
    border-top: 0px;
}

.table>tbody>tr>td{
    padding: 0px;
}
CSS
);
$this->registerCss($style);
?>
<?= \inspinia\sweetalert2\Alert::widget(['useSessionFlash' => true]) ?>
<div class="ibox float-e-margins">
    <div class="ibox-title">
        <h5><?= $this->title; ?></h5>
    </div>
    <div class="ibox-content" style="border-style: outset;">
        <?php $form = ActiveForm::begin(['type'=>ActiveForm::TYPE_HORIZONTAL,'id' => 'form-display']); ?>
            <div class="form-group">
                <?= Html::activeLabel($model, 'display_name', ['label'=>'ชื่อจอแสดงผล', 'class'=>'col-sm-2 control-label']) ?>
                <div class="col-sm-3">
                    <?= $form->field($model, 'display_name',['showLabels'=>false])->textInput(['placeholder'=>'ชื่อจอแสดงผล']); ?>
                </div>

                <?= Html::activeLabel($model, 'page_length', ['label'=>'จำนวนแถวที่แสดง', 'class'=>'col-sm-2 control-label']) ?>
                <div class="col-sm-3">
                    <?= $form->field($model, 'page_length',['showLabels'=>false])->textInput(['placeholder'=>'จำนวนแถวที่แสดง']); ?>
                </div>
            </div>
            <div class="form-group">
                <?= Html::activeLabel($model, 'que_column_length', ['class'=>'col-sm-2 control-label']) ?>
                <div class="col-sm-3">
                    <?= $form->field($model, 'que_column_length',['showLabels'=>false])->textInput(['placeholder'=>'']); ?>
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-8">
                    <span class="badge badge-danger">ส่วนหัวตาราง</span>
                </div>
            </div>
            <div class="form-group">
                <?= Html::activeLabel($model, 'text_th_left', ['label'=>'ข้อความส่วนหัวตาราง 1', 'class'=>'col-sm-2 control-label']) ?>
                <div class="col-sm-3">
                    <?= $form->field($model, 'text_th_left',['showLabels'=>false])->textInput(['placeholder'=>'เช่น หมายเลขช่อง']); ?>
                </div>

                <?= Html::activeLabel($model, 'text_th_right', ['label'=>'ข้อความส่วนหัวตาราง 2', 'class'=>'col-sm-2 control-label']) ?>
                <div class="col-sm-3">
                    <?= $form->field($model, 'text_th_right',['showLabels'=>false])->textInput(['placeholder'=>'เช่น ช่อง,ห้อง,โต๊ะ']); ?>
                </div>
            </div>
            <div class="form-group">
                <?= Html::activeLabel($model, 'text_hold', ['label'=>'ข้อความตารางพักคิว', 'class'=>'col-sm-2 control-label']) ?>
                <div class="col-sm-3">
                    <?= $form->field($model, 'text_hold',['showLabels'=>false])->textInput(['placeholder'=>'เช่น คิวที่เรียกไปแล้ว']); ?>
                </div>
            </div>
            <div class="form-group" style="display: none;">
                <?= Html::activeLabel($model, 'color_th_left', ['label'=>'สีตัวอักษรข้อความ 1', 'class'=>'col-sm-2 control-label']) ?>
                <div class="col-sm-3">
                    <?= $form->field($model, 'color_th_left',['showLabels'=>false])->widget(ColorInput::classname(), [
                        'options' => ['placeholder' => 'Select color ...'],
                    ]); ?>
                </div>

                <?= Html::activeLabel($model, 'color_th_right', ['label'=>'สีตัวอักษรข้อความ 2', 'class'=>'col-sm-2 control-label']) ?>
                <div class="col-sm-3">
                    <?= $form->field($model, 'color_th_right',['showLabels'=>false])->widget(ColorInput::classname(), [
                        'options' => ['placeholder' => 'Select color ...'],
                    ]); ?>
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-8">
                    <span class="badge badge-danger">สีพื้นหลัง</span>
                </div>
            </div>
            <div class="form-group">
                <?= Html::activeLabel($model, 'background_color', ['label'=>'สีพื้นหลังหน้าจอ', 'class'=>'col-sm-2 control-label']) ?>
                <div class="col-sm-3">
                    <?= $form->field($model, 'background_color',['showLabels'=>false])->widget(ColorInput::classname(), [
                        'options' => ['placeholder' => 'Select color ...'],
                    ]); ?>
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-8">
                    <span class="badge badge-danger">CSS</span>
                </div>
            </div>
            <div class="form-group">
                <?= Html::activeLabel($model, 'color_code', ['label'=>'โค้ดสี', 'class'=>'col-sm-2 control-label']) ?>
                <div class="col-sm-3">
                    <?= $form->field($model, 'color_code',['showLabels'=>false])->widget(ColorInput::classname(), [
                        'options' => ['placeholder' => 'Select color ...'],
                    ]); ?>
                </div>
            </div>
            <div class="form-group">
                <?= Html::activeLabel($model, 'display_css', ['label'=>'CSS','class'=>'col-sm-2 control-label']) ?>
                <div class="col-sm-8">
                    <?= $form->field($model, 'display_css',['showLabels'=>false])->widget(CKEditor::classname(),[
                        'options' => ['rows' => 12],
                        'preset' => 'custom',
                        'clientOptions' => [
                            'extraPlugins' => 'font,justify,pbckcode,preview,autogrow',
                            'toolbarGroups' => [
                                ['groups' => ['mode']],
                                //['name' => 'document','groups' => ['mode' ]],
                                ['name' => 'pbckcode',['modes' => [['HTML', 'html'], ['CSS', 'css'], ['PHP', 'php'], ['JS', 'javascript']],]],
                                ['name' => 'clipboard','groups' => ['clipboard','undo']],
                            ],
                        ]
                    ]); ?>
                </div>
            </div>
            
            <div class="form-group">
                <?= Html::activeLabel($model, 'counter_service_id', ['class'=>'col-sm-2 control-label']) ?>
                <div class="col-sm-10">
                    <?= $form->field($model, 'counter_service_id',['showLabels'=>false])->checkBoxList($model->counterServiceData,[
                        'inline'=>false,
                        'item' => function($index, $label, $name, $checked, $value) {

                            $return = '<div class="checkbox"><label style="font-size: 1em">';
                            $return .= Html::checkbox( $name, $checked,['value' => $value]);
                            $return .= '<span class="cr"><i class="cr-icon cr-icon glyphicon glyphicon-ok"></i></span>' . ucwords($label);
                            $return .= '</label></div>';

                            return $return;
                        }
                    ]); ?>
                </div>
            </div>
            <div class="form-group">
                <?= Html::activeLabel($model, 'service_id', ['class'=>'col-sm-2 control-label']) ?>
                <div class="col-sm-10">
                    <?= $form->field($model, 'service_id',['showLabels'=>false])->checkBoxList($model->serviceData,[
                        'inline'=>false,
                        'item' => function($index, $label, $name, $checked, $value) {

                            $return = '<div class="checkbox"><label style="font-size: 1em">';
                            $return .= Html::checkbox( $name, $checked,['value' => $value]);
                            $return .= '<span class="cr"><i class="cr-icon cr-icon glyphicon glyphicon-ok"></i></span>' . ucwords($label);
                            $return .= '</label></div>';

                            return $return;
                        }
                    ]); ?>
                </div>
            </div>
            <div class="form-group">
                <?= Html::activeLabel($model, 'show_que_waitdrug', ['class'=>'col-sm-2 control-label']) ?>
                <div class="col-sm-4">
                    <?= $form->field($model, 'show_que_waitdrug',['showLabels'=>false])->RadioList(
                        [0 => 'ไม่แสดง', 1 => 'แสดง'],[
                        'inline'=>true,
                        'item' => function($index, $label, $name, $checked, $value) {

                            $return = '<div class="radio"><label style="font-size: 1em">';
                            $return .= Html::radio( $name, $checked,['value' => $value]);
                            $return .= '<span class="cr"><i class="cr-icon fa fa-circle"></i></span>' . ucwords($label);
                            $return .= '</label></div>';

                            return $return;
                        }
                    ]); ?>
                </div>
            </div>
            <div class="form-group">
                <?= Html::activeLabel($model, 'display_status', ['class'=>'col-sm-2 control-label']) ?>
                <div class="col-sm-4">
                    <?= $form->field($model, 'display_status',['showLabels'=>false])->RadioList(
                        [0 => 'ปิดใช้งาน', 1 => 'เปิดใช้งาน'],[
                        'inline'=>true,
                        'item' => function($index, $label, $name, $checked, $value) {

                            $return = '<div class="radio"><label style="font-size: 1em">';
                            $return .= Html::radio( $name, $checked,['value' => $value]);
                            $return .= '<span class="cr"><i class="cr-icon fa fa-circle"></i></span>' . ucwords($label);
                            $return .= '</label></div>';

                            return $return;
                        }
                    ]); ?>
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-8 col-sm-offset-2">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h3 class="panel-title">ตัวอย่างจอแสดงผล</h3>
                        </div>
                        <div class="panel-body" style="background-color: <?= $model->isNewRecord && empty($model['background_color']) ? '#204d74' : $model['background_color']; ?>;">
                            <?php
                                echo Table::widget([
                                    'tableOptions' => ['class' => 'table','id' => 'tb-display'],
                                    'beforeHeader' => [
                                        [
                                            'columns' => [
                                                ['content' => ($model->isNewRecord ? 'หมายเลข' : $model['text_th_left']), 'options' => ['style' => 'text-align: center;width: 50%;','class' => 'th-left']],
                                                ['content' => ($model->isNewRecord ? 'ช่อง' : $model['text_th_right']), 'options' => ['style' => 'text-align: center;width: 50%;','class' => 'th-right']],
                                            ],
                                        ],
                                    ],
                                    'columns' => [
                                        [
                                            ['content' => 'A001', 'options' => ['class' => 'td-left']],
                                            ['content' => '1', 'options' => ['class' => 'td-right']],
                                        ],
                                        [
                                            ['content' => 'A002', 'options' => ['class' => 'td-left']],
                                            ['content' => '2', 'options' => ['class' => 'td-right']],
                                        ],
                                        [
                                            ['content' => 'A003', 'options' => ['class' => 'td-left']],
                                            ['content' => '3', 'options' => ['class' => 'td-right']],
                                        ],
                                    ],
                                ]);
                            ?>

                            <?php
                            if($model['show_que_waitdrug'] == 1){
                                echo Table::widget([
                                    'tableOptions' => ['class' => 'table','id' => 'tb-que-wait'],
                                    'columns' => [
                                        [
                                            ['content' => '<div class="ribbon ribbon-right ribbon-shadow ribbon-border-dash ribbon-round ribbon-wait uppercase" style="width:100%;padding: 0.2em 1em;">
                                            คิวรอยานาน
                                            </div>', 'options' => ['style' => 'width: 40%;']],
                                            ['content' => '-', 'options' => ['class' => 'td-right','style' => 'width: 60%;']],
                                        ],
                                    ],
                                    'datatableOptions' => [
                                        "clientOptions" => [
                                            "dom" => "t",
                                            "responsive" => true,
                                            "autoWidth" => false,
                                            "deferRender" => true,
                                            "ordering" => false,
                                            "pageLength" => 1,
                                            "columns" => [
                                                ["data" => "text","defaultContent" => '<div class="ribbon ribbon-right ribbon-shadow ribbon-border-dash ribbon-round ribbon-wait uppercase" style="width:100%;padding: 0.2em 1em;">
                                                คิวรอยานาน
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
                            }
                            ?>

                            <?php
                                echo Table::widget([
                                    'tableOptions' => ['class' => 'table','id' => 'tb-hold'],
                                    'columns' => [
                                        [
                                            ['content' => ($model->isNewRecord ? '<div class="ribbon ribbon-right ribbon-shadow ribbon-border-dash ribbon-round ribbon-hold uppercase" style="width:100%;padding: 0.2em 1em;">
                                            คิวที่เรียกไปแล้ว
                                            </div>' : '<div class="ribbon ribbon-right ribbon-shadow ribbon-border-dash ribbon-round ribbon-hold uppercase" style="width:100%;padding: 0.2em 1em;">
                                            '.$model['text_hold'].'
                                            </div>'), 'options' => ['class' => '','style' => 'width: 40%;']],
                                            ['content' => '<marquee>A004 | A005</marquee>', 'options' => ['class' => 'td-right','style' => 'width: 60%;']],
                                        ],
                                    ],
                                    'datatableOptions' => [
                                        "clientOptions" => [
                                            "dom" => "t",
                                            "responsive" => true,
                                            "autoWidth" => false,
                                            "deferRender" => true,
                                            "ordering" => false,
                                            "pageLength" => 1,
                                            "columns" => [
                                                ["data" => "text","defaultContent" => '<div class="ribbon ribbon-right ribbon-shadow ribbon-border-dash ribbon-round ribbon-hold uppercase" style="width:100%;padding: 0.2em 1em;">
                                                '.$model['text_hold'].'
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
            </div>
            <div class="form-group">
                <div class="col-sm-offset-2 col-sm-10">
                    <?= Html::a(Icon::show('close').' ปิดหน้าต่าง',['/app/setting/display'],['class' => 'btn btn-default']); ?>
                    <?php if(!$model->isNewRecord): ?>
                        <?= Html::a(Icon::show('refresh').' รีเซ็ต',['/app/setting/update-display','id' => $model['display_ids']],['class' => 'btn btn-danger']); ?>
                    <?php endif; ?>
                    <?= Html::submitButton(Icon::show('desktop').'แสดงตัวอย่าง', ['class' => 'btn btn-success']) ?>
                    <?= Html::button(Icon::show('save').'บันทึก', ['class' => 'btn btn-primary activity-save']) ?>
                </div>
            </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>
<?php
$this->registerJS(<<<JS
$('button.activity-save').on('click',function(){
    var \$form = $('#form-display');
    var data = new FormData($(\$form)[0]);//\$form.serialize();
    \$.ajax({
        url: \$form.attr('action'),
        type: 'POST',
        data: data,
        async: false,
        processData: false,
        contentType: false,
        success: function (res) {
            if(res.validate != null){
                $.each(res.validate, function(key, val) {
                    $(\$form).yiiActiveForm('updateAttribute', key, [val]);
                });
            }else{
                socket.emit('update-display', res);
                swal({//alert completed!
                    type: 'success',
                    title: 'บันทึกสำเร็จ!',
                    showConfirmButton: false,
                    timer: 1500
                });
                window.location.href = res.url;
            }
        },
        error: function(jqXHR, errMsg) {
            swal('Oops...',errMsg,'error');
        }
    });
});
JS
);
?>