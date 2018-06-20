<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \app\models\RegisterForm */

$this->title = '新用户注册';

$fieldOptions1 = [
    'options' => ['class' => 'form-group has-feedback'],
    'inputTemplate' => "{input}<span class='glyphicon glyphicon-envelope form-control-feedback'></span>"
];

$fieldOptions2 = [
    'options' => ['class' => 'form-group has-feedback'],
    'inputTemplate' => "{input}<span class='glyphicon glyphicon-lock form-control-feedback'></span>"
];

$fieldOptions3 = [
    'options' => ['class' => 'form-group has-feedback'],
    'inputTemplate' => "{input}<span class='glyphicon glyphicon-user form-control-feedback'></span>"
];
?>

<div class="login-box">
    <div class="login-logo">
        <b>自用工单记录系统</b>
    </div>
    <!-- /.login-logo -->
    <div class="login-box-body">
        <p class="login-box-msg">注册新用户</p>

        <?php $form = ActiveForm::begin(['id' => 'login-form', 'enableClientValidation' => false]); ?>

        <?= $form
            ->field($model, 'user_name', $fieldOptions3)
            ->label(false)
            ->textInput(['placeholder' => '用户名']) ?>

        <?= $form
            ->field($model, 'email', $fieldOptions1)
            ->label(false)
            ->textInput(['placeholder' => $model->getAttributeLabel('email')]) ?>

        <?= $form->field($model, 'password', $fieldOptions2)
            ->label(false)
            ->passwordInput(['placeholder' => '密码']) ?>

        <?= $form
            ->field($model, 're_password', $fieldOptions2)
            ->label(false)
            ->passwordInput(['placeholder' => '请重复输入密码']) ?>

        <div class="row">
            <!-- /.col -->
            <div class="col-xs-4">
                <?= Html::submitButton('注册', ['class' => 'btn btn-primary btn-block btn-flat', 'name' => 'login-button']) ?>
            </div>
            <!-- /.col -->
        </div>


        <?php ActiveForm::end(); ?>

    </div>
    <!-- /.login-box-body -->
</div><!-- /.login-box -->
