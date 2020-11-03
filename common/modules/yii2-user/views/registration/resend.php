<?php

/*
 * This file is part of the Dektrium project.
 *
 * (c) Dektrium project <http://github.com/dektrium>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
 * @var yii\web\View $this
 * @var dektrium\user\models\ResendForm $model
 */

$this->title = Yii::t('user', 'Request new confirmation message');
$this->params['breadcrumbs'][] = $this->title;
?>
<div>
    <div>
        <h4 class="logo-name text-center" style="font-size: 100px;">PMH</h4>
    </div>
    <h3 class="text-center"><?= Html::encode($this->title) ?></h3>
    <?php $form = ActiveForm::begin([
        'id' => 'resend-form',
        'enableAjaxValidation' => true,
        'enableClientValidation' => false,
    ]); ?>

    <?= $form->field($model, 'email')->textInput(['autofocus' => true]) ?>

    <?= Html::submitButton(Yii::t('user', 'Continue'), ['class' => 'btn btn-primary btn-block']) ?>
    <?= Html::a('กลับไปยังหน้าเข้าสู่ระบบ',['/auth/login'],['class' => 'btn btn-default btn-block']); ?>
    <br>

    <?php ActiveForm::end(); ?>
</div>
