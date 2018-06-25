<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\date\DatePicker;
use app\models\DictData;
use app\models\Order;

/* @var $this yii\web\View */
/* @var $model app\models\OrderSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="order-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'GET',
    ]); ?>

    <div class="row">
        <div class="col-lg-2">
            <?= $form->field($model, 'title')->input('text', ['placeholder'=>'概述关键字'])->label(false); ?>
        </div>

        <div class="col-lg-4">
            <div class="input-group">
                <?php
                echo DatePicker::widget([
                    'name' => 'OrderSearch[start_at]',
                    'value' => '',
                    'type' => DatePicker::TYPE_RANGE,
                    'name2' => 'OrderSearch[end_at]',
                    'value2' => '',
                    'options' => ['placeholder' => '发起时间-开始日期'],
                    'options2' => ['placeholder' => '发起时间-截止日期'],
                    'pluginOptions' => [
                        'width'=>'200px',
                        'autoclose' => true,
                        'format' => 'yyyy-mm-dd',
                        'todayHighlight' => true
                    ]
                ]);
                echo '<br/>';
                ?>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-2">
            <?= $form->field($model, 'system')->dropDownList(Yii::$app->params['order_systems'],['prompt'=>'选择系统'])->label(false); ?>
        </div>

        <div class="col-lg-2">
            <?php  echo $form->field($model, 'status')->dropDownList(Order::statusList(), ['prompt'=>'是否解决'])->label(false) ?>
        </div>
        <div class="col-lg-2">
            <?php  echo $form->field($model, 'classify')->dropDownList(Yii::$app->params['order_classify'], ['prompt'=>'问题归类'])->label(false) ?>
        </div>
        <?= Html::submitButton('搜索', ['class' => 'btn btn-primary']) ?>
        <?php  //Html::resetButton('重置', ['class' => 'btn btn-default']) ?>
        <?= Html::a('新建工单', ['create'], ['class' => 'btn btn-success' ,'style'=>['margin-right'=>'4%', 'float'=>'right']]) ?>
    </div>

    <?php ActiveForm::end(); ?>
    <br/>

</div>