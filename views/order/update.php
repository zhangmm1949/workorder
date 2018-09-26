<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Order */

$this->title = '修改工单';
$this->params['breadcrumbs'][] = ['label' => '工单列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = '修改工单';
?>
<div class="order-update">

    <h3><?= Html::encode('编号：' . $model->order_sn) ?></h3>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
