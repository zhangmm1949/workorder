<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Order */

$this->title = '工单详情';
$this->params['breadcrumbs'][] = ['label' => 'Orders', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="order-view">

    <h3><?= Html::encode('编号：' . $model->order_sn) ?></h3>

    <hr/>
    <p>
        <?= Html::a('更新', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('处理', ['solve', 'id' => $model->id], ['class' => 'btn btn-danger']) ?>
        <?= Html::a('PDF', ['pdf', 'id' => $model->id], ['class' => 'btn btn-danger']) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'order_sn',
            'title',
            ['attribute' => 'presenter.user_name','label'=>'发起人'],
            ['attribute' => 'present_time', 'format'=>['date', 'php:Y-m-d H:is']],
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
                'attribute' => 'content',
                'format' => 'html'
            ],
            ['attribute' => 'tags'],
            ['attribute' => 'status','value'=> function($data){
                $status = Yii::$app->params['order_status'];
                return $status[$data->status];
            }],
            ['attribute' => 'solver.user_name','label'=>'解决人','value'=>function($data){
                return isset($data->solver->user_name) ? $data->solver->user_name : '--';
            }],
            ['attribute'=>'solve_time', 'value'=>function($data){
                return $data->is_solved ? date('Y-m-d H:i:s', $data->solve_time) : '--';
            }],
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
                }
            ],
            'remark',
        ],
    ]) ?>
</div>
