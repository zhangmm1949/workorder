<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kucha\ueditor\UEditor;
use kartik\datetime\DateTimePicker;

/* @var $this yii\web\View */
/* @var $model app\models\Blog */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="blog-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'category_id')->textInput() ?>

    <?= $form->field($model, 'publishAt')->widget(
        DateTimePicker::class,([
        'name' => 'publishAt',
        'value' => empty($model->publish_at) ? '' : $model->publish_at,
        'type' => DateTimePicker::TYPE_INPUT,
        'options' => ['placeholder' => '发布时间'],
        'pluginOptions' => [
            'width'=>'200px',
            'autoclose' => true,
            'format' => 'yyyy-mm-dd H:i:s',
            'todayHighlight' => true,
        ]
    ])
    )->label('设置发布时间');
    ?>

    <?= $form->field($model, 'content')->widget(UEditor::class,['clientOptions' => Yii::$app->params['UEditor_clientOptions']]) ?>

    <?= $form->field($model, 'tag')->textInput(['maxlength' => true])->label('标签') ?>

    <?= $form->field($model, 'display')->dropDownList([1 => '可见', 0 => '不可见'])->label('是否可见') ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
