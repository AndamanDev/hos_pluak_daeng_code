<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\KeyStorageItem */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="key-storage-item-form">

    <?php $form = ActiveForm::begin(['id' => $model->formName()]); ?>

    <?php echo $form->field($model, 'key')->textInput() ?>

    <?php echo $form->field($model, 'value')->textInput() ?>

    <?php echo $form->field($model, 'comment')->textarea() ?>

    <?php if(!Yii::$app->request->isAjax) {?>
    <div class="form-group">
        <?php echo Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>
    <?php } ?>

    <?php ActiveForm::end(); ?>

</div>
