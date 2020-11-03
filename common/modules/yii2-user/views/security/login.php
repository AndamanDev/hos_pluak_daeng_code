<?php

/*
 * This file is part of the Dektrium project.
 *
 * (c) Dektrium project <http://github.com/dektrium>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

use dektrium\user\widgets\Connect;
use dektrium\user\models\LoginForm;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
 * @var yii\web\View $this
 * @var dektrium\user\models\LoginForm $model
 * @var dektrium\user\Module $module
 */

$this->title = Yii::t('user', 'Sign in');
$this->params['breadcrumbs'][] = $this->title;
?>

<?= $this->render('/_alert', ['module' => Yii::$app->getModule('user')]) ?>
<div>
    <div>
        <h4 class="logo-name text-center" style="font-size: 100px;">PMH</h4>
    </div>
    <h3 class="text-center"><?= Yii::$app->name ?></h3>
    <?php $form = ActiveForm::begin([
        'id' => 'login-form',
        'enableAjaxValidation' => true,
        'enableClientValidation' => false,
        'validateOnBlur' => false,
        'validateOnType' => false,
        'validateOnChange' => false,
        'options' => ['class' => 'm-t','autocomplete' => 'off']
    ]) ?>

    <?php if ($module->debug): ?>
        <?= $form->field($model, 'login', [
            'inputOptions' => [
                'autofocus' => 'autofocus',
                'class' => 'form-control',
                'tabindex' => '1',
                'placeholder' => $model->getAttributeLabel('login'),
            ]])->dropDownList(LoginForm::loginList());
        ?>

    <?php else: ?>

        <?= $form->field($model, 'login',
            ['inputOptions' => ['autofocus' => 'autofocus', 'class' => 'form-control', 'tabindex' => '1','placeholder' => 'ชื่อผู้ใช้งาน หรือ อีเมล']]
        )->label(false);
        ?>

    <?php endif ?>

    <?php if ($module->debug): ?>
        <div class="alert alert-warning">
            <?= Yii::t('user', 'Password is not necessary because the module is in DEBUG mode.'); ?>
        </div>
    <?php else: ?>
        <?= $form->field(
            $model,
            'password',
            ['inputOptions' => ['class' => 'form-control', 'tabindex' => '2','placeholder' => 'รหัสผ่าน']])
            ->passwordInput()
            ->label(false) ?>
    <?php endif ?>

    <?= $form->field($model, 'rememberMe')->checkbox(['tabindex' => '3']) ?>

    <?= Html::submitButton(
        Yii::t('user', 'Sign in'),
        ['class' => 'btn btn-primary btn-block', 'tabindex' => '4']
    ) ?>

    <p class="text-center">
        <?= ($module->enablePasswordRecovery ? ' (' . Html::a(Yii::t('user', 'Forgot password?'),['/user/recovery/request'],['tabindex' => '5']). ')' : '') ?>
    </p>

    <?php ActiveForm::end(); ?>

    <?php if ($module->enableConfirmation): ?>
        <p class="text-center">
            <?= Html::a(Yii::t('user', 'Didn\'t receive confirmation message?'), ['/user/registration/resend']) ?>
        </p>
    <?php endif ?>
    <?php if ($module->enableRegistration): ?>
        <p class="text-center">
            <?= Html::a(Yii::t('user', 'Don\'t have an account? Sign up!'), ['/user/registration/register']) ?>
        </p>
    <?php endif ?>
    <?= Connect::widget([
        'baseAuthUrl' => ['/user/security/auth'],
    ]) ?>
    <p class="m-t text-center"> <small>All right reserved &copy; <?= date('Y') ?> by MComScience</small> </p>
</div>
