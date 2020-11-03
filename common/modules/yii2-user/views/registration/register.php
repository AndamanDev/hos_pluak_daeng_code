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
 * @var dektrium\user\models\User $model
 * @var dektrium\user\Module $module
 */

$this->title = Yii::t('user', 'Sign up');
$this->params['breadcrumbs'][] = $this->title;
?>
<div>
    <div>
        <h4 class="logo-name text-center" style="font-size: 100px;">PMH</h4>
    </div>
    <h3 class="text-center"><?= Html::encode($this->title) ?></h3>
    <?php $form = ActiveForm::begin([
        'id' => 'registration-form',
        'enableAjaxValidation' => true,
        'enableClientValidation' => false,
    ]); ?>

    <?= $form->field($model, 'name') ?>

    <?= $form->field($model, 'email') ?>

    <?= $form->field($model, 'username') ?>

    <?php if ($module->enableGeneratingPassword == false): ?>
        <?= $form->field($model, 'password')->passwordInput() ?>
    <?php endif ?>

    <?= Html::submitButton(Yii::t('user', 'Sign up'), ['class' => 'btn btn-success btn-block']) ?>
    <?= Html::a('กลับไปยังหน้าเข้าสู่ระบบ',['/auth/login'],['class' => 'btn btn-default btn-block']); ?>

    <?php ActiveForm::end(); ?>
</div>
