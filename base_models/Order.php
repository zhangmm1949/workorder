<?php

namespace app\base_models;

use Yii;

/**
 * This is the model class for table "xm_order".
 *
 * @property int $id
 * @property string $order_sn 工单编号
 * @property int $present_user 发起人
 * @property int $present_time 发起时间
 * @property int $system 所属系统
 * @property int $level 级别
 * @property string $title 标题
 * @property string $content 内容
 * @property int $status 状态
 * @property int $solve_user 解决人
 * @property int $solve_time 解决时间
 * @property int $classify 问题归类（操作问题，系统bug，需求等）
 * @property string $tags 标签
 * @property int $update_time 最后更新时间
 * @property string $remark 备注
 * @property int $is_mail 是否已发送邮件
 */
class Order extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'xm_order';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['present_user', 'present_time', 'system', 'level', 'status', 'solve_user', 'solve_time', 'classify', 'update_time', 'is_mail'], 'integer'],
            [['content'], 'required'],
            [['content'], 'string'],
            [['order_sn'], 'string', 'max' => 30],
            [['title'], 'string', 'max' => 255],
            [['tags'], 'string', 'max' => 50],
            [['remark'], 'string', 'max' => 2000],
            [['order_sn'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
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
            'title' => '标题',
            'content' => '内容',
            'status' => '状态',
            'solve_user' => '解决人',
            'solve_time' => '解决时间',
            'classify' => '问题归类（操作问题，系统bug，需求等）',
            'tags' => '标签',
            'update_time' => '最后更新时间',
            'remark' => '备注',
            'is_mail' => '是否已发送邮件',
        ];
    }
}
