<?php

namespace app\base_models;

use Yii;

/**
 * This is the model class for table "order".
 *
 * @property integer $id
 * @property string $order_sn
 * @property integer $present_user
 * @property integer $present_time
 * @property integer $system
 * @property integer $level
 * @property string $title
 * @property string $content
 * @property integer $status
 * @property integer $solve_user
 * @property integer $solve_time
 * @property integer $classify
 * @property string $remark
 */
class Order extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'xm_order';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['present_user', 'content'], 'required'],
            [['present_user', 'present_time', 'system', 'level', 'status', 'solve_user', 'solve_time', 'classify'], 'integer'],
            [['content'], 'string'],
            [['order_sn'], 'string', 'max' => 30],
            [['title'], 'string', 'max' => 255],
            [['remark'], 'string', 'max' => 2000],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'order_sn' => '工单编号',
            'present_user' => '发起人',
            'present_time' => '发起时间',
            'system' => '所属系统',
            'level' => '级别',
            'title' => '概述',
            'content' => '内容',
            'status' => '状态',
            'solve_user' => '解决人',
            'solve_time' => '解决时间',
            'classify' => '问题归类',
            'remark' => '备注',
        ];
    }
}
