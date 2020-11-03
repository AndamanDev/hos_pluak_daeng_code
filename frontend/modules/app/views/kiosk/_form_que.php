<?php
use kartik\widgets\ActiveForm;
use yii\helpers\Html;
use kartik\icons\Icon;
use kartik\select2\Select2;
use kartik\widgets\DepDrop;
use yii\helpers\ArrayHelper;
use frontend\modules\app\models\TbService;
use frontend\modules\app\models\TbServiceGroup;
use yii\helpers\Url;

$this->registerCss(<<<CSS
.select2-dropdown {
    z-index: 2100;
}
CSS
);
?>
<?php $form = ActiveForm::begin(['type'=>ActiveForm::TYPE_HORIZONTAL,'id' => 'form-que']); ?>
    <div class="form-group">
        <?= Html::activeLabel($model, 'service_group_id', ['class'=>'col-sm-2 control-label']) ?>
        <div class="col-sm-6">
            <?= $form->field($model, 'service_group_id',['showLabels'=>false])->widget(Select2::classname(), [
                'data' => ArrayHelper::map(TbServiceGroup::find()->asArray()->all(),'service_group_id','service_group_name'),
                'language' => 'th',
                'options' => ['placeholder' => 'ประเภทเสียง'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
                'theme' => Select2::THEME_BOOTSTRAP,
            ]); ?>
        </div>
    </div>
    <div class="form-group">
        <?= Html::activeLabel($model, 'service_id', ['class'=>'col-sm-2 control-label']) ?>
        <div class="col-sm-6">
            <?= $form->field($model, 'service_id',['showLabels'=>false])->widget(DepDrop::classname(), [
                'data' => ArrayHelper::map(TbService::find()->where(['service_group_id' => $model['service_group_id']])->asArray()->all(),'service_id','service_name'),
                'type'=>DepDrop::TYPE_SELECT2,
                'select2Options'=>['pluginOptions'=>['allowClear'=>true],'theme' => Select2::THEME_BOOTSTRAP],
                'pluginOptions'=>[
                    'depends'=>['tbque-service_group_id'],
                    'placeholder'=>'Select...',
                    'url'=>Url::to(['/app/kiosk/child-service-group'])
                ]
            ]); ?>
        </div>
    </div>
    <div class="form-group">
        <div class="col-sm-8" style="text-align: right;">
            <?= Html::button(Icon::show('close').'ปิดหน้าต่าง',['class' => 'btn btn-default','data-dismiss' => 'modal']); ?>
            <?= Html::submitButton(Icon::show('save').'บันทึก',['class' => 'btn btn-primary']); ?>
        </div>
    </div>
<?php ActiveForm::end(); ?>

<?php
$this->registerJs(<<<JS
//Form Event
var table = $('#tb-que-list').DataTable();
var \$form = $('#form-que');
\$form.on('beforeSubmit', function() {
    var data = new FormData($(\$form)[0]);//\$form.serialize();
    var \$btn = $('button[type="submit"]').button('loading');//loading btn
    \$.ajax({
        url: \$form.attr('action'),
        type: 'POST',
        data: data,
        async: false,
        processData: false,
        contentType: false,
        success: function (data) {
            if(data.status == '200'){
                $('#ajaxCrudModal').modal('hide');//hide modal
                table.ajax.reload();//reload table
                swal({//alert completed!
                    type: 'success',
                    title: 'บันทึกสำเร็จ!',
                    showConfirmButton: false,
                    timer: 1500
                });
                setTimeout(function(){ \$btn.button('reset'); }, 1500);//clear button loading
            }else if(data.validate != null){
                $.each(data.validate, function(key, val) {
                    $(\$form).yiiActiveForm('updateAttribute', key, [val]);
                });
            }
            \$btn.button('reset');
        },
        error: function(jqXHR, errMsg) {
            swal('Oops...',errMsg,'error');
            \$btn.button('reset');
        }
    });
    return false; // prevent default submit
});
JS
);
?>