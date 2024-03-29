<?php
use kartik\widgets\ActiveForm;
use kartik\select2\Select2;
use kartik\widgets\DepDrop;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use frontend\modules\app\models\TbServiceProfile;
use frontend\modules\app\models\TbCounterService;
?>
<?php $form = ActiveForm::begin([
    'type'=>ActiveForm::TYPE_HORIZONTAL,
    'id' => 'form-'.$modelProfile->formName(),
    'formConfig' => [
        'labelSpan' => 6,
        'columns' => 6,
        'deviceSize' => ActiveForm::SIZE_SMALL,
    ],
]); ?>
    
    <div class="row">
        <div class="col-md-6">
            <?= $form->field($modelProfile, 'service_profile_id')->widget(Select2::classname(), [
                'data' => ArrayHelper::map(TbServiceProfile::find()->where(['service_profile_status' => TbServiceProfile::STATUS_ACTIVE])->asArray()->all(),'service_profile_id','service_profile_name'),
                'language' => 'th',
                'options' => ['placeholder' => 'เซอร์วิสโปรไฟล์'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
                'pluginEvents' => [
                    "change" => "function(e) {
                        $('#form-TbServiceProfile').yiiActiveForm('validate', true);
                    }",
                ],
                'theme' => Select2::THEME_BOOTSTRAP,
            ])->label('เซอร์วิสโปรไฟล์',['class' => 'col-sm-4 control-label no-padding-right']); ?>
        </div>
        <div class="col-md-6">
            <?php foreach($services as $item): ?>
                <?= \kartik\helpers\Html::badge($item['service_prefix'].': '.$item['service_name'],['class' => 'badge badge-primary']) ?>
            <?php endforeach; ?>
            <?php /*
            <?= $form->field($modelProfile, 'counter_service_id')->widget(DepDrop::classname(), [
                'data' => ArrayHelper::map(TbCounterService::find()->where(['counter_service_type_id' => $modelProfile['counter_service_type_id']])->asArray()->all(),'counter_service_id','counter_service_name'),
                'type'=>DepDrop::TYPE_SELECT2,
                'options' => ['placeholder' => 'จุดบริการ'],
                'select2Options'=>[
                    'pluginOptions'=>['allowClear'=>true],
                    'theme' => Select2::THEME_BOOTSTRAP,
                    'pluginEvents' => [
                        "change" => "function(e) {
                            $('#form-TbServiceProfile').yiiActiveForm('validate', true);
                        }",
                    ],
                ],
                'pluginOptions'=>[
                    'depends'=>['tbserviceprofile-service_profile_id'],
                    'placeholder'=>'จุดบริการ...',
                    'url'=>Url::to(['/app/calling/child-service-profile']),
                ],
                'pluginEvents' => [
                    "depdrop:afterChange"=>"function(event, id, value) { 
                        //$('#form-TbServiceProfile').yiiActiveForm('validate', true);
                    }",
                ],
            ])->label('จุดบริการ',['class' => 'col-sm-4 control-label no-padding-right']); ?>
            */?>
        </div>
    </div>
<?php ActiveForm::end(); ?>