<?php
/**
 * Created by PhpStorm.
 * User: zhangmm
 * Date: 2017/12/2
 * Time: 17:10
 */
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\User;
use yii\redactor\widgets\Redactor;
use yii\widgets\DetailView;
use app\models\DictData;

    /* @var $this yii\web\View */
/* @var $model app\models\Order */

$this->title = '处理工单';
$this->params['breadcrumbs'][] = ['label' => '工单列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = '处理工单';
?>
<div class="order-update">

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
                    $systemes = \app\models\DictData::getDickDataListByType('systems');
                    return $systemes[$data->system];
                }
            ],
            [
                'attribute' => 'level',
                'label' => '级别',
                'value' => function($data){
                    $level_arr = \app\models\DictData::getDickDataListByType('order_level');
                    return $level_arr[$data->level];
                }
            ],
            [
                'attribute' => 'content',
                'format' => 'html'
            ],
            ['attribute' => 'status','value'=> function($data){
                return $data->status == 1 ? '已完成' : '未完成';
            }],
        ],
    ]) ?>

    <div class="order-form">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'solve_user')->dropDownList(User::getUserList(), ['prompt'=>'请选择', 'style'=>'width:20%']) ?>

        <?= $form->field($model, 'classify')->dropDownList(DictData::getDickDataListByType('order_classify'),['prompt'=>'请选择归类', 'style'=>'width:20%']) ?>

        <?= $form->field($model, 'remark')->textarea() ?>

        <div class="form-group">
            <?= Html::submitButton('提交', ['class' => 'btn btn-primary']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>