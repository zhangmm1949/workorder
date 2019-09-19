<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\User;
use kucha\ueditor\UEditor;
use app\models\System;
use app\models\UserSystem;

/* @var $this yii\web\View */
/* @var $model app\models\Order */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="order-form">

    <?php $form = ActiveForm::begin(); ?>

    <?php if (Yii::$app->user->identity->isAdmin ): ?>
        <?= $form->field($model, 'present_user')->dropDownList(User::getUserList(false,1), ['prompt'=>'请选择', 'style'=>'width:20%']) ?>
    <?php else: ?>
        <?= $form->field($model, 'present_user')->hiddenInput(['value'=>$model->present_user])->label(false) ?>
    <?php endif; ?>

    <?= $form->field($model, 'system')->dropDownList(System::getAllSystems(),['prompt'=>'请选择系统', 'style'=>'width:20%']) ?>

    <?= $form->field($model, 'level')->dropDownList(Yii::$app->params['order_level'],['style'=>'width:20%']) ?>

    <?= $form->field($model, 'title')->input('text', ['placeholder'=>'一句话描述，简明扼要。不超过50字', 'style'=>'width:50%']); ?>

    <?= $form->field($model, 'content')->widget(UEditor::class,['clientOptions' => Yii::$app->params['UEditor_clientOptions']]) ?>

    <?= $form->field($model, 'solve_user')->dropDownList(UserSystem::getUsersBySystem($model->system), ['prompt'=>'请选择', 'style'=>'width:20%'])->label('跟进人') ?>

    <?= $form->field($model, 'status')->dropDownList($model->is_solved  ? Yii::$app->params['order_status'] : array_diff(Yii::$app->params['order_status'],['已完成']), ['style'=>'width:20%'])?>

    <?php if ($model->is_solved): ?>

    <?= $form->field($model, 'classify')->dropDownList(Yii::$app->params['order_classify'],['prompt'=>'请选择归类', 'style'=>'width:20%']) ?>

    <?php endif; ?>

    <?= $form->field($model, 'tags')->input('text') ?>

    <?= $form->field($model, 'remark')->textarea() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? '提交' : '提交', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
