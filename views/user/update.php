<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\System;

/* @var $this yii\web\View */
/* @var $model app\models\User */

$this->title = '编辑用户: ' . $model->user_name;
$this->params['breadcrumbs'][] = ['label' => 'Users', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->user_name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="user-update">

    <br/>

    <div class="user-form">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'user_name')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'tel')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'department_id')->dropDownList(Yii::$app->params['user_department']) ?>

        <?= $form->field($model, 'status')->radioList(Yii::$app->params['user_status']) ?>

        <div class="row">
            <div class="col-lg-3">
                <?= $form
                    ->field($model, 'systems')
                    ->label('关联系统')
                    ->checkboxList(System::getAllSystems()) ?>
            </div>
        </div>

        <div class="form-group">
            <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>
