<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\OrderSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '工单列表';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="order-index">

    <?php  echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
//        'filterModel' => $searchModel,
        'columns' => [
//            'order_sn',
            'id',
            [
                'label' => '发起人',
                'attribute' => 'present_user',
                'value' => 'presenter.user_name',
            ],
            [
                'label'=>'记录时间',
                'attribute' => 'present_time',
                'format' => ['date', 'php:Y/m/d'],
            ],
            [
                'attribute' => 'title',
                'value' => function($data){
                    return Html::a($data->title,'/order/view/?id='.$data->id);
                },
                'options' => [
                    'width' => '20%'
                ],
                'format' => 'raw',
            ],
            [
                'attribute' => 'system',
                'label' => '所属系统',
                'value' => function($data){
                    $systemes = \app\models\System::getAllSystems();
                    return $systemes[$data->system];
                }
            ],
	        [
                'attribute' => 'solve_user',
                'label' => '处理人',
                'value' => function($data){
                    return isset($data->solver->user_name) ? Html::tag('b', $data->solver->user_name, ['style'=>"color: black"]) : '--';
                },
                'format' => 'raw'
            ],
            [
                'attribute' => 'level',
                'label' => '级别',
                'value' => function($data){
                    $level_arr = Yii::$app->params['order_level'];
                    if ($data->level == 1 && !$data->is_solved){
                        return Html::decode('<b style="color: red">P0</b>');
                    }else{
                        return $level_arr[$data->level];
                    }
                },
                'format' => 'raw',
            ],
            [
                'label' => '进度',
                'attribute' => 'status',
                'value' => function($data){
                    switch ($data->status){
                        case 0:
                            return Html::decode('<b style="color: red">未处理</b>');
                            break;
                        case 10:
                            return Html::decode('<b style="color: green">处理中</b>');
                            break;
                        case 20:
                            return Html::decode('<b style="color: black">已完成</b>');
                    }
                },
                'format' => 'raw'
            ],
            [
                'attribute' => 'remark_view',
                'label' => '备注',
                'options' => [
                    'width' => '25%',
                ],
            ],
            [
                'attribute' => 'classify',
                'label' => '问题类型',
                'value' => function($data){
                    if (!$data->is_solved){
                        return '待解决';
                    }else{
                        $arr = Yii::$app->params['order_classify'];
                        return $arr[$data->classify];
                    }
                },
                'format' => 'raw',
            ],
            [
                'label'=>'解决时间',
                'attribute' => 'solve_time',
                'value' => function($data){
                    return $data->is_solved ? date('Y-m-d', $data->solve_time) : '--';
                },
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'header' => '操作',
                'template' => '{view} {update} {delete} {solve}',
                'buttons' => [
                    'solve' => function($url, $model, $key){
                        return $model->is_solved ? '' : Html::a('<span class="glyphicon glyphicon-ok"></span>', $url, ['title' => '处理']);
                    },
                    'delete' => function($url, $model, $key){
                        return Yii::$app->user->identity->isAdmin ? Html::a('<span class="glyphicon glyphicon-trash"></span>',$url, ['title'=>'删除', 'data-method'=>'post', 'data-confirm'=>Yii::t('yii', '确定要删除这条记录吗？')]) : '';
                    }
                ],
                'headerOptions' => ['width' => '10%'],
            ],
        ],
    ]); ?>
</div>
