<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\SystemSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '工单所属系统';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="system-index">

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('添加', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'name',
            [
                'attribute' => 'status',
                'label' => '状态',
                'value' => function($data){
                    return $data->status == 1 ? '可用' : '不可用';
                }
            ],
            'sort',
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view} {update}',
            ],
        ],
    ]); ?>
</div>
