<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\redactor\widgets\Redactor;
use app\models\User;
use app\models\DictData;

/* @var $this yii\web\View */
/* @var $model app\models\Order */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="order-form">

    <?php $form = ActiveForm::begin(); ?>

    <?php if (Yii::$app->user->identity->isAdmin ): ?>
        <?= $form->field($model, 'present_user')->dropDownList(User::getUserList(), ['prompt'=>'请选择', 'style'=>'width:20%']) ?>
    <?php else: ?>
        <?= $form->field($model, 'present_user')->hiddenInput(['value'=>Yii::$app->user->id])->label(false) ?>
    <?php endif; ?>

    <?= $form->field($model, 'system')->dropDownList(DictData::getDickDataListByType('systems'),['prompt'=>'请选择系统', 'style'=>'width:20%']) ?>

    <?= $form->field($model, 'level')->dropDownList(DictData::getDickDataListByType('order_level'),['style'=>'width:20%']) ?>

    <?= $form->field($model, 'title')->input('text', ['placeholder'=>'一句话描述，简明扼要。不超过50字', 'style'=>'width:50%']); ?>

    <?= $form->field($model, 'content')->widget(Redactor::className()) ?>

    <?php if (Yii::$app->user->identity->isAdmin && $model->status == 1): ?>

    <?= $form->field($model, 'solve_user')->dropDownList(User::getUserList(), ['prompt'=>'请选择', 'style'=>'width:20%']) ?>

    <?= $form->field($model, 'classify')->dropDownList(DictData::getDickDataListByType('order_classify'),['prompt'=>'请选择归类', 'style'=>'width:20%']) ?>

    <?= $form->field($model, 'remark')->textarea() ?>

    <?php endif; ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? '提交' : '提交', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
