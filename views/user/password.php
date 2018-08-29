<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\PasswordForm */
/* @var $id */
/* @var $form yii\widgets\ActiveForm */

$this->title = '修改密码: ';
$this->params['breadcrumbs'][] = ['label' => 'Users', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Password';
?>

<div class="user-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'user_id')->hiddenInput(['value'=>$id])->label(false); ?>

    <?= $form->field($model, 'password')->passwordInput()->label('原密码') ?>

    <?= $form->field($model, 'new_password')->passwordInput()->label('请输入新密码') ?>

    <?= $form->field($model, 're_new_password')->passwordInput()->label('再次输入新密码') ?>

    <div class="form-group">
        <?= Html::submitButton('提交', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
