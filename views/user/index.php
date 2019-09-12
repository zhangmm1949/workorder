<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\UserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '用户列表';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-index">
    <p>
        <?= Html::a('新建用户', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [


            'id',
            'user_name',
//            'auth_key',
//            'password',
            'email:email',
             'tel',
            [
                'attribute' => 'department_id',
                'value' => function($data){
                    return Yii::$app->params['user_department'][$data->department_id];
                }
            ],
            [
                'attribute' => 'status',
                'value' => function($data){
                    return $data->status == 1 ? '可用' : '不可用';
                }
            ],
//             'created_at',

            [
                'class' => 'yii\grid\ActionColumn',
                'header' => '操作',
                'template' => '{view} {update} {update-status}',#此处括号之间的空格可使页面图标稍稍隔开
                'buttons' => [
                    'update-status' => function($url, $model, $key){
                        return Yii::$app->user->identity->isAdmin ? Html::a('<span class="glyphicon glyphicon-refresh"></span>',$url, ['title'=>'更改用户状态', 'data-method'=>'post', 'data-confirm'=>Yii::t('yii', '确定要修改用户状态吗？')]) : '';
                    }
                ],
                'headerOptions' => ['width' => '10%'],

            ],
        ],
    ]); ?>
</div>
