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
            'order_sn',
            [
                'label' => '发起人',
                'attribute' => 'present_user',
                'value' => 'presenter.user_name',
            ],
            [
                'label'=>'发起时间',
                'attribute' => 'present_time',
                'format' => ['date', 'php:Y-m-d'],
            ],
            'title',
            [
                'attribute' => 'system',
                'label' => '所属系统',
                'value' => function($data){
                    $systemes = Yii::$app->params['order_systems'];
                    return $systemes[$data->system];
                }
            ],
            [
                'attribute' => 'level',
                'label' => '级别',
                'value' => function($data){
                    $level_arr = Yii::$app->params['order_level'];
                    return $level_arr[$data->level];
                }
            ],
            [
                'label' => '是否完成',
                'attribute' => 'status',
                'value' => function($data){
                    return $data->status == 1 ? '完成' : '未完成';
                },
            ],
            [
                'attribute' => 'solve_user',
                'value' => function($data){
                    return $data->status == 1 ? $data->solver->user_name : '--';
                }
            ],
            [
                'label'=>'解决时间',
                'attribute' => 'solve_time',
                'value' => function($data){
                    return $data->status == 1 ? date('Y-m-d', $data->solve_time) : '--';
                },
            ],
            [
                 'attribute' => 'classify',
                 'label' => '问题类型',
                 'value' => function($data){
                        if ($data->status !== 1){
                            return '待解决';
                        }else{
                            $arr = Yii::$app->params['order_classify'];
                            return $arr[$data->classify];
                        }
                 }
            ],
            // 'remark',

            [
                'class' => 'yii\grid\ActionColumn',
                'header' => '操作',
                'template' => '{view} {update} {delete} {solve}',
                'buttons' => [
                    'solve' => function($url, $model, $key){
                        return $model->status == 1 ? '' : Html::a('<span class="glyphicon glyphicon-ok"></span>', $url, ['title' => '处理']);
                    }
                ],
                'headerOptions' => ['width' => '10%'],
            ],
        ],
    ]); ?>
</div>
