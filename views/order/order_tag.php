<?php

use yii\grid\GridView;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Order */

$this->title = '工单标签';
$this->params['breadcrumbs'][] = ['label' => 'Orders', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="order-view">

    <h3><?= Html::encode('前10工单标签') ?></h3>

    <hr/>

    <?= GridView::widget([
        'dataProvider' => $model,
        'columns' => [
            [
                'label' => '标签',
                'attribute' => 'tag',
                'sort' => false
            ],
            [
                'label'=>'数量',
                'attribute' => 'num',
            ]
        ],
    ]); ?>
</div>
