<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\System;


/* @var $this yii\web\View */
/* @var $model app\models\User */

$this->title = '新建用户';
$this->params['breadcrumbs'][] = ['label' => '用户列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-create">

    <?php $form = ActiveForm::begin(['id' => 'login-form', 'enableClientValidation' => false]); ?>
    <div class="row">
        <div class="col-lg-3">
        <?= $form
            ->field($model, 'user_name')
            ->label(false)
            ->textInput(['placeholder' => '用户名']) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-3">
        <?= $form
            ->field($model, 'email')
            ->label(false)
            ->textInput(['placeholder' => $model->getAttributeLabel('email')]) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-3">
        <?= $form->field($model, 'password')
            ->label(false)
            ->passwordInput(['placeholder' => '密码']) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-3">
        <?= $form
            ->field($model, 're_password')
            ->label(false)
            ->passwordInput(['placeholder' => '请重复输入密码']) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-3">
            <?= $form
                ->field($model, 'department_id')
                ->label(false)
                ->dropDownList(Yii::$app->params['user_department']) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-3">
            <?= $form
                ->field($model, 'user_systems')
                ->label('关联系统')
                ->checkboxList(System::getUsableSystems()) ?>
        </div>
    </div>

    <div class="row">
        <!-- /.col -->
        <div class="col-xs-4">
            <?= Html::submitButton('提交', ['class' => 'btn btn-success', 'name' => 'login-button']) ?>
        </div>
        <!-- /.col -->
    </div>


    <?php ActiveForm::end(); ?>

</div>
